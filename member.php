<?php 
include_once 'inc/config.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
include_once 'inc/page.inc.php';
$link=connect();
$is_manage_login=is_manage_login($link);
$member_id=is_login($link);
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
	skip('index.php', 'error', 'Wrong ID!');
}
$query="select * from BlueBoard_member where id={$_GET['id']}";
$result_memebr=execute($link, $query);
if(mysqli_num_rows($result_memebr)!=1){
	skip('index.php', 'error', 'This BlueBoard Member does not exit!');
}
$data_member=mysqli_fetch_assoc($result_memebr);
$query="select count(*) from content where member_id={$_GET['id']}";
$count_all=num($link, $query);

$template['title']='User Panel';
$template['css']=array('style/public.css','style/list.css','style/member.css');
?>
<?php include 'inc/header.inc.php'?>
<div id="position" class="auto">
	<a href="index.php">Main</a> &gt; <?php echo $data_member['name']?>
</div>
<div id="main" class="auto">
	<div id="left">
		<ul class="postsList">
			<?php 
			$page=page($count_all,20);
			$query="select
			content.title,content.id,content.time,content.member_id,content.times,BlueBoard_member.name,BlueBoard_member.photo
			from content,BlueBoard_member where
			content.member_id={$_GET['id']} and
			content.member_id=BlueBoard_member.id order by id desc {$page['limit']}";
			$result_content=execute($link, $query);
			while($data_content=mysqli_fetch_assoc($result_content)){
				$data_content['title']=htmlspecialchars($data_content['title']);
				$query="select time from reply where content_id={$data_content['id']} order by id desc limit 1";
				$result_last_reply=execute($link, $query);
				if(mysqli_num_rows($result_last_reply)==0){
					$last_time='Does not exit yet!';
				}else{
					$data_last_reply=mysqli_fetch_assoc($result_last_reply);
					$last_time=$data_last_reply['time'];
				}
				$query="select count(*) from reply where content_id={$data_content['id']}";
			?>
			<li>
				<div class="smallPic">
					<img width="45" height="45" src="<?php if($data_content['photo']!=''){echo $data_content['photo'];}else{echo 'style/photo.jpg';}?>" />
				</div>
				<div class="subject">
					<div class="titleWrap"><h2><a target="_blank" href="show.php?id=<?php echo $data_content['id']?>"><?php echo $data_content['title']?></a></h2></div>
					<p>
						<?php 
						if(check_user($member_id,$data_content['member_id'],$is_manage_login)){
							$url=urlencode("content_delete.php?id={$data_content['id']}");
							$return_url=urlencode($_SERVER['REQUEST_URI']);
							$message="Are you sure you want to detele [{$data_content['title']}] ?";
							$delete_url="confirm.php?url={$url}&return_url={$return_url}&message={$message}";
							echo "<a href='content_update.php?id={$data_content['id']}'>Modify</a> <a href='{$delete_url}'>Delete</a>";
						}
						?>
						 Publish date: <?php echo $data_content['time']?>&nbsp;&nbsp;&nbsp;&nbsp;Last reply: <?php echo $last_time?>
					</p>
				</div>
				<div class="count">
					<p>
						Reply<br /><span><?php echo num($link,$query)?></span>
					</p>
					<p>
						Visit<br /><span><?php echo $data_content['times']?></span>
					</p>
				</div>
				<div style="clear:both;"></div>
			</li>
			<?php }?>
		</ul>
		<div class="pages">
			<?php 
			echo $page['html'];
			?>
		</div>
	</div>
	<div id="right">
		<div class="member_big">
			<dl>
				<dt>
					<img width="180" height="180" src="<?php if($data_member['photo']!=''){echo $data_member['photo'];}else{echo 'style/photo.jpg';}?>" />
				</dt>
				<dd class="name"><?php echo $data_member['name']?></dd>
				<dd>Total posts: <?php echo $count_all?></dd>
				<?php 
				if($member_id==$data_member['id']){
				?>
				<dd>Settings: <a target="_blank" href="member_photo_update.php">Profile Pictures</a><!--  | <a target="_blank" href="">修改密码</a></dd> -->
				<?php }?>
			</dl>
			<div style="clear:both;"></div>
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
<?php include 'inc/footer.inc.php'?>