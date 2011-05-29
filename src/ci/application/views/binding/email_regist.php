<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>以Email+password登录</title>
</head>
<body>
<h1>设置email和密码，给自己多一种登录方式</h1>
<?php echo validation_errors(); ?>
<?php 
echo form_open('/binding/email/bind_submit');
echo form_label('Email地址','email');
echo form_input('email', 'email@example.com');
echo form_label('密码','passwd');
echo form_password('passwd');
echo form_submit('submit', '保存');
//echo anchor('binding/email/show','登录');
echo form_close();
?>
</body>
