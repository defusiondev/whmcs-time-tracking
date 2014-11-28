<?php
	$id = filter_var($_REQUEST['id'], FILTER_VALIDATE_INT);
	$sql = "SELECT * FROM `mod_tasks` WHERE `id` = $id LIMIT 1";
	$exec = mysql_query($sql);
	if(mysql_num_rows($exec) > 0) {
		$sql = "DELETE FROM `mod_tasks` WHERE `id` = $id LIMIT 1";
		if(mysql_query($sql)) {
			header("Location:$modulelink&task=list&msgid=12");
		} else {
			header("Location:$modulelink&task=list&msgid=13");
		}
	} else {
		header("Location:$modulelink&task=list&msgid=14");
	}
	
?>