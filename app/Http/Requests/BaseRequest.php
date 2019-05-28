<?php
/**
 *  FileName: BaseRequest.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/28
 *  Time: 10:33
 */


namespace App\Http\Requests;

use App\Api\Utils\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::fail($validator->errors()));
    }
}