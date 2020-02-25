<?php 
include_once 'inc/config.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
$link=connect();
$is_manage_login=is_manage_login($link);
$member_id=is_login($link);
if(!$member_id && !$is_manage_login){
	skip('login.php', 'error', 'Please login!');
}
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
	skip('index.php', 'error', 'Wrong ID！');
}
$query="select member_id from content where id={$_GET['id']}";
$result_content=execute($link, $query);
if(mysqli_num_rows($result_content)==1){
	$data_content=mysqli_fetch_assoc($result_content);
	if(check_user($member_id,$data_content['member_id'],$is_manage_login)){
		$query="delete from content where id={$_GET['id']}";
		execute($link, $query);
		if(isset($_GET['return_url'])){
			$return_url=$_GET['return_url'];
		}else{
			$return_url="member.php?id={$member_id}";
		}
		if(mysqli_affected_rows($link)==1){
			skip($return_url, 'ok', 'Delete succesfully!');
		}else{
			skip($return_url, 'error', 'Delete failed, please try again!');
		}
	}else{
		skip('index.php', 'error', 'Sorry, you do not have the right to delete this post!');
	}
}else{
	skip('index.php', 'error', 'This post does not exist!');
}
?>