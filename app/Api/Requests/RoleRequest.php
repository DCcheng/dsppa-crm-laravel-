<?php
/**
 *  FileName: RoleRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 9:47
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class RoleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'access'=>'required|string',
            'menu'=>'required|string',
            'tip'=>'required|string',
            'data_authority' => 'required|string',
            'status' => 'required|integer'
        ];
    }

    public function attributes()
    {
        return [
            'title' => '角色名',
            'access' => '访问节点集',
            'menu' => '访问菜单集',
            'tip' => '描述',
            'data_authority' => '数据权限',
            'status' => '状态',
        ];
    }
}