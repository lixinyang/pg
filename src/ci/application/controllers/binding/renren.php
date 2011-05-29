<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once( dirname(__FILE__).'/bindbase.php' );
class Renren extends Bindbase {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url', 'renren'));
		$this->load->model('usermanager');
	}
	
	function show() {
		$renren = new RenrenConnect();
		$callback = site_url().'/binding/renren/callback';
		$url = $renren->get_authorize_url(RENREN_APPKEY, $callback, 'popup');
		redirect($url);
	}
	
	function callback() {
		//var_dump($_REQUEST);
		$renren = new RenrenConnect();
		//这个key就是这个用户的令牌，很NB，要好好保存
		$callback = site_url().'/binding/renren/callback';
		$sns_oauth_token = $renren->get_access_token(RENREN_APPKEY, RENREN_APPSECRET, $callback , $_REQUEST['code']);
		//var_dump($sns_oauth_token);
		$result = $renren->get_session_key($sns_oauth_token);
		//var_dump($result);
		$reren_session_key = $result->renren_token->session_key;
		$reren_session_secret = $result->renren_token->session_secret;
		$reren_token_expires_in = $result->renren_token->expires_in;
		$sns_uid = $result->user->id;
		if(empty($sns_uid)) throw new Exception('oauth fail, havnt got get_access_token()');
		
		//获取用户信息
		$me = $renren->get_user_info(RENREN_APPKEY, RENREN_APPSECRET, $reren_session_key , $sns_uid);
		//var_dump($me);
		
		//把资料准备好之后，剩下的就交给父类里的模版方法了！
		parent::post_login(UserManager::sns_website_renren, $sns_uid, $reren_session_key, $reren_session_secret, $me[0]->name, $reren_token_expires_in);
		/*
		$binding = $this->usermanager->get_binding_by_sns_uid(UserManager::sns_website_renren, $sns_uid);
		if(empty($binding))
		{
			//初次登录用户
			//创建用户（同时创建sns_binding）
			$user = $this->usermanager->create_user(UserManager::sns_website_renren, $sns_uid, $reren_session_key, $reren_session_secret, $me[0]->name, $reren_token_expires_in);
			//把新创建的用户放到ci->weixiao里
			$this->weixiao->set_user_token($user->user_token);
			$cur_user = $this->weixiao->get_cur_user();
			if(empty($cur_user)) throw new Exception("something strange happens, cant get user just login.");
			$data = array('user'=>$cur_user);
			$this->load->view('binding/first_binding', $data);
		}
		else {
			//老用户
			$user = $this->usermanager->get_by_id($binding->user_id);
			//更新session_key，$reren_token_expires_in和display_name
			if ($reren_session_key!=$binding->sns_oauth_token || $me[0]->name!=$binding->sns_display_name) {
				$binding = $this->usermanager->update_sns_binding($binding->user_id, UserManager::sns_website_renren, $sns_uid, $reren_session_key, $reren_session_secret, $me[0]->name, $reren_token_expires_in);
			}
			//把刚登录的的用户放到ci->weixiao里
			$this->weixiao->set_user_token($user->user_token);
			$cur_user = $this->weixiao->get_cur_user();
			if(empty($cur_user)) throw new Exception("something strange happens, cant get user just login.");
			$data = array('user'=>$cur_user);
			$this->load->view('binding/not_first_binding', $data);
		}
		*/
	}
}
?>