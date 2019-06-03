<?php
/**
 *  FileName: CustomFollowUpRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 13:35
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class CustomFollowUpRequest extends BaseRequest
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
            'description' => 'required|string',
            'longitude' => 'string|max:50',
            'latitude' => 'string|max:50',
            'address' => 'string|max:255',
            'ids' => 'array'
        ];
    }

    public function attributes()
    {
        return [
            'custom_id' => '客户ID',
            'cid' => '跟进类型',
            'description' => '描述',
            'longitude' => '经度',
            'latitude' => '纬度',
            'address' => '地址',
            'ids' => '上传文件的ID组合'
        ];
    }
}