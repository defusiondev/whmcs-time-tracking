<?php
$action = filter_var($_REQUEST['action'], FILTER_SANITIZE_STRING);
$ajax = '';
switch($action) {
	case 'todaysTasks':
		include 'ajax_todaystasks.php';
		break;
	case 'addTaskHourRate':
		include 'ajax_addTaskHourRateForm.php';
		break;
	case 'loadTaskForProject':
		include 'ajax_loadTaskForProject.php';
		break;
	case 'saveHoursForProject':
		include 'ajax_saveHoursForProject.php';
		break;
	case 'markBilled':
		include 'ajax_markBilled.php';
		break;
	case 'getDates':
		include 'ajax_getDates.php';
		break;
	case 'markUnbilled':
		include 'ajax_markUnbilled.php';
		break;
	case 'fetchProjectsForClient':
		include 'ajax_fetchProjectsForClient.php';
		break;
}
echo $ajax;
?>