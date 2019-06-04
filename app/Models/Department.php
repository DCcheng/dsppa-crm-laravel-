<?php
/**
 *  FileName: Department.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 13:40
 */


namespace App\Models;

use App\Api\Requests\ListRequest;
use App\Api\Utils\Response;
use App\Models\Model;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class Department extends Model
{
    protected $table = "department";

    public static function addAttributes($model)
    {
        $model->status = 1;
        $model->create_time = time();
        $model->delete_time = 0;
        return $model;
    }

    public static function deleteForIds($ids, $field = "id")
    {
        $bool = true;
        $msgArr = ["以下部门数据已经被引用，不允许删除："];
        $ids = is_array($ids) ? implode(",", $ids) : $ids;
        $departmentList = DB::table(DB::raw(self::getTableName() . " as a"))
            ->selectRaw("b.id,count(a.id) as total,b.title as title")
            ->join(DB::raw(self::getTableName() . " as b"), DB::raw("b.id"), "=", DB::raw("a.pid"))
            ->whereRaw("a.pid in ($ids)")
            ->groupBy(DB::raw("b.id,b.title"));

        $memberList = DB::table(DB::raw(self::getTableName() . " as a"))
            ->selectRaw("a.id,count(b.uid) as total,a.title as title")
            ->join(DB::raw(Member::getTableName() . " as b"), DB::raw("b.department_id"), "=", DB::raw("a.id"))
            ->whereRaw("a.id in ($ids)")
            ->groupBy(DB::raw("a.id,a.title"));

        $list = DB::table(DB::raw(self::getTableName() . " as a"))
            ->selectRaw("a.id,count(b.uid) as total,a.title as title")
            ->join(DB::raw(CustomFollowUpRecord::getTableName() . " as b"), DB::raw("b.department_id"), "=", DB::raw("a.id"))
            ->whereRaw("a.id in ($ids)")
            ->groupBy(DB::raw("a.id,a.title"))->union($departmentList)->union($memberList)->get();

        foreach ($list as $value) {
            $value = (array)$value;
            if ((int)$value["total"] > 0) {
                $bool = false;
                $msgArr[$value["id"]] = $value["id"] . " - " . $value["title"];
            }
        }

        if ($bool) {
            DB::beginTransaction();
            try {
                self::updateForIds($ids, ["delete_time" => time()], $field);
                DB::commit();
            } catch (Exception $exception) {
                DB::rollBack();
                throw new HttpResponseException(Response::fail($exception->getMessage()));
            }
        } else {
            throw new HttpResponseException(Response::fail(implode("\n", $msgArr)));
        }
    }

    public static function getTree($conditon = "pid = ? and status = 1 and delete_time = 0")
    {
        $arr = [array("id" => 0, "pid" => 0, "text" => "顶级部门", "value" => 0, "tree_title" => "顶级部门", "level" => 0)];
        return Department::getTreeList($conditon, [0], 0, $arr, $arr);
    }

    public static function setTreeData($value, $level)
    {
        if ($level > 0) {
            $treeTitle = str_repeat("&nbsp;&nbsp;", $level) . "└" . $value->title;
        } else {
            $treeTitle = $value->title;
        }
        return ["id" => $value->id, "pid" => $value->pid, "text" => $value->title, "value" => $value->id, "tree_title" => $treeTitle, "level" => $level];
    }

    public static function getParams(ListRequest $request, $size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request, $size);
        $condition = array("delete_time = 0");

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}