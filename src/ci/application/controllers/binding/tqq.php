<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once( dirname(__FILE__).'/bindbase.php' );
class Tqq extends Bindbase {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url', 't_qq'));
		$this->load->model('usermanager');
	}
	
	function show() {
		$callback = site_url().'/binding/tqq/callback';
		$o = new MBOpenTOAuth( MB_AKEY , MB_SKEY  );
		$keys = $o->getRequestToken($callback);
		$this->session->set_userdata($keys);
		
		$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false,'');
	
		redirect($aurl);
	}
	
	function callback() {
		//var_dump($_REQUEST);
		//这个key就是这个用户的令牌，很NB，要好好保存
		$callback = site_url().'/binding/tqq/callback';
		//var_dump($this->session);
		$o = new MBOpenTOAuth( MB_AKEY , MB_SKEY , $this->session->userdata('oauth_token') , $this->session->userdata('oauth_token_secret'));
		$result = $o->getAccessToken(  $_REQUEST['oauth_verifier'] ) ;//获取ACCESSTOKEN
		//var_dump($result);
		$sns_oauth_token = $result['oauth_token'];
		$sns_oauth_token_secret = $result['oauth_token_secret'];
		$sns_uid = $result['name'];//让人吃金啊
		if(empty($sns_uid)) throw new Exception('oauth fail, havnt got getAccessToken()');
		
		//获取用户信息
		//$c = new WeiboClient( WB_AKEY , WB_SKEY , $sns_oauth_token , $sns_oauth_token_secret);
		$c = new MBApiClient( MB_AKEY , MB_SKEY , $sns_oauth_token , $sns_oauth_token_secret);
		$me = $c->getUserInfo();
		$me = $me['data'];
		
		//把资料准备好之后，剩下的就交给父类里的模版方法了！
		parent::post_login(UserManager::sns_website_tqq, $sns_uid, $sns_oauth_token, $sns_oauth_token_secret, $me['name']);
		/*
		$binding = $this->usermanager->get_binding_by_sns_uid(UserManager::sns_website_tqq, $sns_uid);
		if(empty($binding))
		{
			//初次登录用户
			//创建用户（同时创建sns_binding）
			$user = $this->usermanager->create_user(UserManager::sns_website_tqq, $sns_uid, $sns_oauth_token, $sns_oauth_token_secret, $me['name']);
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