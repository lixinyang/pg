<?php
/**
 * 
 * 这是一个很NB的类，它是weixiao001相关逻辑的入口，它会被CI框架自动加载（见config/autoload.php）。
 * 所以它可以这样使用get_instance()->weixiao->get_cur_user();
 * 
 * 它里面定义了常量、变量、常用方法
 * @author lxy
 *
 */
class Weixiao
{
	//Cookie中cookie_token的变量名
	const COOKIE_TOKEN = "cookie_token";
	//Cookie中user_token的变量名
	const USER_TOKEN = "user_token";
	
	private $ci;
	public $cookie_token;
	private $user_token;
	private $user;
	
	function __construct()
	{
		$this->ci = & get_instance();
	}
	
	/**
	 *  
	 * @param string $user_token
	 */
	public function set_user_token($user_token)
	{
		$this->user_token = $user_token;
	}
	
	/**
	 * 当前用户是否登录
	 * 
	 */
	public function is_login()
	{
		return !empty($this->user);
	}
	
	/**
	 * 
	 * 取得当前的访问用户
	 */
	public function get_cur_user()
	{
		if(empty($this->user) || $this->user['user_token']!=$this->user_token)
		{
			if($this->user_token)
			{
				$this->user = $this->ci->usermanager->get_by_token($this->user_token);
			}
		}
		return $this->user;
	}
}
?>