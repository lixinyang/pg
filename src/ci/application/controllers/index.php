<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Index extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//$this->load->helper(array('url','weibooauth','qq','t_qq','renren','url'));
		$this->load->helper(array('url','weibooauth','qq','renren','html'));
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
			$data = array(
				'user' => $this->weixiao->get_cur_user(),
			);
			//获得所有绑定的数据
			$bindings = $this->usermanager->get_binding_by_uid($this->weixiao->get_cur_user()->id);
			foreach ($bindings as $binding)
			{
				switch ($binding->sns_website)
				{
					case UserManager::sns_website_sina:
						$data['binding_sina'] = $binding;
						//读sina的数据
						$c = new WeiboClient( WB_AKEY , WB_SKEY , $binding->sns_oauth_token , $binding->sns_oauth_token_secret);
						$me = $c->verify_credentials();
						$data['sina'] = $me;
						break;
					case UserManager::sns_website_qq:
						$data['binding_qq'] = $binding;
						//读qq的数据
						$qq = new QqConnect();
						$me = $qq->get_user_info(QQ_APPID, QQ_APPKEY, $binding->sns_oauth_token , $binding->sns_oauth_token_secret , $binding->sns_uid);
						$data['qq'] = $me;
						break;
					case UserManager::sns_website_tqq:
						$data['binding_tqq'] = $binding;
						//读t.qq的数据
//						$c = new MBApiClient( MB_AKEY , MB_SKEY , $binding->sns_oauth_token , $binding->sns_oauth_token_secret);
//						$me = $c->getUserInfo();
//						$me = $me['data'];
//						$data['tqq'] = $me;
						break;
					case UserManager::sns_website_renren:
						$data['binding_renren'] = $binding;
						//读renren的数据
						$renren = new RenrenConnect();
						$me = $renren->get_user_info(RENREN_APPKEY, RENREN_APPSECRET, $binding->sns_oauth_token , $binding->sns_uid);
						$data['renren'] = $me[0];
						break;
				}
			}
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