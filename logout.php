<?php 
include_once 'inc/config.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
$link=connect();
$member_id=is_login($link);
if(!$member_id){
	skip('index.php','error','You have not login yet so you do not need to logout!');
}
setcookie('sfk[name]','',time()-3600);
setcookie('sfk[pw]','',time()-3600);
skip('index.php','ok','Logout sucessfully!');
?>