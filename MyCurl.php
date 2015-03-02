<?php
// +----------------------------------------------------------------------
// | CURL类
// +----------------------------------------------------------------------
// | Author: zhangqian
// +----------------------------------------------------------------------
// | Version: 1.1
// +----------------------------------------------------------------------
date_default_timezone_set("PRC");
class MyCurl{

	protected $_useragent = 'Mozilla/5.0 (Windows NT 6.1; rv:35.0) Gecko/20100101 Firefox/35.0'; 
    protected $_curl_handler;

    protected $_followlocation=true; 
    protected $_timeout = 30; //设置cURL允许执行的最长秒数。
    protected $_maxRedirects; //指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的。
    protected $_cookieFileLocation = './cookie.txt'; 

    protected $_postData=array(); 
    protected $_referer ="http://www.baidu.com"; 

    protected $_session; 
    protected $_webpage; 
    protected $_includeHeader; 
    protected $_noBody; 
    protected $_status; 
    protected $_binaryTransfer; //在启用CURLOPT_RETURNTRANSFER的时候，返回原生的（Raw）输出。

    public    $authentication = 0; 
    public    $auth_name      = ''; 
    public    $auth_pass      = ''; 
	

	function __construct(){		
	}

	//设置属性
	function __set($property_name, $value){
		$this->$property_name = $value;
	}

	private function init($url){
		$this->_curl_handler = curl_init($url);
		//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。	
		curl_setopt($this->_curl_handler,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($this->_curl_handler,CURLOPT_USERAGENT,$this->_useragent);
		// curl_setopt($this->_curl_handler,CURLOPT_REFERER,$this->_referer);

		curl_setopt($this->_curl_handler, CURLOPT_FOLLOWLOCATION,$this->_followlocation);
 		curl_setopt($this->_curl_handler, CURLOPT_AUTOREFERER,true);

		//设置cURL允许执行的最长秒数。
		curl_setopt($this->_curl_handler,CURLOPT_TIMEOUT,intval($this->_timeout));
	}

	//post方法
	function curl_post($url,$data=null){
		$this->init($url);
		$this->_postData = $data;
		curl_setopt($this->_curl_handler,CURLOPT_POST,true);
		curl_setopt($this->_curl_handler,CURLOPT_POSTFIELDS,http_build_query($this->_postData));
		$result = curl_exec($this->_curl_handler);
		$httpCode = curl_getinfo($this->_curl_handler, CURLINFO_HTTP_CODE);
		curl_close($this->_curl_handler);
		if($httpCode === 200){
			return array('stauts'=>0,'contents'=>$result);
		}else{
			return array('status'=>1,'contents'=>$httpCode);
		}
	}

	//get方法
	function curl_get($url,$data=null){
		$url = $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($data);
		$this->init($url);
		$result = curl_exec($this->_curl_handler);
		$httpCode = curl_getinfo($this->_curl_handler, CURLINFO_HTTP_CODE);
		curl_close($this->_curl_handler);
		if($httpCode === 200){
			return array('stauts'=>0,'contents'=>$result);
		}else{
			return array('status'=>1,'contents'=>$httpCode);
		}
	}

	function curl_cookie($url){
		$cookie_jar = tempnam('./tmp','JSESSIONID');
		$this->init($url);

		curl_setopt($this->_curl_handler, CURLOPT_COOKIEJAR, $cookie_jar);  
   		$filecontent=curl_exec($this->_curl_handler);  
   		curl_close($this->_curl_handler); 
   		
   		$this->init($url);
   		curl_setopt($this->_curl_handler, CURLOPT_COOKIEFILE, $cookie_jar);  
   		// curl_setopt($this->_curl_handler, CURLOPT_HEADER, false);//设定是否输出页面内容  

   		curl_setopt($this->_curl_handler,CURLOPT_POST,true);
   		$filecontent = curl_exec($this->_curl_handler);  

   		curl_close($this->_curl_handler);  
   		return $filecontent; 
	}

	//获取请求头信息
	function get_header($url){
		$this->init($url);
 		curl_setopt($this->_curl_handler, CURLOPT_HEADER, true);//启用时会将头文件的信息作为数据流输出。
 		curl_setopt($this->_curl_handler, CURLOPT_NOBODY,true);//启用时将不对HTML中的BODY部分进行输出。
	 	$header = curl_exec($this->_curl_handler);
	 	$httpCode = curl_getinfo($this->_curl_handler, CURLINFO_HTTP_CODE);
	 	curl_close($this->_curl_handler);
 		if($httpCode === 200){
			return array('stauts'=>0,'contents'=>$header);
		}else{
			return array('status'=>1,'contents'=>$httpCode);
		}
	}

}
?>
