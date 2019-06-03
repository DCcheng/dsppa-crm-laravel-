<?php
/**
 *  FileName: CategoryRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/3
 *  Time: 16:08
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class CategoryRequest extends BaseRequest
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
            'sort' => 'required|integer',
            'status' => 'required|integer'
        ];
    }

    public function attributes()
    {
        return [
            'pid' => '父级分类ID',
            'title' => '分类标题',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}