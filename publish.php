<?php 
include_once 'inc/config.inc.php';
include_once 'inc/mysql.inc.php';
include_once 'inc/tool.inc.php';
$link=connect();
if(!$member_id=is_login($link)){
	skip('login.php', 'error', 'Please login before posting!');
}
if(isset($_POST['submit'])){
	include 'inc/check_publish.inc.php';
	$_POST=escape($link,$_POST);
	$query="insert into content(module_id,title,content,time,member_id) values({$_POST['module_id']},'{$_POST['title']}','{$_POST['content']}',now(),{$member_id})";
	execute($link, $query);
	if(mysqli_affected_rows($link)==1){
		skip('publish.php', 'ok', 'Published Successfully!');
	}else{
		skip('publish.php', 'error', 'Sorry, published failed, please try again!');
	}
}
$template['title']='Post Page';
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
				$where='';
				if(isset($_GET['father_module_id']) && is_numeric($_GET['father_module_id'])){
					$where="where id={$_GET['father_module_id']} ";
				}
				$query="select * from father_module {$where}order by sort desc";
				$result_father=execute($link, $query);
				while ($data_father=mysqli_fetch_assoc($result_father)){
					echo "<optgroup label='{$data_father['module_name']}'>";
					$query="select * from son_module where father_module_id={$data_father['id']} order by sort desc";
					$result_son=execute($link, $query);
					while ($data_son=mysqli_fetch_assoc($result_son)){
						if(isset($_GET['son_module_id']) && $_GET['son_module_id']==$data_son['id']){
							echo "<option selected='selected' value='{$data_son['id']}'>{$data_son['module_name']}</option>";
						}else{
							echo "<option value='{$data_son['id']}'>{$data_son['module_name']}</option>";
						}
					}
					echo "</optgroup>";
				}
				?>
			</select>
			<input class="title" placeholder="Please enter a title" name="title" type="text" />
			<textarea name="content" class="content"></textarea>
			<!-- <input class="publish" type="submit" name="submit" value="" /> -->
			<input style="margin-top:20px;cursor:pointer;" class="btn" type="submit" name="submit" value="Publish" />
			<div style="clear:both;"></div>
		</form>
	</div>
<?php include 'inc/footer.inc.php'?>