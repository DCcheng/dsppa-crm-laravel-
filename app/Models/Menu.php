<?php
/**
 *  FileName: Menu.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 8:55
 */


namespace App\Models;


use App\Api\Requests\ListRequest;
use App\Api\Utils\Response;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    protected $table = "menu";

    public static function deleteForIds($ids, $field = "id")
    {
        $bool = true;
        $msgArr = ["以下菜单数据已经被引用，不允许删除："];
        $ids = is_array($ids) ? implode(",", $ids) : $ids;
        $list = DB::table(DB::raw(self::getTableName() . " as a"))
            ->selectRaw("b.id,count(a.id) as total,b.title as title")
            ->join(DB::raw(self::getTableName() . " as b"), DB::raw("b.id"), "=", DB::raw("a.pid"))
            ->whereRaw("a.pid in ($ids)")
            ->groupBy(DB::raw("b.id,b.title"))->get()->toArray();

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
        $arr = [array("id" => 0, "pid" => 0, "value" => 0, "text" => "顶级菜单", "icon" => "fa fa-code", "type" => "", "url" => "", "opened" => true, "tree_title" => "顶级菜单", "level" => 0)];
        return Menu::getTreeList($conditon, [0], 0, $arr, $arr);
    }

    public static function setTreeData($value, $level)
    {
        if ($level > 0) {
            $treeTitle = str_repeat("&nbsp;&nbsp;", $level) . "└" . $value->title;
        } else {
            $treeTitle = $value->title;
        }
        return ["id" => $value->id, "pid" => $value->pid, "text" => $value->title, "value" => $value->id, "icon" => $value->icon, "type" => $value->type, "url" => $value->url, "opened" => $level > 2 ? false : true, "tree_title" => $treeTitle, "level" => $level];
    }

    public static function getParams(ListRequest $request, $size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request, $size);
        $condition = array("delete_time = 0", "id > 100");

        $keyword = $request->get('keyword', "");
        if ($keyword != "") {
            $condition[] = "title like ?";
            $params[] = "%" . trim($keyword) . "%";
        }

        $type = $request->get("type", "all");
        if ($type != "all") {
            $condition[] = "type = ?";
            $params[] = $type;
        }

        $pid = $request->get("pid", "all");
        if ($pid != "all") {
            $condition[] = "pid = ?";
            $params[] = $pid;
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}
