<?php 
include_once 'inc/config.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/upload.inc.php';
$link=connect();
if(!$member_id=is_login($link)){
	skip('login.php', 'error', 'Please login!');
}
$query="select * from BlueBoard_member where id={$member_id}";
$result_memebr=execute($link,$query);
$data_member=mysqli_fetch_assoc($result_memebr);
if(isset($_POST['submit'])){
	$save_path='uploads_pics'.date('/Y');
	$upload=upload($save_path,'8M','photo');
	if($upload['return']){
		$query="update BlueBoard_member set photo='{$upload['save_path']}' where id={$member_id}";
		execute($link, $query);
		if(mysqli_affected_rows($link)==1){
			skip("member.php?id={$member_id}",'ok','Upload picture succesfully!');
		}else{
			skip('member_photo_update.php','error','Sorry, upload picture failed, please try again!');
		}
	}else{
		skip('member_photo_update.php', 'error',$upload['error']);
	}
}

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="utf-8" />
<title></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<style type="text/css">
body {
	font-size:12px;
}
h2 {
	padding:0 0 10px 0;
	border-bottom: 1px solid #e3e3e3;
	color:#444;
}
.submit {
	background-color: #3b7dc3;
	color:#fff;
	padding:5px 22px;
	border-radius:2px;
	border:0px;
	cursor:pointer;
	font-size:14px;
}
#main {
	width:80%;
	margin:0 auto;
}
</style>
</head>
<body>
	<div id="main">
		<h2>Edit Picture</h2>
		<div>
			<h3>Profile picture:</h3>
			<img src="<?php if($data_member['photo']!==''){echo $data_member['photo'];}else{echo 'style/photo.jpg';}?>" />
			<!-- <img src="style/photo.jpg"> -->
			<!-- <img src="<?php echo $data_member['photo'];?>"> -->
			<br />
			Best picture size: 180*180
		</div>
		<div style="margin:15px 0 0 0;">
			<form method="post" enctype="multipart/form-data">
				<input style="cursor:pointer;" width="100" type="file" name="photo"/><br /><br />
				<input class="submit" type="submit" name="submit" value="Upload" />
			</form>
		</div>
	</div>
</body>
</html>