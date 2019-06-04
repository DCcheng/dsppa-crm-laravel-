<?php
/**
 *  FileName: MenuRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 9:28
 */


namespace App\Api\Requests;
use App\Api\Requests\BaseRequest;

class MenuRequest extends BaseRequest
{
    protected $table = "menu";

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
            'url'=>'required|string|max:255',
            'icon'=>'string|max:255',
            'tip'=>'string|max:255',
            'sort' => 'required|integer',
            'status' => 'required|integer'
        ];
    }

    public function attributes()
    {
        return [
            'pid' => '父级菜单ID',
            'title' => '菜单标题',
            'url' => '访问路径',
            'icon' => '标签',
            'tip' => '提示',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}