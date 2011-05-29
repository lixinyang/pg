<?php

class QqConnect {

	/**
	 * 
	 * 这是oauth的第一步，获得鉴权地址和request_token。（ps：本sdk的oauth过程就两步，第一步和最后一步）
	 * 返回值为：	array(
	 *		'authorize_url'=>'http://openapi.qzone.qq.com/oauth/qzoneoauth_authorize?oauth_consumer_key=.......',
	 *		'oauth_request_token'=>'3612348443550269993',
	 *		'oauth_request_token_secret'=>'WS47JCvy6mBQ637Q'
	 *	);
	 * 返回错误：
	 * 如果oauth_token为空则调用发生了错误，需要调用者处理
	 * 返回值的使用：
	 * 1）让用户打开authorize_url去授权
	 * 2）程序记录下来oauth_token、oauth_token_secret，后面获得access_token会需要这两个参数。（可以根据自己喜好记录在session、db、memcache等之中）
	 * @param string $appid , 在http://connect.opensns.qq.com/my申请的app id
	 * @param string $appkey , 在http://connect.opensns.qq.com/my申请的app key
	 * @param string $callback , 处理用户授权后QQ把用户送回到的地址
	 */
	function get_authorize_url($appid, $appkey, $callback) {
	    //跳转到QQ登录页的接口地址, 不要更改!!
	    $authorize_url = "http://openapi.qzone.qq.com/oauth/qzoneoauth_authorize?oauth_consumer_key=$appid&";
	
	    //调用get_request_token接口获取未授权的临时token
	    $result = array();
	    $request_token = $this->get_request_token($appid, $appkey);
	    parse_str($request_token, $result);
	
	    ////构造请求URL
	    $authorize_url .= "oauth_token=".$result["oauth_token"]."&oauth_callback=".rawurlencode($callback);
		
		$ret = array(
			'authorize_url'=>$authorize_url,
			'oauth_request_token'=>$result["oauth_token"],
			'oauth_request_token_secret'=>$result["oauth_token_secret"]
		);
		return $ret;
	}
	
	/**
	 * 
	 * oauth鉴权过程的最后一步，获得用户的openid和access_token
	 * 返回值为：	array(
	 *		'openid'=>'8FDD2F094ED59B6E3F59F72359648D2F',
	 *		'oauth_access_token'=>'13410486451735847996',
	 *		'oauth_access_token_secret'=>'WS47JCvy6mBQ637Q',
	 *		'error_code'
	 *	);
	 * 返回错误：
	 * 如果error_code有值则说明发生了错误，error_code的说明见：http://wiki.opensns.qq.com/wiki/%E3%80%90QQ%E7%99%BB%E5%BD%95%E3%80%91%E5%85%AC%E5%85%B1%E8%BF%94%E5%9B%9E%E7%A0%81%E8%AF%B4%E6%98%8E
	 * 返回值的使用：
	 * 1）在callback页面：$access_token = get_access_token($appid, $appkey, $request_token, $request_token_secret,  $_REQUEST["oauth_vericode"])
	 * 2）把$access_token中的openid、oauth_access_token、oauth_access_token_secret保存到db等地方，后面调用其他接口的时候使用。
	 * @param string $appid , 在http://connect.opensns.qq.com/my申请的app id
	 * @param string $appkey , 在http://connect.opensns.qq.com/my申请的app key
	 * @param string $request_token , 上一步get_authorize_url()中返回的oauth_request_token
	 * @param string $request_token_secret , 上一步get_authorize_url()中返回的oauth_request_token_secret
	 * @param string $vericode , QQ把用户送回到你网站的callback页面时在url带回的参数， $_REQUEST["oauth_vericode"]
	 */
	function get_access_token($appid, $appkey, $request_token, $request_token_secret, $vericode)
	{
	    //请求具有Qzone访问权限的access_token的接口地址, 不要更改!!
	    $url    = "http://openapi.qzone.qq.com/oauth/qzoneoauth_access_token?";
	    
	    //生成oauth_signature签名值。签名值生成方法详见（http://wiki.opensns.qq.com/wiki/【QQ登录】签名参数oauth_signature的说明）
	    //（1） 构造生成签名值的源串（HTTP请求方式 & urlencode(uri) & urlencode(a=x&b=y&...)）
		$sigstr = "GET"."&".rawurlencode("http://openapi.qzone.qq.com/oauth/qzoneoauth_access_token")."&";
	
	    //必要参数，不要随便更改!!
	    $params = array();
	    $params["oauth_version"]          = "1.0";
	    $params["oauth_signature_method"] = "HMAC-SHA1";
	    $params["oauth_timestamp"]        = time();
	    $params["oauth_nonce"]            = mt_rand();
	    $params["oauth_consumer_key"]     = $appid;
	    $params["oauth_token"]            = $request_token;
	    $params["oauth_vericode"]         = $vericode;
	
	    //对参数按照字母升序做序列化
	    $normalized_str = $this->get_normalized_string($params);
	    $sigstr        .= rawurlencode($normalized_str);
	
	    //echo "sigstr = $sigstr";
	
		//（2）构造密钥
	    $key = $appkey."&".$request_token_secret;
	
		//（3）生成oauth_signature签名值。这里需要确保PHP版本支持hash_hmac函数
	    $signature = $this->get_signature($sigstr, $key);
	    
		
		//构造请求url
	    $url      .= $normalized_str."&"."oauth_signature=".rawurlencode($signature);
	
	    //发出请求
	    $access_str = file_get_contents($url);
		$result = array();
		parse_str($access_str, $result);
		
		if (isset($result["error_code"]))
		{
			$ret = array(
				'error_code'=>$result['error_code']
			);
		}
		else {
			$ret = array(
				'openid'=>$result['openid'],
				'oauth_access_token'=>$result['oauth_token'],
				'oauth_access_token_secret'=>$result['oauth_token_secret'],
			);
		}
		
		return $ret;
	}

	 /*
	 * @brief 获取用户信息.请求需经过URL编码，编码时请遵循 RFC 1738
	 * 返回值为：	array(
	 *		'ret'=>'8FDD2F094ED59B6E3F59F72359648D2F',
	 *		'msg'=>'13410486451735847996',
	 *		'nickname'=>'WS47JCvy6mBQ637Q',
	 *		'figureurl'
	 *	);
	 * 
	 * 返回错误：
	 * 如果ret<0，会有相应的错误信息提示，返回数据全部用UTF-8编码
	 * 
	 * @param $appid
	 * @param $appkey
	 * @param $access_token
	 * @param $access_token_secret
	 * @param $openid
	 *
	 */
	function get_user_info($appid, $appkey, $access_token, $access_token_secret, $openid)
	{
		//获取用户信息的接口地址, 不要更改!!
	    $url    = "http://openapi.qzone.qq.com/user/get_user_info";
	    $info   = $this->do_get($url, $appid, $appkey, $access_token, $access_token_secret, $openid);
	    $arr = array();
	    $arr = json_decode($info, true);
	
	    return $arr;
	}
	
	private function get_request_token($appid, $appkey) 
	{
	    //请求临时token的接口地址, 不要更改!!
	    $url    = "http://openapi.qzone.qq.com/oauth/qzoneoauth_request_token?";
	
	
	    //生成oauth_signature签名值。签名值生成方法详见（http://wiki.opensns.qq.com/wiki/【QQ登录】签名参数oauth_signature的说明）
	    //（1） 构造生成签名值的源串（HTTP请求方式 & urlencode(uri) & urlencode(a=x&b=y&...)）
		$sigstr = "GET"."&".rawurlencode("http://openapi.qzone.qq.com/oauth/qzoneoauth_request_token")."&";
	
		//必要参数
	    $params = array();
	    $params["oauth_version"]          = "1.0";
	    $params["oauth_signature_method"] = "HMAC-SHA1";
	    $params["oauth_timestamp"]        = time();
	    $params["oauth_nonce"]            = mt_rand();
	    $params["oauth_consumer_key"]     = $appid;
	
	    //对参数按照字母升序做序列化
	    $normalized_str = $this->get_normalized_string($params);
	    $sigstr        .= rawurlencode($normalized_str);
	   
		
		//（2）构造密钥
	    $key = $appkey."&";
	
	
	 	//（3）生成oauth_signature签名值。这里需要确保PHP版本支持hash_hmac函数
	    $signature = $this->get_signature($sigstr, $key);
	    
			
		//构造请求url
	    $url      .= $normalized_str."&"."oauth_signature=".rawurlencode($signature);
	
	    //echo "$sigstr\n";
	    //echo "$url\n";
	
	    return file_get_contents($url);
	}
	
	/**
	 * @brief 对参数进行字典升序排序
	 *
	 * @param $params 参数列表
	 *
	 * @return 排序后用&链接的key-value对（key1=value1&key2=value2...)
	 */
	private function get_normalized_string($params)
	{
	    ksort($params);
	    $normalized = array();
	    foreach($params as $key => $val)
	    {
	        $normalized[] = $key."=".$val;
	    }
	
	    return implode("&", $normalized);
	}
	
	/**
	 * @brief 使用HMAC-SHA1算法生成oauth_signature签名值 
	 *
	 * @param $key  密钥
	 * @param $str  源串
	 *
	 * @return 签名值
	 */
	
	private function get_signature($str, $key)
	{
	    $signature = "";
	    if (function_exists('hash_hmac'))
	    {
	        $signature = base64_encode(hash_hmac("sha1", $str, $key, true));
	    }
	    else
	    {
	        $blocksize	= 64;
	        $hashfunc	= 'sha1';
	        if (strlen($key) > $blocksize)
	        {
	            $key = pack('H*', $hashfunc($key));
	        }
	        $key	= str_pad($key,$blocksize,chr(0x00));
	        $ipad	= str_repeat(chr(0x36),$blocksize);
	        $opad	= str_repeat(chr(0x5c),$blocksize);
	        $hmac 	= pack(
	            'H*',$hashfunc(
	                ($key^$opad).pack(
	                    'H*',$hashfunc(
	                        ($key^$ipad).$str
	                    )
	                )
	            )
	        );
	        $signature = base64_encode($hmac);
	    }
	
	    return $signature;
	} 
	
	/**
	 * @brief 所有Get请求都可以使用这个方法
	 *
	 * @param $url
	 * @param $appid
	 * @param $appkey
	 * @param $access_token
	 * @param $access_token_secret
	 * @param $openid
	 *
	 * @return true or false
	 */
	private function do_get($url, $appid, $appkey, $access_token, $access_token_secret, $openid)
	{
	    $sigstr = "GET"."&".rawurlencode("$url")."&";
	
	    //必要参数, 不要随便更改!!
	    $params = $_GET;
	    $params["oauth_version"]          = "1.0";
	    $params["oauth_signature_method"] = "HMAC-SHA1";
	    $params["oauth_timestamp"]        = time();
	    $params["oauth_nonce"]            = mt_rand();
	    $params["oauth_consumer_key"]     = $appid;
	    $params["oauth_token"]            = $access_token;
	    $params["openid"]                 = $openid;
	    unset($params["oauth_signature"]);
	
	    //参数按照字母升序做序列化
	    $normalized_str = $this->get_normalized_string($params);
	    $sigstr        .= rawurlencode($normalized_str);
	
	    //签名,确保php版本支持hash_hmac函数
	    $key = $appkey."&".$access_token_secret;
	    $signature = $this->get_signature($sigstr, $key);
	    $url      .= "?".$normalized_str."&"."oauth_signature=".rawurlencode($signature);
	
	    //echo "$url\n";
	    return file_get_contents($url);
	}

	/**
	 * @brief 所有post 请求都可以使用这个方法
	 *
	 * @param $url
	 * @param $appid
	 * @param $appkey
	 * @param $access_token
	 * @param $access_token_secret
	 * @param $openid
	 *
	 */
	function do_post($url, $appid, $appkey, $access_token, $access_token_secret, $openid)
	{
	    //构造签名串.源串:方法[GET|POST]&uri&参数按照字母升序排列
	    $sigstr = "POST"."&".rawurlencode($url)."&";
	
	    //必要参数,不要随便更改!!
	    $params = $_POST;
	    $params["oauth_version"]          = "1.0";
	    $params["oauth_signature_method"] = "HMAC-SHA1";
	    $params["oauth_timestamp"]        = time();
	    $params["oauth_nonce"]            = mt_rand();
	    $params["oauth_consumer_key"]     = $appid;
	    $params["oauth_token"]            = $access_token;
	    $params["openid"]                 = $openid;
	    unset($params["oauth_signature"]);
	
	    //对参数按照字母升序做序列化
	    $sigstr .= rawurlencode(get_normalized_string($params));
	
	    //签名,需要确保php版本支持hash_hmac函数
	    $key = $appkey."&".$access_token_secret;
	    $signature = get_signature($sigstr, $key); 
	    $params["oauth_signature"] = $signature; 
	
	    $postdata = get_urlencode_string($params);
	
	    //echo "$sigstr******\n";
	    //echo "$postdata\n";
	
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
	    curl_setopt($ch, CURLOPT_POST, TRUE); 
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
	    curl_setopt($ch, CURLOPT_URL, $url);
	    $ret = curl_exec($ch);
	
	    curl_close($ch);
	    return $ret;
	
	}
	
}