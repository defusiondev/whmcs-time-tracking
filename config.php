<?php
$url = "http://www.yourdomain.co.za/whmcs/includes/api.php"; 				# URL to WHMCS API file
$username = "api";
# Admin username goes here
$password = "password";
# Admin password goes here
$billing_mail_from_name = 'Accounts'; 												# Name for email "from"
$billing_mail_from = 'accounts@yourdomain.co.za'; 						# Email address reports and invoices will be sent from
$auto_create_projects_tasks = false; 												# Automatically create a project and task for all new clients - set to false to disable
$default_hourly_rate = 400; 												# Default hourly rate for automatically created projects. They can be individually edited via the interface
$order_clients_by = 'firstname';															# Enter in a valid column from the tblclients table (firstname or lastname or companyname)
$default_task_name = 'Development';                       # Default task name
?>
