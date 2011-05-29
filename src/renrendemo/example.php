<?php
/*
 * 调用人人网RESTful API的范例，本类需要继承RESTClient类方可使用
 * 要求最低的PHP版本是5.2.0，并且还要支持以下库：cURL, Libxml 2.6.0
 * This example for invoke RenRen RESTful Webservice
 * It MUST be extends RESTClient
 * The requirement of PHP version is 5.2.0 or above, and support as below:
 * cURL, Libxml 2.6.0
 *
 * @Version: 0.0.2 alpha
 * @Created: 0:11:39 2010/11/25
 * @Author:	Edison tsai<dnsing@gmail.com>
 * @Blog:	http://www.timescode.com
 * @Link:	http://www.dianboom.com
 */

require_once 'RenRenClient.class.php';

$rrObj = new RenRenClient;

/*
 *@获取指定用户的信息
 *@POST暂时有两个参数，第一个是需要调用的方法，具体的方法跟人人网的API一致，注意区分大小写
 *@第二个参数是一维数组，顺序排列必须跟config.inc.php文件中的$config->APIMapping设置一样，否则会出现异常
 */


/*
 @Setting session_key example(设置session_key的例子)
 @Don't need to setSessionKey when you are logged in
 @如果你登录了就不需要设置session_key，RenRenClient类会自行获取，以下方法只是为了自由扩展而设置的
*/
$rrObj->setSessionKey('3.c149c48e2c18d48c0110434f3189e070.21600.1294927200-346132863');

/*
 @Setting call_id example(设置call_id的例子)
 @Just for extension，只是为了扩展使用，能结合你自身系统的一些队列流水号来使用，然后做一些数据跟踪
*/
//$rrObj->setCallId('12345678');

#Example 1 (一般的使用例子，已经把call_id 及 session_key封装进去了)

$res = $rrObj->POST('users.getInfo', array('346132863,741966903','uid,name,tinyurl,headhurl,zidou,star'));
var_dump($res);

#See source code for get more info(查看源码来获取更多的信息)
?>