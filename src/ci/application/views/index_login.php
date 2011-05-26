<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>首页 - 登陆前</title>
</head>
<body>
<h1>hi dear, <?php echo $user->display_name; ?>!</h1>
<p>
<?php echo anchor('index/logout', '退出登录'); ?>
</p>
<p>
<h2>您在新浪微博上的个人信息</h2>
<ul>
<?php foreach ($sina as $key => $value):?>
<li><?php echo $key.': '.$value?></li>
<?php endforeach;?>
</ul>
</p>
</body>