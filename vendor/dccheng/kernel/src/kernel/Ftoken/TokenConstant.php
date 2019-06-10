<?php
/**
 *  FileName: TokenConstant.php
 *  Description :
 *  Author: DC
 *  Date: 2019/4/8
 *  Time: 17:44
 */
namespace Kernel\Ftoken;

class TokenConstant
{
    const TOKEN_UPDATE_MESSAGE = " 已经对用户Token进行刷新";

    const TOKEN_LACK_CODE = 10000;
    const TOKEN_LACK_MESSAGE = "缺失Token参数，请重新核对参数完整性";

    const TOKEN_EXPIRE_CODE = 10001;
    const TOKEN_EXPIRE_MESSAGE = " Token已经过期，请重新申请Token";

    const TOKEN_INVALID_CODE = 10002;
    const TOKEN_INVALID_MESSAGE = "无效的Token，请重新核对令牌准确性";

    const PAYLOAD_NOT_ARRAY_CODE = 10003;
    const PAYLOAD_NOT_ARRAY_MESSAGE = "用户数据参数必须为数组类型";

    const PATH = "temp";
    const ENCRYPTION_METHOD = "sha256"; //令牌加密方法
}