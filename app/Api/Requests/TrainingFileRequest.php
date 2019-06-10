<?php 
/**
 *  FileName: TrainingFileRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019-06-10
 *  Time: 02:08
 */


namespace App\Api\Requests;

use App\Api\Requests\BaseRequest;

class TrainingFileRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cid' => 'required|integer',
            'file' => 'required|file'
		];
    }

    public function attributes()
    {
        return [
			'cid' => '跟进类型',
			'file' => '上传文件'
		];
    }
}
?>