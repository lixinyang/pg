<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//require_once '../librarys/weibooauth.php';
class Sina extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		//$this->load->library('weibooauth','sina');
		$this->load->helper(array('url','weibooauth'));
	}
	
	function show() {
		$data = array();
		$data['auth_url'] = $this->get_auth_url();
		$this->load->view('binding/sina', $data);
	}
	
	private function get_auth_url() {
		$o = new WeiboOAuth( WB_AKEY , WB_SKEY  );
		$keys = $o->getRequestToken();
		$callback = site_url().'binding/sina/callback';
		
		$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , $callback );
		
		$_SESSION['oauth_keys'] = $keys;
		
		return $aurl;
	}
	
	function callback($param) {
		$o = new WeiboOAuth( WB_AKEY , WB_SKEY , $_SESSION['oauth_keys']['oauth_token'] , $_SESSION['oauth_keys']['oauth_token_secret']  );
		//这个key就是这个用户的令牌，很NB，要好好保存
		$last_key = $o->getAccessToken(  $_REQUEST['oauth_verifier'] ) ;
		
		//TODO 初次登录进入binding/first_binding，否则关闭弹出窗口，刷新父页面
		$data = array('access_key'=>$last_key);
		$this->load->view('binding/first_binding', $data);
	}
}
?>