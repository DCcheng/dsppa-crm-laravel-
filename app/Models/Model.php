<?php
/**
 *  FileName: Model.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/22
 *  Time: 13:50
 */


namespace App\Models;

use App\Api\Utils\Constant;
use App\Api\Utils\Response;
use App\Http\Requests\ListRequest;
use App\Models\Category;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public static $model;
    public $timestamps = false;

    /**
     *
     */
    public static function init()
    {
        self::$model = Container::getInstance()->make(static::class);
    }

    /**
     * 获取数据表名
     * @return string
     */
    public static function getTableName()
    {
        self::init();
        return DB::connection()->getTablePrefix() . self::$model->getTable();
    }

    /**
     * 用于新增数据是，初始化对象属性
     * @param $model
     * @return mixed
     */
    public static function addAttributes($model)
    {
        return $model;
    }

    /**
     * 添加数据的基础方法
     * @param $data
     * @return Model
     */
    public static function addForData($data)
    {
        $model = new static();
        try {
            $model["attributes"] = $data;
            $model = static::addAttributes($model);
            $model->save();
            return $model;
        } catch (Exception $e) {
            if (config("app.debug")) {
                throw new HttpResponseException(Response::fail($e->getMessage()));
            } else {
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_ACTION_FAIL_CODE . " - " . Constant::SYSTEM_DATA_ACTION_FAIL_MESSAGE));
            }
        }
    }

    /**
     * 用于更新对象属性
     * @param $model
     * @return mixed
     */
    public static function editAttributes($model)
    {
        return $model;
    }

    /**
     * 更新数据的基础方法
     * @param $id
     * @param $data
     * @return Model|Model[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public static function updateForData($id, $data)
    {
        $model = static::find($id);
        if ($model) {
            try {
                $model["attributes"] = $data;
                $model = static::editAttributes($model);
                $model->save();
                return $model;
            } catch (Exception $e) {
                if (config("app.debug")) {
                    throw new HttpResponseException(Response::fail($e->getMessage()));
                } else {
                    throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_ACTION_FAIL_CODE . " - " . Constant::SYSTEM_DATA_ACTION_FAIL_MESSAGE));
                }
            }
        } else {
            throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_EXCEPTION_CODE . " - " . Constant::SYSTEM_DATA_EXCEPTION_MESSAGE));
        }
    }

    /**
     * 根据ID批量更新数据
     * @param $ids
     * @param array $updateData
     * @param string $field
     */
    public static function updateForIds($ids, $updateData = [], $field = "id")
    {
        $ids = is_array($ids) ? implode(",", $ids) : $ids;
        try {
            static::whereRaw( $field . " in (" . $ids . ")")->update($updateData);
        } catch (Exception $e) {
            if (config("app.debug")) {
                throw new HttpResponseException(Response::fail($e->getMessage()));
            } else {
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_ACTION_FAIL_CODE . " - " . Constant::SYSTEM_DATA_ACTION_FAIL_MESSAGE));
            }
        }
    }

    /**
     * 根据ID批量删除数据
     * @param $ids
     * @param string $field
     */
    public static function deleteForIds($ids, $field = "id")
    {
        $ids = is_array($ids) ? implode(",", $ids) : $ids;
        try {
            static::deleteDataForIds($field . " in (" . $ids . ")");
        } catch (Exception $e) {
            if (config("app.debug")) {
                throw new HttpResponseException(Response::fail($e->getMessage()));
            } else {
                throw new HttpResponseException(Response::fail(Constant::SYSTEM_DATA_ACTION_FAIL_CODE . " - " . Constant::SYSTEM_DATA_ACTION_FAIL_MESSAGE));
            }
        }
    }

    /**
     * 根据条件软删除数据，如果需要硬删除则需要继承本类把该方法重写
     * @param $condition
     * @throws Exception
     */
    public static function deleteDataForIds($condition)
    {
        static::whereRaw($condition)->update(["delete_time" => time()]);
    }

    /**
     * 获取参数，初始化查询条件的基础方法，如需特异性操作，需要继承本类把方法重写
     * @param Request $request
     * @param int $size
     * @return array
     */
    public static function getParams(ListRequest $request, $size = 15)
    {
        $page = request()->get("page", 1);
        $size = request()->get("size", $size);
        $arr = $condition = $params = [];
        $condition = implode(" and ", $condition);
        return [$condition, $params, $arr, $page, $size];
    }

    /**
     * @param $pid
     * @param bool $is_value
     * @return array
     */
    public static function getCategoryForPidList($pid,$is_value = false)
    {
        $list = array();
        $categoryList = Category::getChildrenList("pid = :pid  and status = 1 and id > 100 and delete_time = 0", [":pid" => $pid]);
        if($is_value) {
            foreach ($categoryList as $key => $value) {
                $value = (array)$value;
                $list[] = array("text" => $value["title"], "value" => $value["value"], "tree_title" => $value["title"], "content" => $value["title"]);
            }
        }else{
            foreach ($categoryList as $key => $value) {
                $value = (array)$value;
                $list[] = array("text" => $value["title"], "value" => $value["id"], "tree_title" => $value["title"], "content" => $value["title"]);
            }
        }
        return $list;
    }

//    public static function getTreeList($condition, $params, $level, $treeArr,$treeArrForId)
//    {
//        $arr = array();
//        $list = static::find()->where($condition, $params)->orderBy("sort,id asc")->all();
//        foreach ($list as $key => $value) {
//            $v = static::setTreeData($value, $level);
//            $treeArrForId[$v["id"]] = $v;
//            $treeArr[] = $v;
//            list($treeArr, $treeArrForId,$childrenList) = static::getTreeList($condition, [":pid" => $v["id"]], $level + 1, $treeArr,$treeArrForId);
//            $v["children"] = $childrenList;
//            if (count($v["children"]) == 0) {
//                $v["children"] = false;
//            }
//            $arr[] = $v;
//        }
//        return [$treeArr,$treeArrForId,$arr];
//    }
//
//    public static function setTreeData($value, $level)
//    {
//        if ($level > 0) {
//            $treeTitle = str_repeat("  ", $level) . $value->title;
//        } else {
//            $treeTitle = $value->title;
//        }
//        return ["id" => $value->id, "pid" => $value->pid, "text" => $value->title, "tree_title" => $treeTitle, "level" => $level];
//    }

    public static function getChildrenList($condition = "pid = :pid  and status = 1", $params = array())
    {
        $list = DB::table(DB::raw(static::getTableName()))->whereRaw($condition, $params)->orderByRaw("sort,id")->get();
        return $list;
    }
}