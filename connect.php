<?php
require dirname(__FILE__).'/../../../configuration.php';
$con = mysql_connect($db_host, $db_username, $db_password);
if($con) {
	mysql_select_db($db_name, $con);
}
?>
