<?php
/**
 *  FileName: IdsRequest.php  Description :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 10:37
 */


namespace App\Api\Requests;
use App\Api\Requests\BaseRequest;

class IdsRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'ids' => 'required|array',
        ];
    }

    public function attributes()
    {
        return [
            'ids' => 'ID组合',
        ];
    }
}