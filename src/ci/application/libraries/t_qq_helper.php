<?php
namespace qq;
/**
 * 
 * 使用者只需要MBApiClient（调用数据时）、MBOpenTOAuth（登录时）两个类，其他请无视。
 * 基本调用过程分如下三步
 * 
 * $o = new MBOpenTOAuth( MB_AKEY , MB_SKEY  );
 * $keys = $o->getRequestToken('http://h.t.net/sdk/callback.php');//这里填上你的回调URL
 * $aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false,'');
 * $_SESSION['keys'] = $keys;
 * 
 * -------------
 * 
 * $o = new MBOpenTOAuth( MB_AKEY , MB_SKEY , $_SESSION['keys']['oauth_token'] , $_SESSION['keys']['oauth_token_secret']  );
 * $last_key = $o->getAccessToken(  $_REQUEST['oauth_verifier'] ) ;//获取ACCESSTOKEN
 * $_SESSION['last_key'] = $last_key;
 *
 * -------------
 * 
 * $c = new MBApiClient( MB_AKEY , MB_SKEY , $_SESSION['last_key']['oauth_token'] , $_SESSION['last_key']['oauth_token_secret']  );
 * 
 * @author lxy
 *
 */
class MBApiClient
{
    /** 
     * 构造函数 
     *  
     * @access public 
     * @param mixed $wbakey 应用APP KEY 
     * @param mixed $wbskey 应用APP SECRET 
     * @param mixed $accecss_token OAuth认证返回的token 
     * @param mixed $accecss_token_secret OAuth认证返回的token secret 
     * @return void 
	 */
	public $host = 'open.t.qq.com';
    function __construct( $wbakey , $wbskey , $accecss_token , $accecss_token_secret ) 
	{
        $this->oauth = new MBOpenTOAuth( $wbakey , $wbskey , $accecss_token , $accecss_token_secret ); 
	}

	/******************
	 * 获取用户消息
     * @access public 
	*@f 分页标识（0：第一页，1：向下翻页，2向上翻页）
	*@t: 本页起始时间（第一页 0，继续：根据返回记录时间决定）
	*@n: 每次请求记录的条数（1-20条）
	*@name: 用户名 空表示本人
	 * *********************/
	public function getTimeline($p){
		if(!isset($p['name'])){
			$url = 'http://open.t.qq.com/api/statuses/home_timeline?f=1';
			$params = array(
				'format' => MB_RETURN_FORMAT,
				'pageflag' => $p['f'],
				'reqnum' => $p['n'],
				'pagetime' =>  $p['t']
			);					
		}else{
			$url = 'http://open.t.qq.com/api/statuses/user_timeline?f=1';
			$params = array(
				'format' => MB_RETURN_FORMAT,
				'pageflag' => $p['f'],
				'reqnum' => $p['n'],
				'pagetime' =>  $p['t'],
				'name' => $p['name']
			);					
		}
	 	return $this->oauth->get($url,$params); 
	}

	/******************
	 * 广播大厅消息
	*@p: 记录的起始位置（第一次请求是填0，继续请求进填上次返回的Pos）
	*@n: 每次请求记录的条数（1-20条）
	 * *********************/
	public function getPublic($p){
		$url = 'http://open.t.qq.com/api/statuses/public_timeline?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'pos' => $p['p'],
			'reqnum' => $p['n']	
		);
	 	return $this->oauth->get($url,$params); 
	}

	/******************
	*获取关于我的消息 
	*@f 分页标识（0：第一页，1：向下翻页，2向上翻页）
	*@t: 本页起始时间（第一页 0，继续：根据返回记录时间决定）
	*@n: 每次请求记录的条数（1-20条）
	*@l: 当前页最后一条记录，用用精确翻页用
	*@type : 0 提及我的, other 我发表的
	**********************/
	public function getMyTweet($p){
		$p['type']==0?$url = 'http://open.t.qq.com/api/statuses/mentions_timeline?f=1':$url = 'http://open.t.qq.com/api/statuses/broadcast_timeline?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'pageflag' => $p['f'],
			'reqnum' => $p['n'],
			'pagetime' => $p['t'],
			'lastid' => $p['l']	
		);
	 	return $this->oauth->get($url,$params); 
	}

	/******************
	*获取话题下的消息
	*@t: 话题名字
	*@f 分页标识（PageFlag = 1表示向后（下一页）查找；PageFlag = 2表示向前（上一页）查找；PageFlag = 3表示跳到最后一页  PageFlag = 4表示跳到最前一页）
	*@p: 分页标识（第一页 填空，继续翻页：根据返回的 pageinfo决定）
	*@n: 每次请求记录的条数（1-20条）
	**********************/
	public function getTopic($p){
		$url = 'http://open.t.qq.com/api/statuses/ht_timeline?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'pageflag' => $p['f'],
			'reqnum' => $p['n'],
			'httext' => $p['t'],
			'pageinfo' => $p['p']
		);
	 	return $this->oauth->get($url,$params); 
	}

	/******************
	*获取一条消息
	*@id: 微博ID
	**********************/
	public function getOne($p){
		$url = 'http://open.t.qq.com/api/t/show?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'id' => $p['id']
		);
	 	return $this->oauth->get($url,$params); 
	}

	/******************
	*发表一条消息
	*@c: 微博内容
	*@ip: 用户IP(以分析用户所在地)
	*@j: 经度（可以填空）
	*@w: 纬度（可以填空）
	*@p: 图片
	*@r: 父id
	*@type: 1 发表 2 转播 3 回复 4 点评
	**********************/
	public function postOne($p){
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'content' => $p['c'],
			'clientip' => $p['ip'],
			'jing' => $p['j'],
			'wei' => $p['w']
		);
		switch($p['type']){
			case 2:
				$url = 'http://open.t.qq.com/api/t/re_add?f=1';
				$params['reid'] = $p['r'];
				return $this->oauth->post($url,$params); 
				break;
			case 3:
				$url = 'http://open.t.qq.com/api/t/reply?f=1';
				$params['reid'] = $p['r'];
				return $this->oauth->post($url,$params); 
				break;
			case 4:
				$url = 'http://open.t.qq.com/api/t/comment?f=1';
				$params['reid'] = $p['r'];
				return $this->oauth->post($url,$params); 
				break;
			default:
				if(!empty($p['p'])){
					$url = 'http://open.t.qq.com/api/t/add_pic?f=1';
					$params['pic'] = $p['p'];
					return $this->oauth->post($url,$params,true); 
				}else{
					$url = 'http://open.t.qq.com/api/t/add?f=1';
					return $this->oauth->post($url,$params); 
				}	
			break;			
		}	

	}

	/******************
	*删除一条消息
	*@id: 微博ID
	**********************/
	public function delOne($p){
		$url = 'http://open.t.qq.com/api/t/del?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'id' => $p['id']
		);
	 	return $this->oauth->post($url,$params); 
	}	

	/******************
	*获取转播和点评消息列表
	*@reid：转发或者回复根结点ID；
	*@f：（根据dwTime），0：第一页，1：向下翻页，2向上翻页；
	*@t：起始时间戳，上下翻页时才有用，取第一页时忽略；
	*@tid：起始id，用于结果查询中的定位，上下翻页时才有用；
	*@n：要返回的记录的条数(1-20)；
	*@Flag:标识0 转播列表，1点评列表 2 点评与转播列表
	**********************/
	public function getReplay($p){
		$url = 'http://open.t.qq.com/api/t/re_list?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'rootid' => $p['reid'],
			'pageflag' => $p['f'],
			'reqnum' => $p['n'],
			'flag' => $p['flag']
		);
		if(isset($p['t'])){
			$params['pagetime'] = $p['t'];	
		}
		if(isset($p['tid'])){
			$params['twitterid'] = $p['tid'];	
		}
	 	return $this->oauth->get($url,$params); 	
	}

	/******************
	*获取当前用户的信息
	*@n:用户名 空表示本人
	**********************/
	public function getUserInfo($p=false){
		if(!$p || !$p['n']){
			$url = 'http://open.t.qq.com/api/user/info?f=1';
			$params = array(
				'format' => MB_RETURN_FORMAT
			);
		}else{
			$url = 'http://open.t.qq.com/api/user/other_info?f=1';
			$params = array(
				'format' => MB_RETURN_FORMAT,
				'name' => $p['n']
			);
		}
	 	return $this->oauth->get($url,$params); 	
	}

	/******************
	*更新用户资料
	*@p 数组,包括以下:
	*@nick: 昵称
	*@sex: 性别 0 ，1：男2：女
	*@year:出生年 1900-2010
	*@month:出生月 1-12
	*@day:出生日 1-31
	*@countrycode:国家码
	*@provincecode:地区码
	*@citycode:城市 码
	*@introduction: 个人介绍
	**********************/
	public function updateMyinfo($p){
		$url = 'http://open.t.qq.com/api/user/update?f=1';
		$p['format'] = MB_RETURN_FORMAT;
	 	return $this->oauth->post($url,$p); 	
	}	

	/******************
	*更新用户头像
	*@Pic:文件域表单名 本字段不能放入到签名串中
	******************/
	public function updateUserHead($p){
		$url = 'http://open.t.qq.com/api/user/update_head?f=1';
		$p['format'] = MB_RETURN_FORMAT;
		return $this->oauth->post($url, $p, true); 	
	}	

	/******************
	*获取听众列表/偶像列表
	*@num: 请求个数(1-30)
	*@start: 起始位置
	*@n:用户名 空表示本人
	*@type: 0 听众 1 偶像
	**********************/
	public function getMyfans($p){
		try{
			if($p['n']  == ''){
				$p['type']?$url = 'http://open.t.qq.com/api/friends/idollist':$url = 'http://open.t.qq.com/api/friends/fanslist';
			}else{
				$p['type']?$url = 'http://open.t.qq.com/api/friends/user_idollist':$url = 'http://open.t.qq.com/api/friends/user_fanslist';
			}
			$params = array(
				'format' => MB_RETURN_FORMAT,
				'name' => $p['n'],
				'reqnum' => $p['num'],
				'startindex' => $p['start']
			);
		 	return $this->oauth->get($url,$params);
		} catch(MBException $e) {
			$ret = array("ret"=>0, "msg"=>"ok"
					, "data"=>array("timestamp"=>0, "hasnext"=>1, "info"=>array()));
			return $ret;
		}
	}

	/******************
	*收听/取消收听某人
	*@n: 用户名
	*@type: 0 取消收听,1 收听 ,2 特别收听
	**********************/	
	public function setMyidol($p){
		switch($p['type']){
			case 0:
				$url = 'http://open.t.qq.com/api/friends/del?f=1';
				break;
			case 1:
				$url = 'http://open.t.qq.com/api/friends/add?f=1';
				break;
			case 2:
				$url = 'http://open.t.qq.com/api/friends/addspecail?f=1';
				break;
		}
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'name' => $p['n']
		);
	 	return $this->oauth->post($url,$params);		
	}
	
	/******************
	*检测是否我粉丝或偶像
	*@n: 其他人的帐户名列表（最多30个,逗号分隔）
	*@flag: 0 检测粉丝，1检测偶像
	**********************/	
	public function checkFriend($p){
		$url = 'http://open.t.qq.com/api/friends/check?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'names' => $p['n'],
			'flag' => $p['type']
		);
		return $this->oauth->get($url,$params);
	}

	/******************
	*发私信
	*@c: 微博内容
	*@ip: 用户IP(以分析用户所在地)
	*@j: 经度（可以填空）
	*@w: 纬度（可以填空）
	*@n: 接收方微博帐号
	**********************/
	public function postOneMail($p){
		$url = 'http://open.t.qq.com/api/private/add?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'content' => $p['c'],
			'clientip' => $p['ip'],
			'jing' => $p['j'],
			'wei' => $p['w'],
			'name' => $p['n']
			);
		return $this->oauth->post($url,$params); 
	}
	
	/******************
	*删除一封私信
	*@id: 微博ID
	**********************/
	public function delOneMail($p){
		$url = 'http://open.t.qq.com/api/private/del?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'id' => $p['id']
		);
	 	return $this->oauth->post($url,$params); 
	}
	
	/******************
	*私信收件箱和发件箱
	*@f 分页标识（0：第一页，1：向下翻页，2向上翻页）
	*@t: 本页起始时间（第一页 0，继续：根据返回记录时间决定）
	*@n: 每次请求记录的条数（1-20条）
	*@type : 0 发件箱 1 收件箱
	**********************/	
	public function getMailBox($p){
		if($p['type']){
			$url = 'http://open.t.qq.com/api/private/recv?f=1';
		}else{
			$url = 'http://open.t.qq.com/api/private/send?f=1';
		}
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'pageflag' => $p['f'],
			'pagetime' => $p['t'],
			'reqnum' => $p['n']
		);
	 	return $this->oauth->get($url,$params);		
	}	

	/******************
	*搜索
	*@k:搜索关键字
	*@n: 每页大小
	*@p: 页码
	*@type : 0 用户 1 消息 2 话题 
	**********************/	
	public function getSearch($p){
		switch($p['type']){
			case 0:
				$url = 'http://open.t.qq.com/api/search/user?f=1';
				break;
			case 1:
				$url = 'http://open.t.qq.com/api/search/t?f=1';
				break;
			case 2:
				$url = 'http://open.t.qq.com/api/search/ht?f=1';
				break;
			default:
				$url = 'http://open.t.qq.com/api/search/t?f=1';
				break;
		}		

		$params = array(
			'format' => MB_RETURN_FORMAT,
			'keyword' => $p['k'],
			'pagesize' => $p['n'],
			'page' => $p['p']
		);
	 	return $this->oauth->get($url,$params);		
	}	

	/******************
	*热门话题
	*@type: 请求类型 1 话题名，2 搜索关键字 3 两种类型都有
	*@n: 请求个数（最多20）
	*@Pos :请求位置，第一次请求时填0，继续填上次返回的POS
	**********************/	
	public function getHotTopic($p){
		$url = 'http://open.t.qq.com/api/trends/ht?f=1';
		if($p['type']<1 || $p['type']>3){
			$p['type'] = 1;
		}
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'type' => $p['type'],
			'reqnum' => $p['n'],
			'pos' => $p['pos']
		);
	 	return $this->oauth->get($url,$params);		
	}			

	/******************
	*查看数据更新条数
	*@op :请求类型 0：只请求更新数，不清除更新数，1：请求更新数，并对更新数清零
	*@type：5 首页未读消息记数，6 @页消息记数 7 私信页消息计数 8 新增粉丝数 9 首页广播数（原创的）
	**********************/	
	public function getUpdate($p){
		$url = 'http://open.t.qq.com/api/info/update?f=1';
		if(isset($p['type'])){
			if($p['op']){
				$params = array(
					'format' => MB_RETURN_FORMAT,
					'op' => $p['op'],
					'type' => $p['type']
				);			
			}else{
				$params = array(
					'format' => MB_RETURN_FORMAT,
					'op' => $p['op']
				);			
			}
		}else{
			$params = array(
				'format' => MB_RETURN_FORMAT,
				'op' => $p['op']
			);
		}
	 	return $this->oauth->get($url,$params);		
	}	

	/******************
	*添加/删除 收藏的微博
	*@id : 微博id
	*@type：1 添加 0 删除
	**********************/	
	public function postFavMsg($p){
		if($p['type']){
			$url = 'http://open.t.qq.com/api/fav/addt?f=1';
		}else{
			$url = 'http://open.t.qq.com/api/fav/delt?f=1';
		}
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'id' => $p['id']
		);
	 	return $this->oauth->post($url,$params);		
	}

	/******************
	*添加/删除 收藏的话题
	*@id : 微博id
	*@type：1 添加 0 删除
	**********************/	
	public function postFavTopic($p){
		if($p['type']){
			$url = 'http://open.t.qq.com/api/fav/addht?f=1';
		}else{
			$url = 'http://open.t.qq.com/api/fav/delht?f=1';
		}
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'id' => $p['id']
		);
	 	return $this->oauth->post($url,$params);		
	}	

	/******************
	*获取收藏的内容
	*******话题
	n:请求数，最多15
	f:翻页标识  0：首页   1：向下翻页 2：向上翻页
	t:翻页时间戳0
	lid:翻页话题ID，第次请求时为0
	*******消息
	f 分页标识（0：第一页，1：向下翻页，2向上翻页）
	t: 本页起始时间（第一页 0，继续：根据返回记录时间决定）
	n: 每次请求记录的条数（1-20条）
	*@type 0 收藏的消息  1 收藏的话题
	**********************/	
	public function getFav($p){
		if($p['type']){
			$url = 'http://open.t.qq.com/api/fav/list_ht?f=1';
			$params = array(
				'format' => MB_RETURN_FORMAT,
				'reqnum' => $p['n'],		
				'pageflag' => $p['f'],		
				'pagetime' => $p['t'],		
				'lastid' => $p['lid']		
				);
		}else{
			$url = 'http://open.t.qq.com/api/fav/list_t?f=1';	
			$params = array(
				'format' => MB_RETURN_FORMAT,
				'reqnum' => $p['n'],		
				'pageflag' => $p['f'],		
				'pagetime' => $p['t']		
				);
		}
	 	return $this->oauth->get($url,$params);		
	}

	/******************
	*获取话题id
	*@list: 话题名字列表（abc,efg,）
	**********************/	
	public function getTopicId($p){
			$url = 'http://open.t.qq.com/api/ht/ids?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'httexts' => $p['list']
		);
	 	return $this->oauth->get($url,$params);		
	}	

	/******************
	*获取话题内容
	*@list: 话题id列表（abc,efg,）
	**********************/	
	public function getTopicList($p){
			$url = 'http://open.t.qq.com/api/ht/info?f=1';
		$params = array(
			'format' => MB_RETURN_FORMAT,
			'ids' => $p['list']
		);
	 	return $this->oauth->get($url,$params);		
	}		
}

class MBOpenTOAuth {
	public $host = 'http://open.t.qq.com/';
	public $timeout = 30; 
	public $connectTimeout = 30;
	public $sslVerifypeer = FALSE; 
	public $format = MB_RETURN_FORMAT;
	public $decodeJson = TRUE; 
	public $httpInfo; 
	public $userAgent = 'oauth test'; 
	public $decode_json = FALSE; 

    function accessTokenURL()  { return 'https://open.t.qq.com/cgi-bin/access_token'; } 
    function authenticateURL() { return 'http://open.t.qq.com/cgi-bin/authenticate'; } 
    function authorizeURL()    { return 'http://open.t.qq.com/cgi-bin/authorize'; } 
	function requestTokenURL() { return 'https://open.t.qq.com/cgi-bin/request_token'; } 

	function lastStatusCode() { return $this->http_status; } 

    function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) { 
        $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1(); 
        $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret); 
        if (!empty($oauth_token) && !empty($oauth_token_secret)) { 
            $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret); 
        } else { 
            $this->token = NULL; 
        } 
	}

    /** 
     * oauth授权之后的回调页面 
	 * 返回包含 oauth_token 和oauth_token_secret的key/value数组
     */ 
    function getRequestToken($oauth_callback = NULL) { 
        $parameters = array(); 
        if (!empty($oauth_callback)) { 
            $parameters['oauth_callback'] = $oauth_callback; 
        }  

        $request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters); 
		$token = OAuthUtil::parse_parameters($request); 
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); 
        return $token; 
    } 

    /** 
     * 获取授权url
     * @return string 
     */ 
    function getAuthorizeURL($token, $signInWithWeibo = TRUE , $url='') { 
        if (is_array($token)) { 
            $token = $token['oauth_token']; 
        } 
        if (empty($signInWithWeibo)) { 
            return $this->authorizeURL() . "?oauth_token={$token}"; 
        } else { 
            return $this->authenticateURL() . "?oauth_token={$token}"; 
        } 
	} 	

    /** 
	* 交换授权
	* Exchange the request token and secret for an access token and 
     * secret, to sign API calls. 
     * 
     * @return array array("oauth_token" => the access token, 
     *                "oauth_token_secret" => the access secret) 
     */ 
    function getAccessToken($oauth_verifier = FALSE, $oauth_token = false) { 
        $parameters = array(); 
        if (!empty($oauth_verifier)) { 
            $parameters['oauth_verifier'] = $oauth_verifier; 
        } 
		$request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters);
		$token = OAuthUtil::parse_parameters($request); 
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); 
        return $token; 
	} 

	function jsonDecode($response, $assoc=true)	{
		$response = preg_replace('/[^\x20-\xff]*/', "", $response);	
		$jsonArr = json_decode($response, $assoc);
		if(!is_array($jsonArr))
		{
			throw new Exception('格式错误!');
		}
		$ret = $jsonArr["ret"];
		$msg = $jsonArr["msg"];
		/**
		 *Ret=0 成功返回
		 *Ret=1 参数错误
		 *Ret=2 频率受限
		 *Ret=3 鉴权失败
		 *Ret=4 服务器内部错误
		 */
		switch ($ret) {
			case 0:
				return $jsonArr;;
				break;
			case 1:
				throw new Exception('参数错误!');
				break;
			case 2:
				throw new Exception('频率受限!');
				break;
			case 3:
				throw new Exception('鉴权失败!');
				break;
			default:
				$errcode = $jsonArr["errcode"];
				if(isset($errcode))			//统一提示发表失败
				{
					throw new Exception("发表失败");
					break;
					//require_once MB_COMM_DIR.'/api_errcode.class.php';
					//$msg = ApiErrCode::getMsg($errcode);
				}
				throw new Exception('服务器内部错误!');
				break;
		}
	}
	
    /** 
     * 重新封装的get请求. 
     * @return mixed 
     */ 
    function get($url, $parameters) { 
		$response = $this->oAuthRequest($url, 'GET', $parameters); 
		if (MB_RETURN_FORMAT === 'json') { 
            return $this->jsonDecode($response, true);
		}
        return $response; 
	}

	 /** 
     * 重新封装的post请求. 
     * @return mixed 
     */ 
    function post($url, $parameters = array() , $multi = false) { 
        $response = $this->oAuthRequest($url, 'POST', $parameters , $multi ); 
		if (MB_RETURN_FORMAT === 'json') { 
            return $this->jsonDecode($response, true); 
        } 
        return $response; 
	}

	 /** 
     * DELTE wrapper for oAuthReqeust. 
     * @return mixed 
     */ 
    function delete($url, $parameters = array()) { 
        $response = $this->oAuthRequest($url, 'DELETE', $parameters); 
		if (MB_RETURN_FORMAT === 'json') { 
            return $this->jsonDecode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * 发送请求的具体类
     * @return string 
     */ 
    function oAuthRequest($url, $method, $parameters , $multi = false) { 
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) { 
            $url = "{$this->host}{$url}.{$this->format}"; 
		}
        $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters); 
		$request->sign_request($this->sha1_method, $this->consumer, $this->token);
        switch ($method) { 
        case 'GET': 
            return $this->http($request->to_url(), 'GET'); 
        default: 
            return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata($multi) , $multi ); 
        } 
	}     

	function http($url, $method, $postfields = NULL , $multi = false){
		//$https = 0;
		//判断是否是https请求
		if(strrpos($url, 'https://')===0){
			$port = 443;
			$version = '1.1';
			$host = 'ssl://'.MB_API_HOST;	
			
		}else{
			$port = 80;	
			$version = '1.0';
			$host = MB_API_HOST;
		}

		$header = "$method $url HTTP/$version\r\n";	
		$header .= "Host: ".MB_API_HOST."\r\n";
		if($multi){
			$header .= "Content-Type: multipart/form-data; boundary=" . OAuthUtil::$boundary . "\r\n";	
		}else{	
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";  
		}
		if(strtolower($method) == 'post' ){
			$header .= "Content-Length: ".strlen($postfields)."\r\n";
			$header .= "Connection: Close\r\n\r\n";  
			$header .= $postfields;
		}else{
			$header .= "Connection: Close\r\n\r\n";  
		}

		$ret = '';
		
		$fp = fsockopen($host,$port,$errno,$errstr,30);

		if(!$fp){
			$error = '建立sock连接失败';
			throw new Exception($error);
		}else{
			fwrite ($fp, $header);  
			while (!feof($fp)) {
				$ret .= fgets($fp, 4096);
			}
			fclose($fp);
			if(strrpos($ret,'Transfer-Encoding: chunked')){
				//changed by lxy.mobi
				//$info = split("\r\n\r\n",$ret);
				//$response = split("\r\n",$info[1]);
				$info = explode("\r\n\r\n",$ret);
				$response = explode("\r\n",$info[1]);
				$t = array_slice($response,1,-1);

				$returnInfo = implode('',$t);
			}else{
				//changed by lxy.mobi
				//$response = split("\r\n\r\n",$ret);
				$response = explode("\r\n\r\n",$ret);
				$returnInfo = $response[1];
			}
			//转成utf-8编码
			return iconv("utf-8","utf-8//ignore",$returnInfo);
		}
		
	}
 

	/*
	使用curl库的请求函数,可以根据实际情况使用
	function http($url, $method, $postfields = NULL , $multi = false){
        $this->http_info = array(); 
        $ci = curl_init(); 
        curl_setopt($ci, CURLOPT_USERAGENT, $this->userAgent); 
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout); 
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout); 
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->sslVerifypeer); 
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader')); 
        curl_setopt($ci, CURLOPT_HEADER, FALSE); 

        switch ($method) { 
        case 'POST': 
            curl_setopt($ci, CURLOPT_POST, TRUE); 
            if (!empty($postfields)) { 
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields); 
            } 
            break; 
        case 'DELETE': 
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
            if (!empty($postfields)) { 
                $url = "{$url}?{$postfields}"; 
            } 
        } 

        $header_array = array(); 
        $header_array2=array(); 
        if( $multi ) 
        	$header_array2 = array("Content-Type: multipart/form-data; boundary=" . OAuthUtil::$boundary , "Expect: ");
        foreach($header_array as $k => $v) 
            array_push($header_array2,$k.': '.$v); 

        curl_setopt($ci, CURLOPT_HTTPHEADER, $header_array2 ); 
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE ); 

        curl_setopt($ci, CURLOPT_URL, $url); 

        $response = curl_exec($ci); 
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE); 
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci)); 
        $this->url = $url; 
		print_r($response);	
        curl_close ($ci); 
        return $response; 

	}*/
	
    function getHeader($ch, $header) { 
        $i = strpos($header, ':'); 
        if (!empty($i)) { 
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i))); 
            $value = trim(substr($header, $i + 2)); 
            $this->http_header[$key] = $value; 
        } 
        return strlen($header); 
	} 
}

/**
 * oauth相关类
 * @param 
 * @return
 * @author tuguska
 */

class OAuthConsumer { 
    public $key; 
    public $secret; 

    function __construct($key, $secret) { 
        $this->key = $key; 
        $this->secret = $secret; 
    } 

    function __toString() { 
        return "OAuthConsumer[key=$this->key,secret=$this->secret]"; 
    } 
} 

class OAuthToken { 
    public $key; 
    public $secret; 

    function __construct($key, $secret) { 
        $this->key = $key; 
        $this->secret = $secret; 
    } 

    /** 
	 * 
	 * generates the basic string serialization of a token that a server 
     * would respond to request_token and access_token calls with 
     */ 
    function to_string() { 
        return "oauth_token=" . 
            OAuthUtil::urlencode_rfc3986($this->key) . 
            "&oauth_token_secret=" . 
            OAuthUtil::urlencode_rfc3986($this->secret); 
    } 

    function __toString() { 
        return $this->to_string(); 
    } 
}

//oauth签名方法
class OAuthSignatureMethod { 
    public function check_signature(&$request, $consumer, $token, $signature) { 
        $built = $this->build_signature($request, $consumer, $token); 
        return $built == $signature; 
    } 
}
class OAuthSignatureMethod_HMAC_SHA1 extends OAuthSignatureMethod { 
    function get_name() { 
        return "HMAC-SHA1"; 
    } 

    public function build_signature($request, $consumer, $token) { 
		$base_string = $request->get_signature_base_string(); 
        $request->base_string = $base_string; 
        $key_parts = array( 
            $consumer->secret, 
            ($token) ? $token->secret : "" 
        ); 
		
		$key_parts = OAuthUtil::urlencode_rfc3986($key_parts); 

		$key = implode('&', $key_parts); 
        return base64_encode(hash_hmac('sha1', $base_string, $key, true)); 
    } 
} 

class OAuthSignatureMethod_PLAINTEXT extends OAuthSignatureMethod { 
    public function get_name() { 
        return "PLAINTEXT"; 
    } 

    public function build_signature($request, $consumer, $token) { 
        $sig = array( 
            OAuthUtil::urlencode_rfc3986($consumer->secret) 
        ); 

        if ($token) { 
            array_push($sig, OAuthUtil::urlencode_rfc3986($token->secret)); 
        } else { 
            array_push($sig, ''); 
        } 

        $raw = implode("&", $sig); 
        // for debug purposes 
        $request->base_string = $raw; 

        return OAuthUtil::urlencode_rfc3986($raw); 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthSignatureMethod_RSA_SHA1 extends OAuthSignatureMethod { 
    public function get_name() { 
        return "RSA-SHA1"; 
    } 

    protected function fetch_public_cert(&$request) { 
        // not implemented yet, ideas are: 
        // (1) do a lookup in a table of trusted certs keyed off of consumer 
        // (2) fetch via http using a url provided by the requester 
        // (3) some sort of specific discovery code based on request 
        // 
        // either way should return a string representation of the certificate 
        throw Exception("fetch_public_cert not implemented"); 
    } 

    protected function fetch_private_cert(&$request) { 
        // not implemented yet, ideas are: 
        // (1) do a lookup in a table of trusted certs keyed off of consumer 
        // 
        // either way should return a string representation of the certificate 
        throw Exception("fetch_private_cert not implemented"); 
    } 

    public function build_signature(&$request, $consumer, $token) { 
        $base_string = $request->get_signature_base_string(); 
        $request->base_string = $base_string; 

        // Fetch the private key cert based on the request 
        $cert = $this->fetch_private_cert($request); 

        // Pull the private key ID from the certificate 
        $privatekeyid = openssl_get_privatekey($cert); 

        // Sign using the key 
        $ok = openssl_sign($base_string, $signature, $privatekeyid); 

        // Release the key resource 
        openssl_free_key($privatekeyid); 

        return base64_encode($signature); 
    } 

    public function check_signature(&$request, $consumer, $token, $signature) { 
        $decoded_sig = base64_decode($signature); 

        $base_string = $request->get_signature_base_string(); 

        // Fetch the public key cert based on the request 
        $cert = $this->fetch_public_cert($request); 

        // Pull the public key ID from the certificate 
        $publickeyid = openssl_get_publickey($cert); 

        // Check the computed signature against the one passed in the query 
        $ok = openssl_verify($base_string, $decoded_sig, $publickeyid); 

        // Release the key resource 
        openssl_free_key($publickeyid); 

        return $ok == 1; 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthRequest { 
    public $parameters; 
    private $http_method; 
    private $http_url; 
    // for debug purposes 
    public $base_string; 
    public static $version = '1.0'; 
    public static $POST_INPUT = 'php://input'; 

    function __construct($http_method, $http_url, $parameters=NULL) { 
        @$parameters or $parameters = array(); 
        $this->parameters = $parameters; 
        $this->http_method = $http_method; 
        $this->http_url = $http_url; 
    } 


    /** 
     * attempt to build up a request from what was passed to the server 
     */ 
    public static function from_request($http_method=NULL, $http_url=NULL, $parameters=NULL) { 
        $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") 
            ? 'http' 
            : 'https'; 
        @$http_url or $http_url = $scheme . 
            '://' . $_SERVER['HTTP_HOST'] . 
            ':' . 
            $_SERVER['SERVER_PORT'] . 
            $_SERVER['REQUEST_URI']; 
        @$http_method or $http_method = $_SERVER['REQUEST_METHOD']; 

        // We weren't handed any parameters, so let's find the ones relevant to 
        // this request. 
        // If you run XML-RPC or similar you should use this to provide your own 
        // parsed parameter-list 
        if (!$parameters) { 
            // Find request headers 
            $request_headers = OAuthUtil::get_headers(); 

            // Parse the query-string to find GET parameters 
            $parameters = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']); 

            // It's a POST request of the proper content-type, so parse POST 
            // parameters and add those overriding any duplicates from GET 
            if ($http_method == "POST" 
                && @strstr($request_headers["Content-Type"], 
                    "application/x-www-form-urlencoded") 
            ) { 
                $post_data = OAuthUtil::parse_parameters( 
                    file_get_contents(self::$POST_INPUT) 
                ); 
                $parameters = array_merge($parameters, $post_data); 
            } 

            // We have a Authorization-header with OAuth data. Parse the header 
            // and add those overriding any duplicates from GET or POST 
            if (@substr($request_headers['Authorization'], 0, 6) == "OAuth ") { 
                $header_parameters = OAuthUtil::split_header( 
                    $request_headers['Authorization'] 
                ); 
                $parameters = array_merge($parameters, $header_parameters); 
            } 

        } 

        return new OAuthRequest($http_method, $http_url, $parameters); 
    } 

    /** 
     * pretty much a helper function to set up the request 
     */ 
    public static function from_consumer_and_token($consumer, $token, $http_method, $http_url, $parameters=NULL) { 
		@$parameters or $parameters = array();
        $defaults = array("oauth_version" => OAuthRequest::$version, 
            "oauth_nonce" => OAuthRequest::generate_nonce(), 
            "oauth_timestamp" => OAuthRequest::generate_timestamp(), 
            "oauth_consumer_key" => $consumer->key); 
        if ($token) 
            $defaults['oauth_token'] = $token->key; 

        $parameters = array_merge($defaults, $parameters); 
		//unset($parameters['pic']);
        return new OAuthRequest($http_method, $http_url, $parameters); 
    } 

    public function set_parameter($name, $value, $allow_duplicates = true) { 
        if ($allow_duplicates && isset($this->parameters[$name])) { 
            // We have already added parameter(s) with this name, so add to the list 
            if (is_scalar($this->parameters[$name])) { 
                // This is the first duplicate, so transform scalar (string) 
                // into an array so we can add the duplicates 
                $this->parameters[$name] = array($this->parameters[$name]); 
            } 

            $this->parameters[$name][] = $value; 
        } else { 
            $this->parameters[$name] = $value; 
        } 
    } 

    public function get_parameter($name) { 
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null; 
    } 

    public function get_parameters() { 
        return $this->parameters; 
    } 

    public function unset_parameter($name) { 
        unset($this->parameters[$name]); 
    } 

    /** 
     * The request parameters, sorted and concatenated into a normalized string. 
     * @return string 
     */ 
    public function get_signable_parameters() { 
        // Grab all parameters 
        $params = $this->parameters; 
        
        // remove pic 
        if (isset($params['pic'])) { 
            unset($params['pic']); 
        }
        
          if (isset($params['image'])) 
         { 
            unset($params['image']); 
        }

        // Remove oauth_signature if present 
        // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.") 
        if (isset($params['oauth_signature'])) { 
            unset($params['oauth_signature']); 
        } 

        return OAuthUtil::build_http_query($params); 
    } 

    /** 
     * Returns the base string of this request 
     * 
     * The base string defined as the method, the url 
     * and the parameters (normalized), each urlencoded 
     * and the concated with &. 
     */ 
    public function get_signature_base_string() { 
        $parts = array( 
            $this->get_normalized_http_method(), 
            $this->get_normalized_http_url(), 
            $this->get_signable_parameters() 
        ); 
        
        //print_r( $parts );

        $parts = OAuthUtil::urlencode_rfc3986($parts); 
        return implode('&', $parts); 
    } 

    /** 
     * just uppercases the http method 
     */ 
    public function get_normalized_http_method() { 
        return strtoupper($this->http_method); 
    } 

    /** 
     * parses the url and rebuilds it to be 
     * scheme://host/path 
     */ 
    public function get_normalized_http_url() { 
        $parts = parse_url($this->http_url); 

        $port = @$parts['port']; 
        $scheme = $parts['scheme']; 
        $host = $parts['host']; 
        $path = @$parts['path']; 

        $port or $port = ($scheme == 'https') ? '443' : '80'; 

        if (($scheme == 'https' && $port != '443') 
            || ($scheme == 'http' && $port != '80')) { 
                $host = "$host:$port"; 
            } 
        return "$scheme://$host$path"; 
    } 

    /** 
     * builds a url usable for a GET request 
     */ 
    public function to_url() { 
        $post_data = $this->to_postdata(); 
        $out = $this->get_normalized_http_url(); 
        if ($post_data) { 
            $out .= '?'.$post_data; 
        } 
        return $out; 
    } 

    /** 
     * builds the data one would send in a POST request 
     */ 
    public function to_postdata( $multi = false ) {
    if( $multi )
    	return OAuthUtil::build_http_query_multi($this->parameters); 
    else 
        return OAuthUtil::build_http_query($this->parameters); 
    } 

    /** 
     * builds the Authorization: header 
     */ 
    public function to_header() { 
        $out ='Authorization: OAuth realm=""'; 
        $total = array(); 
        foreach ($this->parameters as $k => $v) { 
            if (substr($k, 0, 5) != "oauth") continue; 
            if (is_array($v)) { 
                throw new MBOAuthExcep('Arrays not supported in headers'); 
            } 
            $out .= ',' . 
                OAuthUtil::urlencode_rfc3986($k) . 
                '="' . 
                OAuthUtil::urlencode_rfc3986($v) . 
                '"'; 
        } 
        return $out; 
    } 

    public function __toString() { 
        return $this->to_url(); 
    } 


	public function sign_request($signature_method, $consumer, $token) { 
        $this->set_parameter( 
            "oauth_signature_method", 
            $signature_method->get_name(), 
            false 
		);
		$signature = $this->build_signature($signature_method, $consumer, $token); 
		$this->set_parameter("oauth_signature", $signature, false); 
    } 

    public function build_signature($signature_method, $consumer, $token) { 
        $signature = $signature_method->build_signature($this, $consumer, $token); 
        return $signature; 
    } 

    /** 
     * util function: current timestamp 
     */ 
    private static function generate_timestamp() { 
		return time(); 
    } 

    /** 
     * util function: current nonce 
     */ 
    private static function generate_nonce() { 
		$mt = microtime(); 
        $rand = mt_rand(); 

        return md5($mt . $rand); // md5s look nicer than numbers 
    } 
} 

/** 
 * @ignore 
 */ 
class OAuthServer { 
    protected $timestamp_threshold = 300; // in seconds, five minutes 
    protected $version = 1.0;             // hi blaine 
    protected $signature_methods = array(); 

    protected $data_store; 

    function __construct($data_store) { 
        $this->data_store = $data_store; 
    } 

    public function add_signature_method($signature_method) { 
        $this->signature_methods[$signature_method->get_name()] = 
            $signature_method; 
    } 

    // high level functions 

    /** 
     * process a request_token request 
     * returns the request token on success 
     */ 
    public function fetch_request_token(&$request) { 
        $this->get_version($request); 

        $consumer = $this->get_consumer($request); 

        // no token required for the initial token request 
        $token = NULL; 

        $this->check_signature($request, $consumer, $token); 

        $new_token = $this->data_store->new_request_token($consumer); 

        return $new_token; 
    } 

    /** 
     * process an access_token request 
     * returns the access token on success 
     */ 
    public function fetch_access_token(&$request) { 
        $this->get_version($request); 

        $consumer = $this->get_consumer($request); 

        // requires authorized request token 
        $token = $this->get_token($request, $consumer, "request"); 


        $this->check_signature($request, $consumer, $token); 

        $new_token = $this->data_store->new_access_token($token, $consumer); 

        return $new_token; 
    } 

    /** 
     * verify an api call, checks all the parameters 
     */ 
    public function verify_request(&$request) { 
        $this->get_version($request); 
        $consumer = $this->get_consumer($request); 
        $token = $this->get_token($request, $consumer, "access"); 
        $this->check_signature($request, $consumer, $token); 
        return array($consumer, $token); 
    } 

    // Internals from here 
    /** 
     * version 1 
     */ 
    private function get_version(&$request) { 
        $version = $request->get_parameter("oauth_version"); 
        if (!$version) { 
            $version = 1.0; 
        } 
        if ($version && $version != $this->version) { 
            throw new MBOAuthExcep("OAuth version '$version' not supported"); 
        } 
        return $version; 
    } 

    /** 
     * figure out the signature with some defaults 
     */ 
    private function get_signature_method(&$request) { 
        $signature_method = 
            @$request->get_parameter("oauth_signature_method"); 
        if (!$signature_method) { 
            $signature_method = "PLAINTEXT"; 
        } 
        
        if (!in_array($signature_method, 
            array_keys($this->signature_methods))) { 
                throw new MBOAuthExcep( 
                    "Signature method '$signature_method' not supported " . 
                    "try one of the following: " . 
                    implode(", ", array_keys($this->signature_methods)) 
                ); 
            } 
        return $this->signature_methods[$signature_method]; 
    } 

    /** 
     * try to find the consumer for the provided request's consumer key 
     */ 
    private function get_consumer(&$request) { 
        $consumer_key = @$request->get_parameter("oauth_consumer_key"); 
        if (!$consumer_key) { 
            throw new MBOAuthExcep("Invalid consumer key"); 
        } 

        $consumer = $this->data_store->lookup_consumer($consumer_key); 
        if (!$consumer) { 
            throw new MBOAuthExcep("Invalid consumer"); 
        } 

        return $consumer; 
    } 

    /** 
     * try to find the token for the provided request's token key 
     */ 
    private function get_token(&$request, $consumer, $token_type="access") { 
        $token_field = @$request->get_parameter('oauth_token'); 
        $token = $this->data_store->lookup_token( 
            $consumer, $token_type, $token_field 
        ); 
        if (!$token) { 
            throw new MBOAuthExcep("Invalid $token_type token: $token_field"); 
        } 
        return $token; 
    } 

    /** 
     * all-in-one function to check the signature on a request 
     * should guess the signature method appropriately 
     */ 
    private function check_signature(&$request, $consumer, $token) { 
        // this should probably be in a different method 
        $timestamp = @$request->get_parameter('oauth_timestamp'); 
        $nonce = @$request->get_parameter('oauth_nonce'); 

        $this->check_timestamp($timestamp); 
        $this->check_nonce($consumer, $token, $nonce, $timestamp); 

        $signature_method = $this->get_signature_method($request); 

        $signature = $request->get_parameter('oauth_signature'); 
        $valid_sig = $signature_method->check_signature( 
            $request, 
            $consumer, 
            $token, 
            $signature 
        ); 

        if (!$valid_sig) { 
            throw new MBOAuthExcep("Invalid signature"); 
        } 
    } 

    /** 
     * check that the timestamp is new enough 
     */ 
    private function check_timestamp($timestamp) { 
        // verify that timestamp is recentish 
        $now = time(); 
        if ($now - $timestamp > $this->timestamp_threshold) { 
            throw new MBOAuthExcep( 
                "Expired timestamp, yours $timestamp, ours $now" 
            ); 
        } 
    } 

    /** 
     * check that the nonce is not repeated 
     */ 
    private function check_nonce($consumer, $token, $nonce, $timestamp) { 
        // verify that the nonce is uniqueish 
        $found = $this->data_store->lookup_nonce( 
            $consumer, 
            $token, 
            $nonce, 
            $timestamp 
        ); 
        if ($found) { 
            throw new MBOAuthExcep("Nonce already used: $nonce"); 
        } 
    } 

} 

/** 
 * @ignore 
 */ 
class OAuthDataStore { 
    function lookup_consumer($consumer_key) { 
        // implement me 
    } 

    function lookup_token($consumer, $token_type, $token) { 
        // implement me 
    } 

    function lookup_nonce($consumer, $token, $nonce, $timestamp) { 
        // implement me 
    } 

    function new_request_token($consumer) { 
        // return a new token attached to this consumer 
    } 

    function new_access_token($token, $consumer) { 
        // return a new access token attached to this consumer 
        // for the user associated with this token if the request token 
        // is authorized 
        // should also invalidate the request token 
    } 

} 
class OAuthDataApi extends OAuthDataStore
{
  
}



/** 
 * @ignore 
 */ 
class OAuthUtil { 

	public static $boundary = '';

    public static function urlencode_rfc3986($input) { 
        if (is_array($input)) { 
            return array_map(array('OAuthUtil', 'urlencode_rfc3986'), $input); 
        } else if (is_scalar($input)) { 
            return str_replace( 
                '+', 
                ' ', 
                str_replace('%7E', '~', rawurlencode($input)) 
            ); 
        } else { 
            return ''; 
        } 
    } 


    // This decode function isn't taking into consideration the above 
    // modifications to the encoding process. However, this method doesn't 
    // seem to be used anywhere so leaving it as is. 
    public static function urldecode_rfc3986($string) { 
        return urldecode($string); 
    } 

    // Utility function for turning the Authorization: header into 
    // parameters, has to do some unescaping 
    // Can filter out any non-oauth parameters if needed (default behaviour) 
    public static function split_header($header, $only_allow_oauth_parameters = true) { 
        $pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/'; 
        $offset = 0; 
        $params = array(); 
        while (preg_match($pattern, $header, $matches, PREG_OFFSET_CAPTURE, $offset) > 0) { 
            $match = $matches[0]; 
            $header_name = $matches[2][0]; 
            $header_content = (isset($matches[5])) ? $matches[5][0] : $matches[4][0]; 
            if (preg_match('/^oauth_/', $header_name) || !$only_allow_oauth_parameters) { 
                $params[$header_name] = OAuthUtil::urldecode_rfc3986($header_content); 
            } 
            $offset = $match[1] + strlen($match[0]); 
        } 

        if (isset($params['realm'])) { 
            unset($params['realm']); 
        } 

        return $params; 
    } 

    // helper to try to sort out headers for people who aren't running apache 
    public static function get_headers() { 
        if (function_exists('apache_request_headers')) { 
            // we need this to get the actual Authorization: header 
            // because apache tends to tell us it doesn't exist 
            return apache_request_headers(); 
        } 
        // otherwise we don't have apache and are just going to have to hope 
        // that $_SERVER actually contains what we need 
        $out = array(); 
        foreach ($_SERVER as $key => $value) { 
            if (substr($key, 0, 5) == "HTTP_") { 
                // this is chaos, basically it is just there to capitalize the first 
                // letter of every word that is not an initial HTTP and strip HTTP 
                // code from przemek 
                $key = str_replace( 
                    " ", 
                    "-", 
                    ucwords(strtolower(str_replace("_", " ", substr($key, 5)))) 
                ); 
                $out[$key] = $value; 
            } 
        } 
        return $out; 
    } 

    // This function takes a input like a=b&a=c&d=e and returns the parsed 
    // parameters like this 
    // array('a' => array('b','c'), 'd' => 'e') 
    public static function parse_parameters( $input ) { 
        if (!isset($input) || !$input) return array(); 

        $pairs = explode('&', $input); 

        $parsed_parameters = array(); 
        foreach ($pairs as $pair) { 
            $split = explode('=', $pair, 2); 
            $parameter = OAuthUtil::urldecode_rfc3986($split[0]); 
            $value = isset($split[1]) ? OAuthUtil::urldecode_rfc3986($split[1]) : ''; 

            if (isset($parsed_parameters[$parameter])) { 
                // We have already recieved parameter(s) with this name, so add to the list 
                // of parameters with this name 
                if (is_scalar($parsed_parameters[$parameter])) { 
                    // This is the first duplicate, so transform scalar (string) into an array 
                    // so we can add the duplicates 
                    $parsed_parameters[$parameter] = array($parsed_parameters[$parameter]); 
                } 
                $parsed_parameters[$parameter][] = $value; 
            } else { 
                $parsed_parameters[$parameter] = $value; 
            } 
        } 
        return $parsed_parameters; 
    } 
    
    public static function build_http_query_multi($params) { 
        if (!$params) return ''; 
		
		//print_r( $params );
		//return null;
        
        // Urlencode both keys and values 
        $keys = array_keys($params);
        $values = array_values($params);
        //$keys = OAuthUtil::urlencode_rfc3986(array_keys($params)); 
        //$values = OAuthUtil::urlencode_rfc3986(array_values($params)); 
        $params = array_combine($keys, $values); 

        // Parameters are sorted by name, using lexicographical byte value ordering. 
        // Ref: Spec: 9.1.1 (1) 
        uksort($params, 'strcmp'); 

        $pairs = array(); 
        
        self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

        foreach ($params as $parameter => $value) { 
			//if( $parameter == 'pic' && $value{0} == '@' )
			if( in_array($parameter,array("pic","image")) )
			{
				/*
				$tmp = 'E:\qweibo_proj\trunk\1.txt';
				$url = ltrim($tmp,'@');
				$content = file_get_contents( $url );
				$filename = reset( explode( '?' , basename( $url ) ));
				$mime = self::get_image_mime($url); 
				*/
				//$url = ltrim( $value , '@' );
				$content = $value[2];//file_get_contents( $url );
				$filename = $value[1];//reset( explode( '?' , basename( $url ) ));
				$mime = $value[0];//self::get_image_mime($url); 
				
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= 'Content-Type: '. $mime . "\r\n\r\n";
				$multipartbody .= $content. "\r\n";
			}
			else
			{
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="'.$parameter."\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
				
			}    
        } 
        
        $multipartbody .=  "$endMPboundary\r\n";
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61) 
        // Each name-value pair is separated by an '&' character (ASCII code 38) 
        return $multipartbody; 
    } 

    public static function build_http_query($params) { 
        if (!$params) return ''; 

        // Urlencode both keys and values 
        $keys = OAuthUtil::urlencode_rfc3986(array_keys($params)); 
        $values = OAuthUtil::urlencode_rfc3986(array_values($params)); 
        $params = array_combine($keys, $values); 

        // Parameters are sorted by name, using lexicographical byte value ordering. 
        // Ref: Spec: 9.1.1 (1) 
        uksort($params, 'strcmp'); 

        $pairs = array(); 
        foreach ($params as $parameter => $value) { 
            if (is_array($value)) { 
                // If two or more parameters share the same name, they are sorted by their value 
                // Ref: Spec: 9.1.1 (1) 
                natsort($value); 
                foreach ($value as $duplicate_value) { 
                    $pairs[] = $parameter . '=' . $duplicate_value; 
                } 
            } else { 
                $pairs[] = $parameter . '=' . $value; 
            } 
        } 
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61) 
        // Each name-value pair is separated by an '&' character (ASCII code 38) 
        return implode('&', $pairs); 
    } 
    
    public static function get_image_mime( $file )
    {
    	$ext = strtolower(pathinfo( $file , PATHINFO_EXTENSION ));
    	switch( $ext )
    	{
    		case 'jpg':
    		case 'jpeg':
    			$mime = 'image/jpg';
    			break;
    		 	
    		case 'png';
    			$mime = 'image/png';
    			break;
    			
    		case 'gif';
    		default:
    			$mime = 'image/gif';
    			break;    		
    	}
    	return $mime;
    }
} 
?>
