<?php
/**
 * 
 * 他管理user表和binding表两张表
 * @author lxy
 *
 */
class UserManager extends CI_Model
{
	/**
	 * 
	 * sns_binding表中的sns_website的取值
	 * @var unknown_type
	 */
	const sns_website_sina = 'sina';
	const sns_website_qq = 'qq';
	
	/**
	 * 
	 * 从数据库中获取某一唯一值的方法
	 * 没找到返回null
	 * 找到一个就返回那个
	 * 找到多个就抛exception（要注意哦！！）
	 * @param unknown_type $table_name
	 * @param unknown_type $query
	 * @throws Excpetion
	 */
	private function get_uniq($table_name, $query)
	{
		$q = $this->db->get_where($table_name, $query);
		if($q->num_rows()==0)
		{
			return null;
		}
		else if($q->num_rows()>1)
		{
			throw new Excpetion("damn! we got something dupulicated that should be uniq: table(".$table_name."), query(".var_dump($query).")");
		}
		else
		{
			return $q->row();
		}
	}
	function get_by_id($id)
	{
		$q = $this->db->get_where(TBL_USER, array('id'=>$id));
		if ($q->num_rows()==1) {
			return $q->row();
		}
		else {
			return null;
		}
	}
	/**
	 * 
	 * 根据用户在session中的user_token来取这个用户，因为user_id是单调递增的，而user_token是user_id的sha1，不容易被猜到
	 * 找到了1个就返回，没找到或者找到多个就返回null
	 * @param string $user_token
	 */
	function get_by_token($user_token)
	{
		$q = $this->db->get_where(TBL_USER, array('user_token'=>$user_token));
		if ($q->num_rows()==1) {
			return $q->row();
		}
		else {
			return null;
		}
	}
	
	/**
	 * 通过sns站点，用户在我们站点上的uid，查询sns_binding
	 * 不存在会返回null，存在就返回binding的信息
	 * @param string $sns_website, $uid
	 */
	function get_binding_by_uid($sns_website, $uid) 
	{
		return $this->get_uniq(TBL_SNS_BINDING, array('sns_website'=>$sns_website,'user_id'=>$uid));
	}
	
	/**
	 * 通过sns站点，用户在sns站点上的uid，查询sns_binding
	 * 不存在会返回null，存在就返回binding的信息
	 * @param string $sns_website, $sns_uid
	 */
	function get_binding_by_sns_uid($sns_website, $sns_uid) {
		$q = $this->db->get_where(TBL_SNS_BINDING, array('sns_website'=>$sns_website,'sns_uid'=>$sns_uid));
		if($q->num_rows()==0)
		{
			return null;
		}
		else if($q->num_rows()>1)
		{
			throw new Excpetion("damn! we got repeated sns_binding for accesskey:".$oauth_token);
		}
		else
		{
			return $q->row();
		}
	}
	
	function create_user($sns_website, $sns_uid, $sns_oauth_token, $sns_oauth_token_secret, $name='')
	{
		//已经绑定过了
		$binding = $this->get_binding_by_sns_uid($sns_website, $sns_uid);
		if (!empty($binding)) {
			throw new Exception('sns_binding already binded: '.$sns_website.",".$sns_uid);
		}
		
		$this->db->trans_start();
		//生成user
		$data = array(
			'display_name' => $name,
			'regist_time' => date("Y-m-d H:i:s")
		);
		$this->db->insert(TBL_USER, $data);
		$uid = $this->db->insert_id();
		
		//设置user_token
		$this->db->where('id',$uid);
		$user_token = sha1($uid);
		$this->db->update(TBL_USER, array('user_token' => $user_token));
		
		//生成binding
		$this->create_sns_binding($uid, $sns_website, $sns_uid, $sns_oauth_token, $sns_oauth_token_secret, $name);
		
		$this->db->trans_complete();
		
		return $this->get_by_token($user_token);
	}
	
	function create_sns_binding($uid, $sns_website, $sns_uid, $sns_oauth_token, $sns_oauth_token_secret, $name='') {
		//已经绑定过了
		$binding = $this->get_binding_by_sns_uid($sns_website, $sns_uid);
		if (!empty($binding)) {
					throw new Exception('sns_binding already binded: '.$sns_website.",".$sns_uid);
		}
		$binding = array(
			'user_id'=>$uid,
			'sns_website'=>$sns_website,
			'sns_uid'=>$sns_uid,
			'sns_oauth_token'=>$sns_oauth_token,
			'sns_oauth_token_secret'=>$sns_oauth_token_secret,
			'sns_display_name'=>$name
		);
		$this->db->insert(TBL_SNS_BINDING, $binding);
		
		return $this->get_binding_by_sns_uid($sns_website, $sns_uid);
	}
	
}
?>