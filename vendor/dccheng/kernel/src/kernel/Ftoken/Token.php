<?php
/**
 *  FileName: Token.php
 *  Description : 主要用于Token的生成以及校验
 *                校验有两种形式
 *                需要客户端调整的参数有
 *                $param , 用于指向客户端传入参数变量字段
 *                $key , 用于TOKEN加密的私钥，有使用者自行定义
 *                $exp , 用于TOKEN管理的有效时间，默认为3600，单位“秒”
 *                $isValidateHeader , 配置是否用户请求头部校验，如果不使用头部校验，需要结合$param参数使用
 *                执行本类中的方法都会返回数组数据，其中包含了ret以及msg字段，ret如果为1代表执行操作成功，如果不为1则证明执行操作失败，并且会把执行中所遇到的异常返回使用者。
 *  Author: DC
 *  Date: 2019/4/8
 *  Time: 17:44
 */

namespace Kernel\Ftoken;;
use Kernel\Ftoken\TokenConstant;
use Exception;

class Token
{
    public $param = "Token";
    /**
     * @var string
     * TOKEN加密私钥
     */
    public $key = "gZ7v81FWTdWc0vS!";

    /**
     * @var int
     * TOKEN有效时间
     */
    public $exp = 5184000;

    /**
     * @var string
     */
    private $func = "chr";

    /**
     * [$file 临时文件存放路径]
     * @var [string]
     */
    private $file = "";

    /**
     *
     */
    private $isValidateHeader = true;


    private $userInfoArr = array();

    /**
     * [createToken 用于创建Token操作]
     * @param  [array] $payload [需要记录的信息，一般存储用户ID等]
     * @return [array]          [接口返回数据，ret：状态字段，0-失败，1-成功。msg：操作返回信息描述。data：包含的数据]
     */
    private function createToken($payload){
        if(is_array($payload) && count($payload) > 0) {
            $time = time() + $this->exp;
            $payload = json_encode(array_merge($payload,array("exp"=>$time)));
            $token = $this->getSign(base64_encode($payload));
            $this->file = __DIR__ . "/temp/" . $token;
            file_put_contents($this->file, $payload);
            return array($token,$time);
        }else{
            throw new Exception( TokenConstant::PAYLOAD_NOT_ARRAY_MESSAGE,TokenConstant::PAYLOAD_NOT_ARRAY_CODE);
        }
    }

    /**
     * [validateTokenReturnArray 用于验证Token的有效性]
     */
    private function validateToken(){
        if($this->isValidateHeader) {
            if (!isset($_SERVER["HTTP_AUTHORIZATION"])) {
                throw new Exception( TokenConstant::TOKEN_LACK_MESSAGE,TokenConstant::TOKEN_LACK_CODE);
            }
            $token = $_SERVER["HTTP_AUTHORIZATION"];
        }else{
            if(!isset($_GET[$this->param]) && !isset($_POST[$this->param])){
                throw new Exception( TokenConstant::TOKEN_LACK_MESSAGE,TokenConstant::TOKEN_LACK_CODE);
            }
            $token = isset($_GET[$this->param])?$_GET[$this->param]:$_POST[$this->param];
        }
        $this->file = __DIR__ . "/temp/" .$token;
        if(file_exists($this->file) && $token != ""){
            $data = json_decode(file_get_contents($this->file),true);
            if($data["exp"] > time()){
                $this->userInfoArr = $data;
                return $this->userInfoArr;
            }else{
                unlink($this->file);
                throw new Exception(TokenConstant::TOKEN_EXPIRE_MESSAGE,TokenConstant::TOKEN_EXPIRE_CODE);
            }
        }else{
            throw new Exception(TokenConstant::TOKEN_INVALID_MESSAGE,TokenConstant::TOKEN_INVALID_CODE);
        }
    }

    /**
     * [createToken 用于创建Token操作]
     * @param  [array] $payload [需要记录的信息，一般存储用户ID等]
     * @return [array]          [接口返回数据，ret：状态字段，0-失败，1-成功。msg：操作返回信息描述。data：包含的数据]
     */
    public function create($payload){
        return $this->createToken($payload);
    }

    public function validate(){
        return $this->validateToken();
    }

    /**
     * [invalidate 销毁令牌信息]
     * @return [array]        [接口返回数据，ret：状态字段，0-失败，1-成功。msg：操作返回信息描述。data：包含的数据]
     */
    public function invalidate(){
        $this->validateToken();
        unlink($this->file);
    }

    /**
     * [refresh 刷新令牌信息]
     * @param  [string] $token [用户访问令牌]
     * @return [array]        [接口返回数据，ret：状态字段，0-失败，1-成功。msg：操作返回信息描述。data：包含的数据]
     */
    public function refresh(){
        unlink($this->file);
        return $this->createToken($this->userInfoArr);
    }

    /**
     * [getSign description]
     * @param  [string] $str [传入字符串]
     * @return [string]      [加密令牌]
     */
    private function getSign($str){
        $c = $this->func;
        $ss = $c(104).$c(97).$c(115).$c(104);
        $sign = base64_encode($ss(TokenConstant::ENCRYPTION_METHOD,$str.$this->key));
        return $sign;
    }
}