<?php
@header('Content-Type:text/html;charset=utf-8'); 
session_start();
@require_once('config.php');
@require_once('oauth.php');
@require_once('opent.php');
@require_once('api_client.php');

$c = new MBApiClient( MB_AKEY , MB_SKEY , $_SESSION['last_key']['oauth_token'] , $_SESSION['last_key']['oauth_token_secret']  );
//时间线
$p =array(
	'f' => 0,
	't' => 0,		
	'n' => 5 
);
var_dump($c->getTimeline($p));

//拉取username的信息
$p =array(
	'f' => 0,
	't' => 0,		
	'n' => 5,
   	'name' => 'username'	
);
//var_dump($c->getTimeline($p));

//拉取广播大厅消息
$p =array(
	'p' => 0,
	'n' => 5		
);
//var_dump($c->getPublic($p));

//拉取关于我的消息
$p =array(
	'f' => 0,
	'n' => 5,		
	't' => 0,
	'l' => '',
	'type' => 1
);
//var_dump($c->getMyTweet($p));
//
//单条消息
$p =array(
	'id' => 26016073563599 
);
//var_dump($c->getOne($p));
//
//发消息
//	*@content: 微博内容
$p =array(
	'c' => '火车侠',
	'ip' => $_SERVER['REMOTE_ADDR'], 
	'j' => '',
	'w' => ''
);
//var_dump($c->postOne($p));
//
//	*@content: 微博内容
$p =array(
	'id' => 14511064212422
);
//var_dump($c->delOne($p));
$p =array(
	'c' => '转播火车侠',
	'ip' => $_SERVER['REMOTE_ADDR'], 
	'j' => '',
	'w' => '',
	'type' => 1,
	'r' => 10511064707448 
);
//var_dump($c->postOne($p));

$p =array(
	'c' => '转播火车侠',
	'ip' => $_SERVER['REMOTE_ADDR'], 
	'j' => '',
	'w' => '',
	'type' => 2,
	'r' => 10511064707448 
);
//var_dump($c->postOne($p));


$p =array(
	'n' => 20, 
	'f' => 0,
	'reid' => 11016107749292 
);
//print_r($c->getReplay($p));
//print_r($c->getUserInfo());
$p =array(
	'n' => 'username', 
);
//print_r($c->getUserInfo($p));

//
$p =array(
	'n' => 'username',
    'type' => 2	
);
//print_r($c->setMyidol($p));

$p =array(
	'f' => 0,
	'n' => 5,		
	't' => 0,
	'type' => 0
);
//print_r($c->getMailBox($p));
//
$p =array(
	'k' => 'username',
	'n' => 10,		
	'p' => 0,
	'type' => 2
);
//print_r($c->getSearch($p));
//
////
$p =array(
	'type' => 3,		
	'n' => 5,
	'pos' => 0
);
//print_r($c->getHotTopic($p));

$p =array(
	'op' => 0		
);
//print_r($c->getUpdate($p));
//31016124947861
$p =array(
	'id' => 31016124947861,
	'type' => 0	
);
//print_r($c->postFavMsg($p));

$p =array(
	'id' => 10109507010925991304,
	'type' => 0	
);
//print_r($c->postFavTopic($p));
//
$p =array(
	'f' => 0,
	'n' => 5,
	't' => 0,
	'lid' => 0,
	'type' => 1	
);
//print_r($c->getFav($p));
$p =array(
	'list' => '四川洪灾,微博简易食谱'
);
//print_r($c->getTopicId($p));
//
$p =array(
	'list' => '10109507010925991304,14318500857773196362'
);
//print_r($c->getTopicList($p));
?>
