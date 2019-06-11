namespace {{Namespace}};
use Kernel\Validation\Validation;
use Yii;
use {{modelNamespace}}\{{modelName}};
use common\controllers\ApiBaseController;
use common\vendor\db\VQuery;
use common\vendor\widgets\AjaxLinkPager;
use Exception;

class {{className}} extends ApiBaseController
{
    public function actionGetlist()
    {
        try{
            list($condition, $params, $arr, $page, $size) = {{modelName}}::getParams();
            $model = (new VQuery())->select("*")->from({{modelName}}::tableName())->where($condition, $params);
            $total = $model->count();
            $pageList = AjaxLinkPager::create($total, $size);
            $list = $model->offset(($page - 1) * $size)->limit($size)->orderBy("id desc")->all();
            foreach ($list as $key => $value) {
                $list[$key] = $value;
            }
            $arr['list'] = $list;
            $arr['pageList'] = $pageList;
            $arr['totalPage'] = ceil($total / $size);
            outputJson(1, "获取{{*}}列表", ["data" => $arr]);
        } catch (Exception $e) {
            outputJson($e->getCode(), $e->getMessage());
        }
    }

    public function actionAdd()
    {
        try {
            $model = {{modelName}}::addForData($_POST);
            outputJson(1, "添加{{*}}成功");
        } catch (Exception $e) {
            outputJson($e->getCode(), $e->getMessage());
        }
    }

    public function actionUpdate()
    {
        try {
            Validation::validate($_POST, [
            ["id", "required"],
            ["id", "number"]
            ]);
            {{modelName}}::updateForData($_POST["id"], $_POST);
            outputJson(1, "修改{{*}}成功");
        } catch (Exception $e) {
            outputJson($e->getCode(), $e->getMessage());
        }
    }

    public function actionDelete()
    {
        try {
            Validation::validate($_POST, [
            ["ids", "required"]
            ]);
            {{modelName}}::deleteForIds($_POST["ids"]);
            outputJson(1, "删除{{*}}成功");
        } catch (Exception $e) {
            outputJson($e->getCode(), $e->getMessage());
        }
    }
}
