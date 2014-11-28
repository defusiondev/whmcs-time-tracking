<?php
if( $_SERVER["REQUEST_METHOD"] != "POST") {

?>
	<form name="task_add" method="post" action="<?php echo $modulelink?>&task=add">
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
							<input type="text" name="task_name" />
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">Description</td>
						<td class="text_project1">
							<textarea cols="20" rows="3" name="description"></textarea>
						</td>
					  </tr>
					  <tr>
						<td width="21%" class="textname_box">Rate per hour</td>
						<td width="79%" class="text_project1">
							<input type="hidden" name="billable" value="1" />
							<input type="text" name="rate" />
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
										$select .= "<option value='{$project['id']}' >";
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
							<input type="submit" name="submit" value="Add Task" />
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
	if(trim($name) == '' || trim($projects) == '') {
		header("Location:$modulelink&task=list&msgid=15");
		exit();
	}
	$sql = "
		INSERT INTO `mod_tasks` (`name`, `description`, `project_id`, `bill_to_client`, `rate`, `created`)
		VALUES ('$name', '$description', '$projects', $billable, $rate, $time);
		";
	if(mysql_query($sql)) {
		header("Location:$modulelink&task=list&msgid=8");
	} else {
		header("Location:$modulelink&task=list&msgid=9");
	}
}
?>