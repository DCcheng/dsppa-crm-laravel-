<?php
/**
 *  FileName: Constant.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/24
 *  Time: 15:51
 */


namespace App\Api\Utils;


class Constant
{
    const CATEGORY_FOR_CUSTOM_TRADE = 1;  //客户行业分类ID
    const CATEGORY_FOR_CUSTOM_LEVEL = 2;  //客户等级分类ID
    const CATEGORY_FOR_CUSTOM_FOLLOWUP = 3;  //客户跟进分类ID
    const CATEGORY_FOR_TRAINING_FILE = 4;  //知识库分类ID

    const CATEGORY_FOR_ = 100;//HTTP请求头分类ID

    const SYSTEM_DATA_EXCEPTION_CODE = "100000";
    const SYSTEM_DATA_EXCEPTION_MESSAGE = "数据异常";

    const SYSTEM_DATA_ACTION_FAIL_CODE = "100001";
    const SYSTEM_DATA_ACTION_FAIL_MESSAGE = "数据操作失败";

    const SYSTEM_DATA_VERIFY_FAIL_CODE = "100002";
    const SYSTEM_DATA_VERIFY_FAIL_MESSAGE = "数据校验失败";

    const SYSTEM_DATA_LACK_CODE = "100003";
    const SYSTEM_DATA_LACK_MESSAGE = "缺少必要的请求数据";

    const SYSTEM_LOGIN_PASSWORD_FAIL_CODE = "100004";
    const SYSTEM_LOGIN_PASSWORD_FAIL_MESSAGE = "用户登录密码错误，请重新填写";

    const SYSTEM_HAVE_CHILDRENS_CODE = "100005";
    const SYSTEM_HAVE_CHILDRENS_MESSAGE = "该数据拥有下级分类，不允许更改状态/分类属性，也不允许进行删除，否则会导致数据引用不完整";

    const SYSTEM_NOT_ALLOW_SELF_AS_PARENT_CODE = "100006";
    const SYSTEM_NOT_ALLOW_SELF_AS_PARENT_MESSAGE = "不允许以自身作为父级分类";

    const SYSTEM_NOT_ALLOW_DELETE_ALL_DATA_CODE = "100007";
    const SYSTEM_NOT_ALLOW_DELETE_ALL_DATA_MESSAGE = "不允许删除所有数据，至少也要保留一条";

    const SYSTEM_NOT_AJAX_CODE = "400000";
    const SYSTEM_NOT_AJAX_MESSAGE = "必须使用异步的方式发起请求";
    const SYSTEM_NO_ACTION_AUTHORITY_CODE = "400001";
    const SYSTEM_NO_ACTION_AUTHORITY_MESSAGE = "您没有相关功能的操作权限";
}
