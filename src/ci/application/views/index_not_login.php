<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>首页 - 登陆前</title>
</head>
<body>
<h1>hi guest!</h1>
<p>
<ul>
<li><?php 
$atts = array(
              'width'      => '640',
              'height'     => '480',
              'scrollbars' => 'no',
              'status'     => 'no',
              'resizable'  => 'yes',
              'screenx'    => '100',
              'screeny'    => '100'
            );
echo anchor_popup('binding/sina/show', '<img src="images/weibo.png" alt="以新浪微博帐号登录" />', $atts);
?></li>
<li><a href="http://open.qq.com">以QQ帐号登录</a></li>
<li><a href="http://renren.com">以人人帐号登录</a></li>
<li><a href="http://kaixin001.com">以开心网帐号登录</a></li>
</ul>
</p>
</body>