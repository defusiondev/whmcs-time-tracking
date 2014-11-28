<?php
// If file called directly, then connect via the WHMCS configuration file, included in connect.php (Required for AJAX actions)
if(!isset($db_name)) {
	//echo "Connection required<br />";
	require_once 'connect.php';
} else {
	//echo "Already connected<br />";
}

# Module configuration
function time_tracking_config() {
    $configarray = array(
        "name" => "Time Tracking",
        "version" => "2.0",
        "author" => "Nobody",
        "language" => "english",
        "fields" => array(
            "licencekey" => array("FriendlyName" => "Licence Key", "Type" => "text", "Size" => "100","Description" => "Enter module Licence Key", "Default" => ""),
            "localkey" => array("FriendlyName" => "", "Type" => "hidden", "Size" => "255","Description" => ""),
            "dateformat" => array("FriendlyName" => "Date Format", "Type" => "radio", "Options" => "dd-mm-yy,mm/dd/yy", "Default" => "mm/dd/yy"),
        )
    );
    return $configarray;	
}

require_once 'config.php';
require_once 'functions.php';

// File path, including the trailing "/"
$ttPath = substr($_SERVER['SCRIPT_FILENAME'], 0, -17);
if(
		!mysql_num_rows( mysql_query("SHOW TABLES LIKE 'mod_tasks'"))
		||
		!mysql_num_rows( mysql_query("SHOW TABLES LIKE 'mod_projects'"))
		||
		!mysql_num_rows( mysql_query("SHOW TABLES LIKE 'mod_taskhours'"))
		) {
		
	if (!$_GET["install"]) {
		echo '
<p><strong>Time tracking module is either not installed, or is missing some tables that are required for its working.</strong></p>
<p>To install it, click on the button below.</p>
<p><input type="button" value="Install Time Tracking Module" onclick="window.location=\''.$modulelink.'&install=true\'"></p>
';
	} else {
		$query1 = "
			CREATE TABLE IF NOT EXISTS `mod_projects` (
			  `id` int(8) NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `client_id` int(8) NOT NULL,
			  `status` int(1) NOT NULL,
			  `last_invoiced` bigint(12) NOT NULL,
			  `modified` bigint(12) NOT NULL,
			  `created` bigint(12) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
				";
		$query2 = "
			CREATE TABLE IF NOT EXISTS `mod_taskhours` (
			  `id` int(8) NOT NULL AUTO_INCREMENT,
			  `task_id` int(8) NOT NULL,
			  `hours` float NOT NULL,
			  `notes` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			  `billed` int(1) NOT NULL,
			  `created` bigint(12) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
			";
		$query3 = "
			CREATE TABLE IF NOT EXISTS `mod_tasks` (
			  `id` int(8) NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `bill_to_client` int(1) NOT NULL,
			  `rate` float NOT NULL,
			  `project_id` int(8) NOT NULL,
			  `modified` bigint(12) NOT NULL,
			  `created` bigint(12) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
			";
		if(mysql_query($query1) && mysql_query($query2) && mysql_query($query3)) {
			header("Location: $modulelink&installation=success");
		} else {
			header("Location: $modulelink&installation=failure");
		}
		exit;
	}
} else {
	$html = '';
	//$html .= '<link href="../modules/admin/time_tracking/css/admin.css" rel="stylesheet" type="text/css" />';
	if(isset($_REQUEST['ajax']) ) {
		include($ttPath."ajax.php");
	} else {
		$html .= '<link href="../modules/admin/time_tracking/css/form.css" rel="stylesheet" type="text/css" />';
		$html .= '<link href="../modules/admin/time_tracking/css/form2.css" rel="stylesheet" type="text/css" />';
		menu($modulelink, false);
	}

	// Get request variables
	$project = isset($_REQUEST['project']) ? trim($_REQUEST['project']) : "" ;
	$task = isset($_REQUEST['task']) ? trim($_REQUEST['task']) : "" ;
	$timesheet = isset($_REQUEST['timesheet']) ? trim($_REQUEST['timesheet']) : "" ;
	$invoice = isset($_REQUEST['invoice']) ? trim($_REQUEST['invoice']) : "" ;
	$taskhours = isset($_REQUEST['taskhours']) ? trim($_REQUEST['taskhours']) : "" ;


	if(trim($project) != '') {
		include_once('project_management.php');
	}
	if(trim($task) != '') {
		include_once('task_management.php');
	}
	if(trim($timesheet) != '') {
		include_once('timesheet.php');
	}
	if(trim($invoice) != '') {
		include_once('invoice.php');
	}
	if(trim($taskhours) != '') {
		include_once('taskhours_management.php');
	}
	echo $html;
}
?>