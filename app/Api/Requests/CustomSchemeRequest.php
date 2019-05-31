<?php
/**
 *  FileName: CustomSchemeRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/31
 *  Time: 9:21
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class CustomSchemeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'custom_id' => 'required|integer',
            'cid' => 'required|integer',
            'area' => 'string|max:20',
            'seller_name' => 'string|max:20',
            'project_name' => 'required|string|max:20',
            'custom_name' => 'string|max:20',
            'producer' => 'string|max:20',
            'brand' => 'string|max:20',
            'person_to_contact' => 'string|max:20',
            'phone' => 'string|max:20',
            'discount' => 'integer',
            'amount' => 'numeric',
            'tip' => 'string',
            'action_time' => 'date'
        ];
    }

    public function attributes()
    {
        return [
            'custom_id' => '客户ID',
            'cid' => '类型ID',
            'area' => '区域',
            'seller_name' => '销售经理名称',
            'project_name' => '项目工程名称',
            'custom_name' => '客户名称',
            'producer' => '方案制作人',
            'brand' => '品牌',
            'person_to_contact' => '联系人',
            'phone' => '联系电话',
            'discount' => '折扣',
            'amount' => '出货金额',
            'tip' => '描述',
            'action_time' => '执行时间'
        ];
    }
}