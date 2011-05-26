<?php
class User extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		//$this->load->helper('url');
	}
	
	function index()
	{
		$data = array();
		$data['query'] = $this->db->get('users');
		$this->load->view('user/list', $data);
	}
	
	function add()
	{
		$this->db->insert('users',$_POST);
		redirect('user');
	}
}