<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Index extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('weibooauth');
		$this->load->model('usermanager');
	}
	
	/**
	 * 
	 * 网站的首页
	 */
	public function index()
	{
		if($this->weixiao->is_login())
		{
			$binding_sina = $this->usermanager->get_binding_by_uid(UserManager::sns_website_sina, $this->weixiao->get_cur_user()->id);
			//读sina的数据
			$c = new WeiboClient( WB_AKEY , WB_SKEY , $binding_sina->sns_oauth_token , $binding_sina->sns_oauth_token_secret);
			$me = $c->verify_credentials();
			$data = array(
				'user' => $this->weixiao->get_cur_user(),
				'sina' => $me
			);
			$this->load->view('index_login', $data);
		}
		else
		{
			$this->load->view('index_not_login');
		}
		//$this->load->view('main_view',  array('users'=>$this->db->get('users')));
	}
	
	public function logout() {
		$this->weixiao->logout();
		redirect('/');
	}
}
?>