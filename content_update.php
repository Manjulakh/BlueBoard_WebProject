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
	skip('index.php', 'error', 'Wrong ID!');
}
$query="select * from content where id={$_GET['id']}";
$result_content=execute($link, $query);
if(mysqli_num_rows($result_content)==1){
	$data_content=mysqli_fetch_assoc($result_content);
	$data_content['title']=htmlspecialchars($data_content['title']);
	if(check_user($member_id,$data_content['member_id'],$is_manage_login)){
		if(isset($_POST['submit'])){
			include 'inc/check_publish.inc.php';
			$_POST=escape($link, $_POST);
			$query="update content set module_id={$_POST['module_id']},title='{$_POST['title']}',content='{$_POST['content']}' where id={$_GET['id']}";
			execute($link, $query);
			if(isset($_GET['return_url'])){
				$return_url=$_GET['return_url'];
			}else{
				$return_url="member.php?id={$member_id}";
			}
			if(mysqli_affected_rows($link)==1){
				skip($return_url, 'ok', 'Edit successfully!');
			}else{
				skip($return_url, 'error', 'Edit failed, please try again!');
			}
		}
	}else{
		skip('index.php', 'error', 'Sorry, You do not have the right to edit this post!');
	}
}else{
	skip('index.php', 'error', 'This post does not exist!');
}
$template['title']='Post Modify Page';
$template['css']=array('style/public.css','style/publish.css');
?>
<?php include 'inc/header.inc.php'?>
<div id="position" class="auto">
	 <a href="index.php">Main</a> &gt; Posting
</div>
<div id="publish">
	<form method="post">
		<select name="module_id">
			<option value='-1'>Please choose a sub module</option>
			<?php 
			$query="select * from father_module order by sort desc";
			$result_father=execute($link, $query);
			while ($data_father=mysqli_fetch_assoc($result_father)){
				echo "<optgroup label='{$data_father['module_name']}'>";
				$query="select * from son_module where father_module_id={$data_father['id']} order by sort desc";
				$result_son=execute($link, $query);
				while ($data_son=mysqli_fetch_assoc($result_son)){
					if($data_son['id']==$data_content['module_id']){
						echo "<option selected='selected' value='{$data_son['id']}'>{$data_son['module_name']}</option>";
					}else{
						echo "<option value='{$data_son['id']}'>{$data_son['module_name']}</option>";
					}
				}
				echo "</optgroup>";
			}
			?>
		</select>
		<input class="title" placeholder="Please enter a title" value="<?php echo $data_content['title']?>" name="title" type="text" />
		<textarea name="content" class="content"><?php echo $data_content['content']?></textarea>
		<input style="margin-top:20px;cursor:pointer;" class="btn" type="submit" name="submit" value="Publish" />
		<div style="clear:both;"></div>
	</form>
</div>
<?php include 'inc/footer.inc.php'?>