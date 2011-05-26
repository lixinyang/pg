<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Index extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}
	
	/**
	 * 
	 * 网站的首页
	 */
	public function index()
	{
		if($this->weixiao->is_login())
		{
			$this->load->view('index_login');
		}
		else
		{
			$this->load->view('index_not_login');
		}
		//$this->load->view('main_view',  array('users'=>$this->db->get('users')));
	}
}
?>