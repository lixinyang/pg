<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>登录成功</title>
</head>
<body>
<script language="JavaScript" type="text/javascript">
function close_myself()
{
	window.opener.location.href = window.opener.location.href;
	window.close();
}
window.setTimeout(close_myself, 3000);
</script>
<h1>hi <?php echo $user->display_name; ?>, 欢迎回来！</h1>
<p>3秒钟后自动关闭。。。</p>
<p><a href="javascript:void(0)" onclick="close_myself()">立即关闭</a></p>
</body>