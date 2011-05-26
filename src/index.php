<?php

function genUid()
{
    return time()."-".rand(10,99);
}

function updateCookie($uid, $uname)
{
    $expire = time()+3600*24*366;
    setcookie('WEIXIAO_UID', $uid, $expire);
    setcookie('WEIXIAO_UNAME', $uname, $expire);
    setcookie('WEIXIAO_LAST_VISIT', date("Y-m-d H-i-s"), $expire);
}

$u = $_COOKIE['WEIXIAO_UID'];
if(empty($u))
{
    $u = genUid();
}
$uname = $_COOKIE['WEIXIAO_UNAME'];
if(!empty($_POST['uname']) && $_POST['uname']!=$uname)
{
    $uname = $_POST['uname'];
}
updateCookie($u, $uname);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>微笑公益购物</title>
    <style type="text/css">
        body,div,a,ul,ol,li,h1,h2,h3,h4,h5,h6,p {
        	margin:0;
        	padding:0;
        }
        h1, h2, h3, h4, h5, h6 {
        	font-weight:bold;
        	letter-spacing:-0.05em;
        	font-family:Arial;
        }
        h1 {font-size:200%;}
        h2 {font-size:170%;}
        h3 {font-size:150%;}
        h4 {font-size:130%;}
        h5 {font-size:110%;}
        h6 {font-size:100%;}
        img {
        	border:0;
        }
        img.sided {
        	background:#FFF;
        	border:2px solid #CCC;
        	padding:3px;
        }
        a {
        	color:#2970A6;
        	text-decoration:none;
        }
        a:hover {
        	text-decoration:underline;
        }
    </style>
    <script language="javascript">
        function addFav(){
           var ctrl = (navigator.userAgent.toLowerCase()).indexOf('mac') != -1 ? 'Command/Cmd':'Ctrl';
           if(document.all)
           {
              window.external.addFavorite(window.location,document.title);
           }
           else if(window.sidebar)
           { 
               window.sidebar.addPanel(document.title,window.location,"");
           }
            else if (window.opera &&window.print)
            {
                var mbm = document.getElementById('addFavor');
                mbm.setAttribute('rel','sidebar');
                mbm.setAttribute('href',url);
                mbm.setAttribute('title',title);
                mbm.click();
            }
           else{
               alert('添加失败，请用Ctrl+D进行添加');
           }
        }
    </script>
</head>
<body>
<div class="content">
<h1>存一份心，即可尽一份力<a href="./doc/ProjectPlan.ppt" style="font-size:16pt;color:#00ff00">（点击下载项目说明PPT）</a>
    <input type=button onclick="javascript:addFav()" value="加入收藏" size="100px"/></h1>
<p><form method="post" style="background:#CCC">
    用户标识：<?php echo $u;?>，
    设置姓名：<input type="text" name="uname" value="<?php echo $uname;?>"/><input type="submit" /></form></p>
<p>无需付出任何额外的费用和操作，只要您点击下面链接后再购买您选好的物品，电子商务网站将提供购物返点给微笑公益购物，是之为谓：公益购物</p>
<p>按需购买，理性消费</p><br/>
<hr/>
<h1><font color="red">点击下面链接进入商城购物，即可为公益事业助力</font></h1> 
<p><br/></p> 
<h1><a href='http://p.yiqifa.com/c?s=d2c1d4cd&w=317782&c=245&i=201&l=0&e=<?php echo $u;?>&t=http://www.amazon.cn' target='_blank'>卓越亚马逊（Amazon）</a></h1> 
<p><br/></p> 
<h1><a href='http://p.yiqifa.com/c?s=5577a142&w=317782&c=254&i=160&l=0&e=<?php echo $u;?>&t=http://www.360buy.com' target='_blank'>京东商城</a></h1>
<p><br/></p> 
<h1><a href='http://p.yiqifa.com/c?s=e899e047&w=317782&c=247&i=159&l=0&e=<?php echo $u;?>&t=http://www.dangdang.com' target='_blank'>当当网</a></h1> 
<p><br/></p> 
<h1><a href='http://p.yiqifa.com/c?s=bd1cbf2d&w=317782&c=5549&i=12782&l=0&e=<?php echo $u;?>&t=http://www.tmall.com' target='_blank'>淘宝商城</a></h1>
<p><br/></p> 
<h1><a href='http://p.yiqifa.com/c?s=b32f3f96&w=317782&c=255&i=150&l=0&e=<?php echo $u;?>&t=http://www.vancl.com' target='_blank'>Vancl凡客诚品</a></h1>
<p><br/></p> 
<h1><a href='http://p.yiqifa.com/c?s=4f40efa2&w=317782&c=249&i=12102&l=0&e=<?php echo $u;?>&t=http://www.binggo.com/' target='_blank'>红孩子</a></h1> 
<p><br/></p> 
<h1><a href='http://p.yiqifa.com/c?s=3c715283&w=317782&c=280&i=240&l=0&e=<?php echo $u;?>&t=http://www.newegg.com.cn' target='_blank'>新蛋网</a></h1>
<hr />
<p>
    <h2>欢迎点击下面广告</h2>
    <a href='http://p.yiqifa.com/s?sid=7d3b2b7c676fcf12&pid=77110&wid=317782&vid=617&cid=246&lid=609&euid=<?php echo $u;?>&vwid=' target='_blank'><img border='0'  width='80'  height='80'  src='http://p.yiqifa.com/image?sid=7d3b2b7c676fcf12&pid=77110&wid=317782&vid=617&cid=246&lid=609&euid=&vwid=' ></a>
    <div style='position:relative'><embed play='true' allownetworking='internal' style='position:absolute;z-index:0' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' wmode='opaque' loop='true' menu='true'  width='200'  height='100'  src='http://p.yiqifa.com/image?sid=3c3c9e2ede1a0bc3&pid=77110&wid=317782&vid=76078&cid=5535&lid=42647&euid=<?php echo $u;?>&vwid=' ><a href='http://p.yiqifa.com/s?sid=3c3c9e2ede1a0bc3&pid=77110&wid=317782&vid=76078&cid=5535&lid=42647&euid=dddefault&vwid=' target='_blank' style='cursor:pointer'><div style='position:relative; filter:alpha(opacity=0); -moz-opacity:0; left:0; top:0; background:none; *background:#ccc; height:90px; width:720px; z-index:10'></div></a></embed></div>

</p>
</div>

</body>