<?php
/**
 *  FileName: AccessRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 16:45
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class AccessRequest extends BaseRequest
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
            'title' => 'required|string|max:255',
            'controller'=>'required|string|max:50',
            'method'=>'required|string|max:50',
            'tip'=>'required|string',
            'sort' => 'required|integer',
            'status' => 'required|integer'
        ];
    }

    public function attributes()
    {
        return [
            'pid' => '父级分类ID',
            'title' => '分类标题',
            'controller' => '控制器',
            'method' => '执行方法',
            'tip' => '提示',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}