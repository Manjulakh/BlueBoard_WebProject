<?php 
include_once 'inc/config.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
$link=connect();
$member_id=is_login($link);
if($member_id){
	skip('index.php','error','You already log in, please do not register again!');
}
if(isset($_POST['submit'])){
	include 'inc/check_register.inc.php';
	$query="insert into BlueBoard_member(name,pw,register_time) values('{$_POST['name']}',md5('{$_POST['pw']}'),now())";
	execute($link,$query);
	if(mysqli_affected_rows($link)==1){
		setcookie('sfk[name]',$_POST['name']);
		setcookie('sfk[pw]',sha1(md5($_POST['pw'])));
		skip('index.php','ok','Register Successfully!');
	}else{
		skip('register.php','eror','Register failed, please try again!');
	}
}
$template['title']='Register Page';
$template['css']=array('style/public.css','style/register.css');
?>
<?php include 'inc/header.inc.php'?>
	<div id="register" class="auto">
		<h2>Welcome to become BlueBoard Members!</h2>
		<form method="post">
			<label>User Name:<input type="text" name="name"  /><span>*User name can't be NULL or over 32 letters</span></label>
			<label>Password:<input type="password" name="pw"  /><span>*Password should be longer than 6 letters</span></label>
			<label>Confirm password:<input type="password" name="confirm_pw"  /><span>*Keep two passwords consistent</span></label>
			<label>Verification code:<input name="vcode" name="vocode" type="text"  /><span>*Please enter the code below</span></label>
			<img class="vcode" src="show_code.php" />
			<div style="clear:both;"></div>
			<input class="btn" name="submit" type="submit" value="register" />
		</form>
	</div>
	<div id="footer" class="auto">
		<div class="bottom">
			<a>BlueBoard</a>
		</div>
		<div class="copyright">Powered by BluBoard ©2019 UMich_Dearborn.com</div>
	</div>
</body>
</html>