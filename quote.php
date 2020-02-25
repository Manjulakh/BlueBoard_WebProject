<?php 
include_once 'inc/config.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
$link=connect();
if(!$member_id=is_login($link)){
	skip('login.php', 'error', 'Please login before replying!');
}
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
	skip('index.php', 'error', 'Wrong Id!');
}
$query="select sc.id,sc.title,sm.name from content sc,BlueBoard_member sm where sc.id={$_GET['id']} and sc.member_id=sm.id";
$result_content=execute($link, $query);
if(mysqli_num_rows($result_content)!=1){
	skip('index.php', 'error', 'This post does not exist!');
}
$data_content=mysqli_fetch_assoc($result_content);
$data_content['title']=htmlspecialchars($data_content['title']);


if(!isset($_GET['reply_id']) || !is_numeric($_GET['reply_id'])){
	skip('index.php', 'error', 'Wrong ID!');
}
$query="select reply.content,BlueBoard_member.name from reply,BlueBoard_member where reply.id={$_GET['reply_id']} and reply.content_id={$_GET['id']} and reply.member_id=BlueBoard_member.id";
$result_reply=execute($link, $query);
if(mysqli_num_rows($result_reply)!=1){
	skip('index.php', 'error', 'The post you quote does not exist!');
}
if(isset($_POST['submit'])){
	include 'inc/check_reply.inc.php';
	$_POST=escape($link,$_POST);
	$query="insert into reply(content_id,quote_id,content,time,member_id) 
			values(
				{$_GET['id']},{$_GET['reply_id']},'{$_POST['content']}',now(),{$member_id}
			)";
	execute($link, $query);
	if(mysqli_affected_rows($link)==1){
		skip("show.php?id={$_GET['id']}", 'ok', 'Reply successfully@');
	}else{
		skip($_SERVER['REQUEST_URI'], 'error', 'Reply failed please try again!');
	}
}
$data_reply=mysqli_fetch_assoc($result_reply);
$data_reply['content']=nl2br(htmlspecialchars($data_reply['content']));

$query="select count(*) from reply where content_id={$_GET['id']} and id<={$_GET['reply_id']}";
$floor=num($link,$query);


$template['title']='Quote Reply Page';
$template['css']=array('style/public.css','style/publish.css');
?>
<?php include 'inc/header.inc.php'?>
<div id="publish">
	<div><?php echo $data_content['name']?>: <?php echo $data_content['title']?></div>
	<div class="quote">
		<p class="title">quote from No.<?php echo $floor?> published by <?php echo $data_reply['name']?></p>
		<?php echo $data_reply['content']?>
	</div>
	<form method="post">
		<textarea name="content" class="content"></textarea>
		<!-- <input class="reply" type="submit" name="submit" value="" /> -->
		<input style="margin-top:20px;cursor:pointer;" class="btn" type="submit" name="submit" value="Reply" />
		<div style="clear:both;"></div>
	</form>
</div>
<?php include 'inc/footer.inc.php'?>