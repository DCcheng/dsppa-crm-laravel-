<?php
/**
 *  FileName: Category.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 16:55
 */


namespace App\Models;
use App\Api\Requests\ListRequest;
use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Models\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Exception;

class Category extends Model
{
    protected $table = "category";

    public static function addAttributes($model)
    {
        $model->create_time = time();
        $model->delete_time = 0;
        return $model;
    }

    public static function deleteForIds($ids, $field = "id")
    {
        $bool = true;
        $msgArr = ["以下分类数据已经被引用，不允许删除："];
        $ids = is_array($ids) ? implode(",", $ids) : $ids;
        $categoryUseList = DB::table(DB::raw(self::getTableName() . " as a"))
            ->selectRaw("a.id,count(b.id) as total,a.title as title")
            ->join(DB::raw(Category::getTableName()." as b"),DB::raw("b.pid"),"=",DB::raw("a.id"))
            ->whereRaw("a.id in ($ids)")
            ->groupBy(DB::raw("a.id,a.title"));

        $customUseListForClass = DB::table(DB::raw(self::getTableName() . " as a"))
            ->selectRaw("a.id,count(b.id) as total,a.title as title")
            ->join(DB::raw(Custom::getTableName()." as b"),DB::raw("b.cid"),"=",DB::raw("a.id"))
            ->whereRaw("a.id in ($ids)")
            ->groupBy(DB::raw("a.id,a.title"));

        $customFollowUpRecordForClass = DB::table(DB::raw(self::getTableName() . " as a"))
            ->selectRaw("a.id,count(b.id) as total,a.title as title")
            ->join(DB::raw(CustomFollowUpRecord::getTableName()." as b"),DB::raw("b.cid"),"=",DB::raw("a.id"))
            ->whereRaw("a.id in ($ids)")
            ->groupByRaw(DB::raw("a.id,a.title"));

        $list = DB::table(DB::raw(self::getTableName() . " as a"))
            ->selectRaw("a.id,count(b.id) as total,a.title as title")
            ->join(DB::raw(CustomFollowUpFile::getTableName()." as b"),DB::raw("b.cid"),"=",DB::raw("a.id"))
            ->whereRaw("a.id in ($ids)")
            ->groupBy(DB::raw("a.id,a.title"))->union($categoryUseList)->union($customUseListForClass)->union($customFollowUpRecordForClass)->get()->toArray();

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
                self::updateForIds($ids,["delete_time" => time()],$field);
                DB::commit();
            } catch (Exception $exception) {
                DB::rollBack();
                throw new HttpResponseException(Response::fail($exception->getMessage()));
            }
        } else {
            throw new HttpResponseException(Response::fail(implode("\n", $msgArr)));
        }
    }

    public static function getTree($conditon = "pid = :pid and status = 1 and delete_time = 0")
    {
        $arr = [array("id" => 0, "pid" => 0, "text" => "顶级分类", "value" => "0", "tree_title" => "顶级分类", "level" => 0)];
        return Category::getTreeList($conditon, [":pid" => 0], 0, $arr,$arr);
    }

    public static function setTreeData($value, $level)
    {
        if ($level > 0) {
            $treeTitle = str_repeat("&nbsp;&nbsp;", $level) ."└". $value->title;
        } else {
            $treeTitle = $value->title;
        }
        return ["id" => $value->id, "pid" => $value->pid, "text" => $value->title, "value" => $value->id, "tree_title" => $treeTitle, "level" => $level];
    }

    public static function getParams(ListRequest $request,$size = 15)
    {
        list(, $params, $arr, $page, $size) = parent::getParams($request,$size);
        $condition = array("delete_time = 0","id > 100");

        $type = $request->get("type","");
        if($type != ""){
            switch ($type){
                case "custom_level": //客户等级
                    $condition[] = "pid = ".Constant::CATEGORY_FOR_CUSTOM_LEVEL;
                    break;
                case "custom_trade": //客户行业分类
                    $condition[] = "pid = ".Constant::CATEGORY_FOR_CUSTOM_TRADE;
                    break;
                case "custom_followup": //客户跟进分类
                    $condition[] = "pid = ".Constant::CATEGORY_FOR_CUSTOM_FOLLOWUP;
                    break;
                default:
                    throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
                    break;
            }
        }

        $condition = implode(" and ", $condition);
        return array($condition, $params, $arr, $page, $size);
    }
}