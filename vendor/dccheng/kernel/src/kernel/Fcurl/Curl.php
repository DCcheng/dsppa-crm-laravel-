<?php
namespace Kernel\FCurl;
use Kernel\FCurl\FCurlConstant;
class Curl
	{
        private $ssl = false;
		//设置请求头
        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }

		//触发get请求
		public function get($url,$header = array()){
			return $this->send($url,array(),"get",$header);
		}

		//触发post请求
		public function post($url,$data = array(),$header = array()){
			return $this->send($url,$data,"post",$header);
		}

		//发送模拟数据包
		private function send($url,$data = array(),$method = "get",$header = array()){

			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $url );
			
			if($this->ssl){
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    		}
		    
		    // curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		    curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		    curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式

			if(is_array($header)&&count($header) > 0){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //设置头信息的地方 
			}

			if($method == "post"){
				curl_setopt($ch, CURLOPT_POST, 1); // 发送一个常规的Post请求
				if(is_array($data)&&count($data) > 0){
					curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query($data) );
				}
			}
			$result = curl_exec ( $ch );
			return $result;
		}
	}
?>