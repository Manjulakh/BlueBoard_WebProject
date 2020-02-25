<?php 
include_once 'inc/config.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
$link=connect();
// $member_id=is_login($link);
if(is_login($link)){
	skip('index.php','error','You already log in, please do not register again!');
}
if(isset($_POST['submit'])){
	include 'inc/check_login.inc.php';
	escape($link,$_POST);
	$query="select * from BlueBoard_member where name='{$_POST['name']}' and pw=md5('{$_POST['pw']}')";
	$result=execute($link, $query);
	if(mysqli_num_rows($result)==1){
		setcookie('sfk[name]',$_POST['name'],time()+$_POST['time']);
		setcookie('sfk[pw]',sha1(md5($_POST['pw'])),time()+$_POST['time']);
		skip('index.php','ok','Login Successfully!');
	}else{
		skip('login.php', 'error', 'Wrong user name or password!');
	}
}
$template['title']='Welcome!';
$template['css']=array('style/public.css','style/register.css');
?>
<?php include 'inc/header.inc.php'?>
	<div id="register" class="auto">
		<h2>Please Log in:</h2>
		<form method="post">
			<label>User Name:<input type="text" name="name"  /><span></span></label>
			<label>Password:<input type="password" name="pw"  /><span></span></label>
			<label>Verification Code:<input name="vcode" type="text"  /><span>*Please put in the verification code below:</span></label>
			<img class="vcode" src="show_code.php" />
			<label>Automatically login:
				<select style="width:236px;height:25px;" name="time">
					<option value="3600">Within 1 hour</option>
					<option value="86400">Within 1 day</option>
					<option value="259200">Within 3 days</option>
					<option value="2592000">Within 30 days</option>
				</select>
				<span>*Please don't keep long logined status in public computers!</span>
			</label>
			<div style="clear:both;"></div>
			<input class="btn" type="submit" name="submit" value="Login" />
		</form>
	</div>
<?php include 'inc/footer.inc.php'?>