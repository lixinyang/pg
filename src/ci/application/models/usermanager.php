<?php
class UserManager
{
	/**
	 * 
	 * 根据用户在session中的user_token来取这个用户，因为user_id是单调递增的，而user_token是user_id的sha1，不容易被猜到
	 * @todo 尚未给出实现
	 * @param string $user_token
	 */
	function get_by_token($user_token)
	{
		echo __FUNCTION__;
		return array(
			'uid'=>'000',
			'user_token'=>'asdfghjkl'
			);
	}
}
?>