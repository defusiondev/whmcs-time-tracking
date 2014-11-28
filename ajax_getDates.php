<?php

$m = isset($_REQUEST['m']) ? $_REQUEST['m'] : date('m');
$y = isset($_REQUEST['y']) ? $_REQUEST['y'] : date('Y');
$d = date('t', mktime(1,1,1, $m, 1, $y));

$time1 = mktime(0, 0, 0, $m, 1, $y);

$time2 = mktime(23, 59, 59, $m, $d, $y);


$sql = "SELECT * FROM `mod_taskhours` WHERE `created` BETWEEN $time1 AND $time2;";
$exec = mysql_query($sql);
$first = true;
$data = '';
while($taskhours = mysql_fetch_array($exec)) {
	if($first) {
		$data = date('j', $taskhours['created']);
		$first = false;
	} else {
		$data .= ','.date('j', $taskhours['created']);
	}
}
echo $data;
?>