<?php
	$id = filter_var($_REQUEST['id'], FILTER_VALIDATE_INT);
	$sql = "SELECT * FROM `mod_projects` WHERE `id` = $id LIMIT 1";
	$exec = mysql_query($sql);
	if(mysql_num_rows($exec) > 0) {
		$sql = "DELETE FROM `mod_projects` WHERE `id` = $id LIMIT 1";
		if(mysql_query($sql)) {
			header("Location:$modulelink&project=list&msgid=5");
		} else {
			header("Location:$modulelink&project=list&msgid=6");
		}
	} else {
		header("Location:$modulelink&project=list&msgid=7");
	}
	
?>