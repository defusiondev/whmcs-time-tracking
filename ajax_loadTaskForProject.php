<?php
	$project_id = $_REQUEST['p'];
	$sql = "SELECT * FROM `mod_tasks` WHERE `project_id` = $project_id AND `bill_to_client` = 1 order by name";
	$exec = mysql_query($sql);
	$select = '';
	if(mysql_num_rows($exec) > 0) {
		$select = "<select id='task' name='task'>";
		while($project = mysql_fetch_array($exec)) {
			$select .= "<option value='{$project['id']}' >";
			$select .= $project['name'];
			$select .= "</option>";
		}
		$select .= "</select>";
	} else {
		$select .= "<select id='task' name='task'>";
		$select .= "<option>---</option>";
		$select .= "</select>";
	}
	echo $select;
?>