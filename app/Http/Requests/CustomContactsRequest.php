<?php
/**
 *  FileName: CustomContactsRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/30
 *  Time: 14:09
 */


namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class CustomContactsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'custom_id' => 'required|integer',
            'sex' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'email|max:20',
            'qq' => 'string|max:20',
            'wechat' => 'string|max:20',
            'is_person_in_charge' => 'integer|max:100',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '联系人名称',
            'custom_id' => '客户ID',
            'sex' => '性别',
            'phone' => '联系电话',
            'email' => '邮箱地址',
            'qq' => 'QQ',
            'wechat' => '微信',
            'is_person_in_charge' => '负责人状态'
        ];
    }
}