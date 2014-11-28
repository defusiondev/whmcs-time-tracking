<?php
if( $_SERVER["REQUEST_METHOD"] != "POST") {

	$id = (filter_var($_REQUEST['id'], FILTER_VALIDATE_INT)) ? $_REQUEST['id'] : 0;
	$sql = "SELECT * FROM `mod_tasks` WHERE `id` = $id";
	$exec = mysql_query($sql);
	$task = mysql_fetch_array($exec);

?>
	<form name="task_add" method="post" action="<?php echo $modulelink?>&task=edit">
		<table width="700" border="0">
		  <tr valign="top">
			<td>
				<table width="100%" border="0">
				  <tr>
					<td valign="top" class="text_newproject">New Task</td>
				  </tr>
				  <tr>
					<td valign="top" class="txt_projectinfo">
						Task Information
					</td>
				  </tr>
				  <tr>
					<td valign="top"><table width="100%" border="0" cellspacing="0">
					  <tr>
						<td width="21%" class="textname_box">Task Name</td>
						<td width="79%" class="text_project1">
					<input type="text" name="task_name" value="<?php echo $task['name'];?>" />
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">Description</td>
						<td class="text_project1">
							<textarea cols="20" rows="3" name="description"><?php echo $task['description'];?></textarea>
						</td>
					  </tr>
					  <tr>
						<td width="21%" class="textname_box">Rate per hour</td>
						<td width="79%" class="text_project1">
							<input type="hidden" name="billable" value="1" />
							<input type="text" name="rate" value="<?php echo $task['rate']; ?>" />
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">
							Project
						</td>
						<td class="text_project1">
						<?php
							$sql = "SELECT * FROM `mod_projects` order by name";
							$exec = mysql_query($sql);
							if(mysql_num_rows($exec) > 0) {
								$select = "<select name='projects'>";
								while($project = mysql_fetch_array($exec)) {
									if($project['id'] == $task['project_id']) {
										$select .= "<option value='{$project['id']}' selected='selected' >";
									} else {
										$select .= "<option value='{$project['id']}' >";
									}
									$select .= $project['name'];
									$select .= "</option>";
								}
								$select .= "</select>";
							} else {
								$select = "No project in database";
							}
							echo $select;
						?>
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">&nbsp;</td>
						<td class="text_project1">
							<input type="hidden" name="id" value="<?php echo $id;?>" />
							<input type="submit" name="submit" value="Update Task" />
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">&nbsp;</td>
						<td class="text_project1">&nbsp;</td>
					  </tr>
					</table></td>
				  </tr>
				  <tr>
					<td valign="top">&nbsp;</td>
				  </tr>
				  <tr>
					<td valign="top">&nbsp;</td>
				  </tr>
				</table>
			</td>
		  </tr>
		</table>
	</form>
<?php
} else {

	$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
	$projects = $_POST['projects'];

	$billable = filter_var($_POST['billable'], FILTER_SANITIZE_STRING);
	if($billable == 1) {
		$billable = 1;
		$rate = (filter_var($_POST['rate'], FILTER_VALIDATE_FLOAT)) ? $_POST['rate'] : 0;
	} else {
		$billable = 0;
		$rate = 0;
	}
	$name = filter_var($_POST['task_name'], FILTER_SANITIZE_STRING);
	$description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
	$time = time();
	$sql = "
		UPDATE `mod_tasks`
		SET `name` = '$name', `description`='$description', `project_id` = $projects, `bill_to_client` = $billable, `rate` =$rate,`modified` = $time
		WHERE `id` = $id;
		";
	if(mysql_query($sql)) {
		header("Location:$modulelink&task=list&msgid=10");
	} else {
		header("Location:$modulelink&task=list&msgid=11");
	}
}
?>