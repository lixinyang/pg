<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>首页 - 登陆前</title>
</head>
<body>
<h1>hi guest!</h1>
<script type="javascript">
</script>
<p>
<ul>
<li><?php 
$atts = array(
              'width'      => '600',
              'height'     => '400',
              'scrollbars' => 'yes',
              'status'     => 'yes',
              'resizable'  => 'yes',
			  'title'	   => '以Email+密码登录',
              'screenx'    => '100',
              'screeny'    => '100'
            );
echo anchor_popup('binding/email/show', '以Email+密码登录', $atts);
?></li>
<li><?php 
$atts = array(
              'width'      => '600',
              'height'     => '400',
              'scrollbars' => 'yes',
              'status'     => 'yes',
              'resizable'  => 'yes',
              'screenx'    => '100',
              'screeny'    => '100'
            );
echo anchor_popup('binding/sina/show', '<img src="images/weibo.png" alt="用新浪微博帐号登录" />', $atts);
?></li>
<li><?php 
$atts = array(
              'width'      	=> '450',
              'height'     	=> '320',
              'menubar' 	=> 'no',
              'scrollbars'  => 'yes',
              'resizable'  	=> 'yes',
              'status'    	=> 'yes',
              'titlebar'    => 'no',
              'toolbar'    	=> 'no',
              'screenx'    => '100',
              'screeny'    => '100',
			  'location'    => 'yes'
);
echo anchor_popup('binding/qq/show', '<img src="images/qq.png" alt="用QQ帐号登录" title="用QQ帐号登录"/>', $atts);
?></li>
<li><?php 
$atts = array(
              'width'      	=> '800',
              'height'     	=> '600',
              'menubar' 	=> 'no',
              'scrollbars'  => 'yes',
              'resizable'  	=> 'yes',
              'status'    	=> 'yes',
              'titlebar'    => 'no',
              'toolbar'    	=> 'no',
              'screenx'    => '100',
              'screeny'    => '100',
			  'location'    => 'yes'
);
echo anchor_popup('binding/tqq/show', '<img src="images/qq.png" alt="用腾讯微博帐号登录" title="用腾讯微博帐号登录"/>', $atts);
?></li>
<li><?php 
$atts = array(
              'width'      	=> '450',
              'height'     	=> '320',
              'menubar' 	=> 'no',
              'scrollbars'  => 'yes',
              'resizable'  	=> 'yes',
              'status'    	=> 'yes',
              'titlebar'    => 'no',
              'toolbar'    	=> 'no',
              'location'    => 'yes'
);
echo anchor_popup('binding/renren/show', '<img src="images/renren.png" alt="用人人帐号登录" />', $atts);
?></li>
<li><a href="javascript:void(0)" onclick="javascript:alert('Coming soon，敬请期待')">以开心网帐号登录</a></li>
</ul>
</p>
</body>