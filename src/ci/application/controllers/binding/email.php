<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//require_once '../librarys/weibooauth.php';
class Email extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url','form'));
		$this->load->model('usermanager');
		$this->load->library(array('form_validation','input'));
	}
	
	/**
	 * 显示，未登录页的用“Email+密码登录”
	 */
	function show() {
		$this->load->view('binding/email');
	}
	
	/**
	 * 处理，未登录页的用“Email+密码登录”
	 */
	function login() {
		$this->form_validation->set_rules('email', 'Email', 'required');
  		$this->form_validation->set_rules('passwd', 'Password', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->show();
		}
		else
		{
			$email = $this->input->get_post('email');
			$passwd = $this->input->get_post('passwd');
			$user = $this->usermanager->get_by_email($email);
			if (empty($user) || trim($passwd)!=trim($user->passwd))
			{
				$this->load->view('binding/email');
			}
			else{
				$this->weixiao->set_user_token($user->user_token);
				$cur_user = $this->weixiao->get_cur_user();
				$data = array('user'=>$cur_user);
				$this->load->view('binding/not_first_binding', $data);
			}
		}
		
	}

	/**
	 * 显示，已登录页的“绑定Email”
	 */
	function bind() {
		$this->load->view('binding/email_regist');
	}
	
	/**
	 * 处理，已登录页的“绑定Email”
	 */
	function bind_submit() {
		$this->form_validation->set_rules('email', 'Email', 'required');
  		$this->form_validation->set_rules('passwd', 'Password', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->bind();
		}
		else
		{
			$email = $this->input->get_post('email');
			$passwd = $this->input->get_post('passwd');
			$user = $this->weixiao->get_cur_user();
			if (!empty($user))
			{
				$cur_user = $this->usermanager->update_user($user->id, null, $email, $passwd);
				$data = array(
					'title'=>'Email绑定成功',
					'heading'=>'Email('.$email.') 绑定成功！',
					'text'=>'现在您可以使用 '.$email.' 登录了。。',
					'timeout'=>5000,
					'auto_close'=>true
				);
				$this->load->view('binding/splash', $data);
			}
		}
		
	}
}
?>