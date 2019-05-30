<?php
/**
 *  FileName: ListRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 14:22
 */


namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class ListRequest extends BaseRequest
{
    public function rules()
    {
        return [
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