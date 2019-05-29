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
use App\Api\Utils\ThrowValidates;

class BaseRequest extends FormRequest
{
    use ThrowValidates;

    /**
     * 允许所有用户访问验证
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 当验证失败时，抛出验证不通过的原因
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::fail($this->formatError($validator)));
    }
}