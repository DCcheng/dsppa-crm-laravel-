<?php
/**
 *  FileName: DepartmentRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 15:04
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class DepartmentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pid' => 'required|integer',
            'title' => 'required|string|max:50',
            'sort'=>'required|integer'
        ];
    }

    public function attributes()
    {
        return [
            'pid' => '父级部门ID',
            'title' => '部门名',
            'sort' => '排序号'
        ];
    }
}