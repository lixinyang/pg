<?php
@header('Content-Type:text/html;charset=utf-8'); 
session_start();
@require_once('config.php');
@require_once('oauth.php');
@require_once('opent.php');
@require_once('api_client.php');

if(isset($_POST['content']))
{
		$c = new MBApiClient( MB_AKEY , MB_SKEY , $_SESSION['last_key']['oauth_token'] , $_SESSION['last_key']['oauth_token_secret']  );	
		//发消息
		//	*@content: 微博内容
		$p =array(
			'c' => $_POST['content'],
			'ip' => $_SERVER['REMOTE_ADDR'], 
			'j' => '',
			'w' => '',
			'p' => array($_FILES['pic']['type'],$_FILES['pic']['name'],file_get_contents($_FILES['pic']['tmp_name'])),
			'type' => 0
		);
		print_r($c->postOne($p));		
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
  内容：<input type="text" name="content" id="content" />
  图片：<input type="file" name="pic" id="pic" />
  <input type="submit" name="button" id="button" value="提交" />
</form>
</body>
</html>
