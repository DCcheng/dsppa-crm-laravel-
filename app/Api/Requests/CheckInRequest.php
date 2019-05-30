<?php
/**
 *  FileName: CheckInRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/30
 *  Time: 15:45
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class CheckInRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'address' => 'required|string|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'longitude' => '经度',
            'latitude' => '纬度',
            'address' => '地址',
        ];
    }
}