<?php
require_once( dirname(__FILE__).'/renren/RenRenClient.class.php' );

/**
 * 对RenRenClient.class.php的构造函数修改了，并添加了init()方法，从而改变了RenRenClient.class.php的config方式。
 * 
 * @author lxy
 *
 */
class RenrenConnect
{
	function get_authorize_url($appkey, $callback, $display='page')
	{
		$url = 'https://graph.renren.com/oauth/authorize';
		$params = array(
			'client_id' => $appkey,
			'redirect_uri' => $callback,
			'response_type' => 'code',
			'display' => $display
		);
		
		$url .= '?'.$this->get_normalized_string($params);
		
		return $url;
	}
	
	function get_access_token($appkey, $appsecret, $callback, $code)
	{
		$url = 'https://graph.renren.com/oauth/token';
		$params = array(
			'client_id' => $appkey,
			'client_secret' => $appsecret,
			'redirect_uri' => $callback,
			'code' => $code,
			'grant_type' => 'authorization_code'
		);
		$rrObj = new RenRenClient();
		$rrObj->init($appkey, $appsecret);
		$result = $rrObj->_POST($url,$params);
		
		if (!empty($result->access_token))
		{
			return $result->access_token;
		}
		else 
		{
			//出错处理
			return $result;
		}
		
	}
	
	/**
	 * 返回值见：http://wiki.dev.renren.com/wiki/Get_API_session_key
	 *
	 * @param unknown_type $access_token
	 */
	function get_session_key($access_token)
	{
		$url = 'https://graph.renren.com/renren_api/session_key';

		$rrObj = new RenRenClient();
		$result = $rrObj->_GET($url,array('oauth_token'=>$access_token));
	    
		return $result;
	}
	
	function get_user_info($appkey, $appsecret, $session_key, $sns_uid , $fields='uid,name,tinyurl,headhurl,zidou,star')
	{
		$rrObj = new RenRenClient();
		$rrObj->init($appkey, $appsecret);
		$rrObj->setSessionKey($session_key);
		$result = $rrObj->POST('users.getInfo', array($sns_uid , $fields));
		return $result;
	}
	
	function get_friends($appkey, $appsecret, $session_key, $page=1, $count=500)
	{
		$rrObj = new RenRenClient();
		$rrObj->init($appkey, $appsecret);
		$rrObj->setSessionKey($session_key);
		$result = $rrObj->POST('friends.getFriends', array($page , $count));
		return $result;
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
	
}