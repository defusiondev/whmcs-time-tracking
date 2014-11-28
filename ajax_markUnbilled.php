<?php
$id = filter_var($_REQUEST['id'], FILTER_VALIDATE_INT);
if(is_numeric($id)) {
	$sql = "UPDATE `mod_taskhours` SET `billed` = 0 WHERE `id` = $id LIMIT 1;";
	mysql_query($sql);
}
?>