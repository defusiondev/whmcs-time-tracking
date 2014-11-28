<?php
switch($task) {
	case 'list' :
		include_once 'task_list.php';
		break;
	case 'add' :
		include_once 'task_add.php';
		break;
	case 'edit' :
		include_once 'task_edit.php';
		break;
	case 'delete' :
		include_once 'task_delete.php';
		break;
	default :
		include_once 'task_list.php';
		break;
}
?>