<?php
/**
 *  FileName: Uploads.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 15:02
 */


namespace App\Models;


use App\Api\Utils\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Kernel\Kernel;

class Uploads extends Model
{
    protected $table = "uploads";

    //上传文件
    public static function uploadFile(Request $request, $field = "file", $convert = true)
    {
        if ($request->isMethod('POST')) {

            $fileCharater = $request->file($field);

            if ($fileCharater->isValid()) { //括号里面的是必须加的哦
                //如果括号里面的不加上的话，下面的方法也无法调用的

                $size = $fileCharater->getSize();
                if ($size > (config("webconfig._uploadSize") * 1024)) {
                    throw new Exception("上传文件最大只允许为" . (config("webconfig._uploadSize") / 1024) . "M");
                }

                $name = $fileCharater->getClientOriginalName();
                //获取文件的扩展名
                $ext = strtolower($fileCharater->getClientOriginalExtension());
                $type = "document";
                $bool = true;
                foreach (config("webconfig._uploadArr") as $key => $value) {
                    if (in_array($ext, $value)) {
                        $bool = false;
                        $type = $key;
                        break;
                    }
                }
                if ($bool) {
                    throw new Exception("上传文件格式错误");
                }
                $path = "uploads/" . $type . "/" . date('Ymd');
                $filename = $fileCharater->store($path);

                $userInfo = config("webconfig.userInfo");
                //新增上传文件记录
                $model = self::addForData([
                    "uid" => $userInfo["uid"],
                    "name" => $name,
                    "filename" => $filename,
                    "convername" => $type == "document" ? explode(".", $filename)[0] . ".pdf" : $filename,
                    "type" => $type,
                    "size" => sprintf("%.1f", $size / 1048576),
                    "status" => 0,
                    "create_time" => time()
                ]);
                if ($type == "document" && $ext != "pdf" && $convert) {
                    $path = storage_path("app");
                    Kernel::pdf()->execute($path."/".$model->filename,$path."/".$model->convername);
//                    exec("PATH=/usr/bin unoconv -f pdf " . $path . $model->filename . " > /dev/null &2>1&");
                }
                return [$model->id, $model->filename];
            } else {
                throw new Exception(Constant::SYSTEM_DATA_LACK_CODE . " - " . Constant::SYSTEM_DATA_LACK_MESSAGE);
            }
        } else {
            throw new Exception("请求方法有误");
        }
    }

    //使用文件
    public static function useFile($id)
    {
        self::updateForData($id, ["status" => 1]);
    }

    //清理文件
    public static function cleanFile($condition = "status = 0")
    {
        $list = DB::table(DB::raw(self::getTableName()))->whereRaw($condition)->get();
        $fileArr = [];
        foreach ($list as $key => $value) {
            $fileArr[] = $value->filename;
            $fileArr[] = $value->convername;
        }
        if (!self::whereRaw($condition)->update(["status" => -1])) {
            throw new Exception(Constant::SYSTEM_DATA_ACTION_FAIL_CODE . " - " . Constant::SYSTEM_DATA_ACTION_FAIL_MESSAGE);
        }
        Storage::delete($fileArr);
    }
}
