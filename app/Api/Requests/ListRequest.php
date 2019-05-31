<?php
/**
 *  FileName: ListRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 14:22
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class ListRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'scheme_id'=>'integer',
            'custom_id'=>'integer',
            'contacts_name' => 'string',
            'keyword' => 'string',
            'province' => 'string',
            'city' => 'string',
            'area' => 'string',
            'start_time' => 'date',
            'end_time' => 'date',
            'page' => 'integer',
            'size' => 'integer',
            'field' => 'string',
            'order' => 'string|in:asc,desc:'
        ];
    }

    public function attributes()
    {
        return [
            'scheme_id'=>'方案卡ID',
            'custom_id'=>'客户ID',
            'contacts_name' => "联系人姓名",
            "keyword" => "查询关键字",
            "province" => "省份",
            "city" => "城市",
            "area" => "区域",
            "start_time" => "开始时间",
            "end_time" => "结束时间",
            "page" => "页码",
            "size" => "获取条数",
            'field' => '排序字段',
            'order' => '排序方式'
        ];
    }
}