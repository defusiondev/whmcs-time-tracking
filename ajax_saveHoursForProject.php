<?php
	$task_id = filter_var($_REQUEST['t'], FILTER_VALIDATE_INT);
	$hours = filter_var($_REQUEST['h'], FILTER_VALIDATE_FLOAT);
	$notes = filter_var($_REQUEST['n'], FILTER_SANITIZE_STRING);

	$m = $_REQUEST['tm'];
	$d = $_REQUEST['td'];
	$y = (filter_var($_REQUEST['ty'], FILTER_VALIDATE_INT)) ? $_REQUEST['ty'] : 2000;

	$time = mktime(1, 1, 1, $m, $d, $y);

	if($task_id != 0 ) {
		$sql = "INSERT INTO `mod_taskhours` (`task_id`, `hours`, `notes`, `billed`, `created`)
			VALUES ($task_id, '$hours', '$notes', 1, $time);
			";
		if(mysql_query($sql)) {
			$load =
				'<script type="text/javascript">
					loadFunctions();
				</script>';
			echo $load;
		}
	}
?>