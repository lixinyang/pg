<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once( dirname(__FILE__).'/bindbase.php' );
class Qq extends Bindbase {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url', 'qq'));
		$this->load->model('usermanager');
	}
	
	function show() {
		$qq = new QqConnect();
		$callback = site_url().'/binding/qq/callback';
		$result = $qq->get_authorize_url(QQ_APPID, QQ_APPKEY, $callback);
		$this->session->set_userdata('oauth_request_token', $result['oauth_request_token']);
		$this->session->set_userdata('oauth_request_token_secret', $result['oauth_request_token_secret']);
		redirect($result['authorize_url']);
	}
	
	function callback() {
		//var_dump($_REQUEST);
		$qq = new QqConnect();
		//这个key就是这个用户的令牌，很NB，要好好保存
		$result = $qq->get_access_token(QQ_APPID, QQ_APPKEY, $this->session->userdata('oauth_request_token') , $this->session->userdata('oauth_request_token_secret') , $_REQUEST['oauth_vericode']);
		//var_dump($result);
		$sns_oauth_token = $result['oauth_access_token'];
		$sns_oauth_token_secret = $result['oauth_access_token_secret'];
		$sns_uid = $result['openid'];
		if(empty($sns_uid)) throw new Exception('oauth fail, havnt got get_access_token()');
		
		//获取用户信息
		$me = $qq->get_user_info(QQ_APPID, QQ_APPKEY, $sns_oauth_token , $sns_oauth_token_secret , $sns_uid);
		
		//把资料准备好之后，剩下的就交给父类里的模版方法了！
		parent::post_login(UserManager::sns_website_qq, $sns_uid, $sns_oauth_token, $sns_oauth_token_secret, $me['nickname']);
		/*
		$binding = $this->usermanager->get_binding_by_sns_uid(UserManager::sns_website_qq, $sns_uid);
		if(empty($binding))
		{
			//初次登录用户
			//创建用户（同时创建sns_binding）
			$user = $this->usermanager->create_user(UserManager::sns_website_qq, $sns_uid, $sns_oauth_token, $sns_oauth_token_secret, $me['nickname']);
			//把新创建的用户放到ci->weixiao里
			$this->weixiao->set_user_token($user->user_token);
			$cur_user = $this->weixiao->get_cur_user();
			if(empty($cur_user)) throw new Exception("something strange happens, cant get user just login.");
			$data = array('user'=>$cur_user);
			$this->load->view('binding/first_binding', $data);
		}
		else {
			//老用户
			//TODO 初次登录进入binding/first_binding，否则关闭弹出窗口，刷新父页面
			$user = $this->usermanager->get_by_id($binding->user_id);
			//把新创建的用户放到ci->weixiao里
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