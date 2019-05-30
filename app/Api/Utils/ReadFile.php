<?php
/**
 *  FileName: ReadFile.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 16:34
 */


namespace App\Api\Utils;

use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Model;

class ReadFile
{
    const ErrorCode = 0;
    const ErrorFileMessage = "所读取的文件不符合格式";
    const ErrorTypeMessage = "读取类型错误";
    const ErrorTemplateMessage = "模板数据并不是最新版本，请重新下载最新的数据模板";
    const ErrorNullMessage = "所读取的数据为空";

    public static function readExcel($filename,$type)
    {
        $filenameArr = explode(".",$filename);
        $extension = strtolower(end($filenameArr));
        if(!in_array($extension,["xls","xlsx","csv"])){
            throw new Exception(self::ErrorFileMessage);
        }

        switch ($type){
            case "custom":
                $f = "readCustomInfo";
                $template = storage_path('app') . "/uploads/CustomTemplate.xlsx";
                break;
            case "scheme-list":
                $f = "readSchemeListInfo";
                $template = storage_path('app') . "/uploads/CustomSchemeListTemplate.xlsx";
                break;
            default:
                throw new Exception(self::ErrorTypeMessage);
                break;
        }

        //校验模板文件是否最新版本
        $spreadsheet = IOFactory::load($filename);
        $datasheet = $spreadsheet->getSheet(1);
        $tempspreadsheet = IOFactory::load($template)->getSheet(1);
        if($tempspreadsheet->getCell("B1")->getValue() != $datasheet->getCell("B1")->getValue() || $tempspreadsheet->getCell("B2")->getValue() != $datasheet->getCell("B2")->getValue()){
            throw new Exception(self::ErrorTemplateMessage,self::ErrorCode);
        }
        $worksheet = $spreadsheet->getSheet(0);
        $highestRow = $worksheet->getHighestRow(); // 总行数
        return ReadFile::$f($worksheet,$highestRow);
    }

    public static function readCustomInfo($worksheet,$highestRow)
    {
        //获取分类信息
        //获取客户等级信息
        $categroyList = Model::getCategoryForPidList(Constant::CATEGORY_FOR_CUSTOM_TRADE);
        $cids = [];
        foreach ($categroyList as $key=>$value){
            $cids[$value["text"]] = $value["value"];
        }
        $data = [];
        try {
            for ($row = 3; $row <= $highestRow; $row++) {
                $arr["name"] = (string)$worksheet->getCell("A" . $row)->getValue();
                if($arr["name"] == ""){
                    break;
                }
                $arr["cid"] = $cids[trim((string)$worksheet->getCell("B" . $row)->getValue())];
                $arr["level"] = (string)$worksheet->getCell("C" . $row)->getValue();
                $arr["province"] = (string)$worksheet->getCell("D" . $row)->getValue();
                $arr["city"] = (string)$worksheet->getCell("E" . $row)->getValue();
                $arr["area"] = (string)$worksheet->getCell("F" . $row)->getValue();
                $arr["address"] = (string)$worksheet->getCell("G" . $row)->getValue();
                $arr["person_name"] = (string)$worksheet->getCell("H" . $row)->getValue();
                $arr["sex"] = (string)$worksheet->getCell("I" . $row)->getValue();
                $arr["phone"] = (string)$worksheet->getCell("J" . $row)->getValue();
                $arr["intention"] = (string)$worksheet->getCell("K" . $row)->getValue();
                $arr["keyword"] = (string)$worksheet->getCell("L" . $row)->getValue();
                $arr["source"] = (string)$worksheet->getCell("M" . $row)->getValue();
                $arr["fax"] = (string)$worksheet->getCell("N" . $row)->getValue();
                $arr["discount"] = (int)$worksheet->getCell("O" . $row)->getValue();
                $data[] = $arr;
            }
            if (count($data) == 0) {
                throw new Exception(self::ErrorNullMessage);
            }
            return $data;
        }catch (Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public static function readSchemeListInfo($worksheet,$highestRow)
    {
        $data = [];
        try {
            for ($row = 3; $row <= $highestRow; $row++) {
                $arr["product_name"] = (string)$worksheet->getCell("A" . $row)->getValue();
                if($arr["product_name"] == ""){
                    break;
                }
                $arr["intention"] = (string)$worksheet->getCell("B" . $row)->getValue();
                $arr["product_model"] = (string)$worksheet->getCell("C" . $row)->getValue();
                $arr["price"] = (string)$worksheet->getCell("D" . $row)->getValue();
                $arr["quantity"] = (string)$worksheet->getCell("E" . $row)->getValue();
                $arr["unit"] = (string)$worksheet->getCell("F" . $row)->getValue();
                $arr["total_price"] = (string)$worksheet->getCell("G" . $row)->getValue();
                $arr["tip"] = (string)$worksheet->getCell("H" . $row)->getValue();
                $data[] = $arr;
            }
            if (count($data) == 0) {
                throw new Exception(self::ErrorNullMessage);
            }
            return $data;
        }catch (Exception $e){
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}