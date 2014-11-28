<?php
	$client_id = filter_var($_REQUEST['client_id'], FILTER_VALIDATE_INT);

	$sql = "SELECT * FROM `mod_projects` WHERE `client_id` = $client_id order by name;";
	$exec = mysql_query($sql);
	if(mysql_num_rows($exec) > 0) {
		$select = "<select id='project_id' name='project_id'>";
		while($project = mysql_fetch_array($exec)) {
			$select .= "<option value='{$project['id']}' >";
			$select .= $project['name'];
			$select .= "</option>";
		}
		$select .= "</select>";
	} else {
		$select = "<select name='project_id' id='project_id'><option value='0'>---</option></select>";
	}
	echo $select;
?>
