<?php
/**
 *  FileName: CustomSchemeListRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/31
 *  Time: 15:59
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class CustomSchemeListRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'scheme_id' => 'required|integer',
            'product_name' => 'required|string|max:255',
            'product_model' => 'required|string|max:50',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'serial_num' => 'integer',
            'unit' => 'string|max:20',
            'tip' => 'string'
        ];
    }

    public function attributes()
    {
        return [
            'scheme_id' => '方案卡ID',
            'product_name' => '产品名',
            'product_model' => '型号',
            'price' => '单价',
            'quantity' => '数量',
            'serial_num' => '序号',
            'unit' => '单位',
            'tip' => '描述'
        ];
    }
}