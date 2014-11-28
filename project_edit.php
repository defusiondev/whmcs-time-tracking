<?php
if( $_SERVER["REQUEST_METHOD"] != "POST") {
	global $order_clients_by;
	$id = filter_var($_REQUEST['id'], FILTER_VALIDATE_INT);
	$sql = "SELECT * FROM `mod_projects` WHERE `id` = $id LIMIT 1";
	$exec = mysql_query($sql);
	$project = mysql_fetch_array($exec);
	
	$clients_exist = false;
	$sql = "SELECT id,firstname,lastname,companyname FROM tblclients order by $order_clients_by";
	if($exec = mysql_query($sql)) {
		if(is_resource($exec) && mysql_num_rows($exec) > 0) {
			$clients_exist = true;
			$clients = array();
			while($clients_ = mysql_fetch_array($exec)) {
				$clients[] = $clients_ ;
			}
		}
	}
	if($clients_exist){
	$select = "<select name='client'>";
	foreach ($clients as $client) {	
		if($project['client_id'] == $client['id']) {
			$select .= "<option selected='selected' value='{$client['id']}' >";
		} else {
			$select .= "<option value='{$client['id']}' >";
		}
		$select .= $client['firstname']." ". $client['lastname']." - ".$client['companyname'];
		$select .= "</option>";		
	}	
	$select .= "</select>";
	}
?>
	<form name="project_add" method="post" action="<?php echo $modulelink?>&project=edit">
		<table>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $id;?>" />
		<table width="700" border="0">
		  <tr valign="top">
			<td>
				<table width="100%" border="0">
				  <tr>
					<td valign="top" class="text_newproject">Edit Project</td>
				  </tr>
				  <tr>
					<td valign="top" class="txt_projectinfo">
						Project Information
					</td>
				  </tr>
				  <tr>
					<td valign="top"><table width="100%" border="0" cellspacing="0">
					  <tr>
						<td width="21%" class="textname_box">Project Name</td>
						<td width="79%" class="text_project1">
							<input type="text" name="project_name" value="<?php echo $project['name'];?>" />
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">Description</td>
						<td class="text_project1">
							<textarea name="project_description" cols="" rows=""><?php echo $project['description'];?></textarea>
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">
							Client
						</td>
						<td class="text_project1">
							<?php
								echo $select;
							?>
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">&nbsp;</td>
						<td class="text_project1">
							<input type="submit" name="submit" value="Update Project" />
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
	$name = filter_var($_POST['project_name'], FILTER_SANITIZE_STRING);
	$description = filter_var($_POST['project_description'], FILTER_SANITIZE_STRING);
	$client_id = filter_var($_POST['client'], FILTER_VALIDATE_INT);
	$time = time();
	$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
	$sql = "
		UPDATE `mod_projects`
		SET
			`name` = '$name',
			`client_id` = $client_id,
			`description` = '$description',
			`status` = 1,
			`modified` = $time
		WHERE `id` = $id;
		";
	if(mysql_query($sql)) {
		header("Location:$modulelink&project=list&msgid=3");
	} else {
		header("Location:$modulelink&project=list&msgid=4");
	}
}
?>