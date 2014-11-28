<?php
switch($project) {
	case 'list' :
		include_once 'project_list.php';
		break;
	case 'add' :
		include_once 'project_add.php';
		break;
	case 'edit' :
		include_once 'project_edit.php';
		break;
	case 'delete' :
		include_once 'project_delete.php';
		break;
}
?>