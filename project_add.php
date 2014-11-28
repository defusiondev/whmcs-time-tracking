<?php
if( $_SERVER["REQUEST_METHOD"] != "POST") {	
	$clients_exist = false;
	global $order_clients_by;
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
		$select .= "<option value='{$client['id']}' >";
		$select .= $client['firstname']." ". $client['lastname'].' - '.$client['companyname'];
		$select .= "</option>";		
	}	
	$select .= "</select>";
	}
?>
	<form name="project_add" method="post" action="<?php echo $modulelink?>&project=add">
		<table>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
				</td>
			</tr>
		</table>
		<table width="700" border="0">
		  <tr valign="top">
			<td>
				<table width="100%" border="0">
				  <tr>
					<td valign="top" class="text_newproject">Add Project</td>
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
							<input type="text" name="project_name" value="" />
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">Description</td>
						<td class="text_project1">
							<textarea name="project_description" cols="" rows=""></textarea>
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
							<input type="submit" name="submit" value="Add Project" />
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
	$sql = "
		INSERT INTO
		`mod_projects` (name, client_id, description, `status`)
		VALUES 
		('$name', $client_id, '$description', 1)
		";
	if(mysql_query($sql)) {
		header("Location:$modulelink&project=list&msgid=3");
	} else {
		header("Location:$modulelink&project=list&msgid=4");
	}
}
?>