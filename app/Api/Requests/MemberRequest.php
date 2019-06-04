<?php
/**
 *  FileName: MemberRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/4
 *  Time: 11:19
 */


namespace App\Api\Requests;


class MemberRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'truename' => 'required|unique:member',
            'role_id' => 'required|integer',
            'phone'=>'required|string|max:30',
            'attence_num'=>'required|string|max:30',
            'department_id'=>'required|integer',
        ];
    }

    public function attributes()
    {
        return [
            'truename' => '真实姓名',
            'role_id' => '角色ID',
            'phone' => '联系电话',
            'attence_num' => '考勤号',
            'department_id' => '部门ID'
        ];
    }
}