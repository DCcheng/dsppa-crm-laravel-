<?php

namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class CustomRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:custom|max:50',
            'cid' => 'required|integer',
            'level' => 'required|string|max:20',
            'province' => 'required|string|max:20',
            'city' => 'required|string|max:20',
            'area' => 'required|string|max:20',
            'address' => 'required|string|max:100',
            'intention' => 'string|max:100',
            'keyword' => 'string|max:20',
            'source' => 'string|max:20',
            'fax' => 'string|max:20',
            'discount' => 'required|integer',
            'person_name'=>'required|array',
            'sex'=>'required|array',
            'phone'=>'required|array',
            'job'=>'required|array',
            'charge_status'=>'required|array',
        ];
    }

    public function attributes()
    {
        return [
            'identify' => '客户公司编号',
            'name' => '客户公司名称',
            'cid' => '客户分类ID',
            'level' => '客户等级',
            'province' => '所在省份',
            'city' => '所在城市',
            'area' => '所在管辖区域/县',
            'address' => '公司地址',
            'intention' => '意向',
            'keyword' => '检索关键字',
            'source' => '来源',
            'fax' => '传真',
            'longitude' => '经度',
            'latitude' => '纬度',
            'discount' => '折扣',
            'contacts_id'=>'联系人ID',
            'person_name' => '联系人姓名',
            'sex' => '性别',
            'phone' => '手机号码',
            'job' => '职位',
            'charge_status' => '负责状态'
        ];
    }
}
