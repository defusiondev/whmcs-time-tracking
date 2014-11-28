<?php
switch($invoice) {
	case 'list' :
		if($_REQUEST['report_type'] == 'rt3') {
			//If Report of ALL clients is required
			include_once('invoice_all_client_report.php');
			break;
		} elseif($_REQUEST['report_type'] == 'rt1') {
			include_once 'invoice_list.php';
			break;
		} elseif($_REQUEST['report_type'] == 'rt2') {
			include_once 'invoice_detailed_list.php';
			break;
		} else {
			include_once 'invoice_list.php';
		}
		break;
	case 'email' :
		include_once 'invoice_email.php';
		break;
	case 'create' :
		include_once 'invoice_create.php';
		break;
}
?>