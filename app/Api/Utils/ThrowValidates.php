<?php
/**
 *  FileName: ThrowValidatesDescription :
 *  Author: DC
 *  Date: 2019/5/29
 *  Time: 9:56
 */


namespace App\Api\Utils;
use Illuminate\Contracts\Validation\Validator;

trait ThrowValidates
{
    public function formatError(Validator $validator){
        $errorArr = [];
        $i = 1;
        foreach ($validator->errors()->getMessages() as $value){
            foreach ($value as $v){
                $errorArr[] = $i.".".$v;
                $i++;
            }
        }
        return implode("\n",$errorArr);
    }
}