<?php

	// fetch clients from db;
	function getClients($id = null) {
		global $order_clients_by;
		if ($id == null)
			$sql = 'select id, firstname, lastname, companyname from tblclients where id in (select client_id from mod_projects) order by '.$order_clients_by;
		else 
			$sql = 'select id, firstname, lastname, companyname from tblclients where id = '.$id;
		$res = @mysql_query($sql);
		$return = array();
		if (!$res || mysql_num_rows($res)<1) {
			return $return;
		} else {
			while ($row = mysql_fetch_assoc($res)) {
				$name = $row['firstname'].' '.$row['lastname'];
				if ($row['companyname'] != '') {
					$name .= ' - '.$row['companyname'];
				}
				$return[$row['id']] = $name;
			}
		}
		return $return;
	}

	// get currencies from db
	function getCurrencies() {
		$sql = 'select `prefix` from tblcurrencies where `default` = 1';
		$res = @mysql_query($sql);
		if (!$res || mysql_num_rows($res)<1) {
			return '';
		} else {
			$row = mysql_fetch_assoc($res);
			return $row['prefix'];
		}
	}

	// get payment methods from api
	function getPayMethods() {
		global $username;
		global $password;
		global $url;
		$postfields["username"] = $username;
		$postfields["password"] = md5($password);
		$postfields["action"] = "getpaymentmethods";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		$data = curl_exec($ch);
		curl_close($ch);
		
		$xml = new SimpleXMLElement($data); 
		$return = array();
		if (!$xml) {
			return array('0' => 'No payment gateway');
		} else {
			if ($xml->totalresults > 0) {
				for($i = 0; $i < $xml->totalresults; $i++) {
					$return[(string)$xml->paymentmethods->paymentmethod[$i]->module] = (string)$xml->paymentmethods->paymentmethod[$i]->displayname. " - ".(string)$xml->paymentmethods->paymentmethod[$i]->module;
				}
			}
			return $return;
		}
	}

	function getTaxRates() {
		$sql = 'select taxrate, name FROM tbltax order by name';
		$res = @mysql_query($sql);
		
		$return = array('0' => 'No Tax');
		if (!$res || mysql_num_rows($res) < 1) {
			return $return;
		} else {
			while ($row = mysql_fetch_assoc($res)) {
				$return[$row['taxrate']] = $row['name'].' ('.$row['taxrate'].')';
			}
			return $return;
		}
	}

function check_license($licensekey,$localkey="") {
    return true;
}

// End Check Function



/**
 * Function list
 */

// Function to display an Array in a human-readable form
	function pr($array, $die = false, $dieMessage = '') {
		if(is_array($array)) {
			echo '<pre>';
			print_r($array);
			echo '</pre>';
		} else {
			echo '<pre>';
			echo $arrays;
			echo '</pre>';
		}
		if($die) {
			die("$dieMessage");
		} else {
			echo $dieMessage;
		}
	}

	function msgid2value($id, $return = true) {
		switch ($id) {
			case 1:
				$msgstr = "Project added successfully";
				break;
			case 2:
				$msgstr = "Project could not be added";
				break;
			case 3:
				$msgstr = "Project updated successfully";
				break;
			case 4:
				$msgstr = "Project could not be updated successfully";
				break;
			case 5:
				$msgstr = "Project deleted successfully";
				break;
			case 6:
				$msgstr = "Project could not be deleted";
				break;
			case 7:
				$msgstr = "Project not found";
				break;
			case 8:
				$msgstr = "Task added successfully";
				break;
			case 9:
				$msgstr = "Task could not be added";
				break;
			case 10:
				$msgstr = "Task updated successfully";
				break;
			case 11:
				$msgstr = "Task could not be updated successfully";
				break;
			case 12:
				$msgstr = "Task deleted successfully";
				break;
			case 13:
				$msgstr = "Task could not be deleted";
				break;
			case 14:
				$msgstr = "Task not found";
				break;
			case 15:
				$msgstr = "Project could not be added because data was not appropriate";
				break;
			case 16:
				$msgstr = "Task Hours edited";
				break;
			case 17:
				$msgstr = "Task Hours NOT edited";
				break;
		}
		if($return) {
			return $msgstr;
		} else {
			echo $msgstr;
		}

	}
	
	function menu($modulelink, $return = true) {
		$menu = "
			<b><a href='$modulelink&timesheet=list'>Timesheet</a></b>
			|
			<b><a href='$modulelink&project=list'>Projects</a></b>
			|
			<b><a href='$modulelink&task=list'>Tasks</a></b>
			|
			<b><a href='$modulelink&invoice=list'>Reports</a></b>
		<br />
		<br />
			";
		if($return) {
			return $menu;
		} else {
			echo $menu;
		}
	}


	/**
	 * Code to insert a sample project and a sample task for that project if there is no previous project for a client.
	 */
		$sql1 =  "SELECT id, firstname, lastname, companyname FROM `tblclients` order by ".$order_clients_by;
		$clients = array ();
		if($exec1 = mysql_query($sql1)) {
			if(mysql_num_rows($exec1) > 0) {
				$ids = '';
				$first = true;
				while( $client = mysql_fetch_array($exec1)) {
					$clients[] = $client;
					if($first) {
						$ids .= $client['id'];
						$first = false;
					} else {
						$ids .= ', '. $client['id'];
					}
				}
			}
		}
		if($auto_create_projects_tasks && count($clients) > 0 ) {
			foreach($clients as $k => $client) {
				$sql2 = "SELECT * FROM `mod_projects` WHERE `client_id` = {$client['id']}";
				if($exec2 = mysql_query($sql2)) {
					if(mysql_num_rows($exec2) == 0 ) {
						$time = time();
						if (trim($client['companyname']) != '') {
							$description = ucwords(strtolower($client['companyname'])).' - Project';
						} else {
							$description = ucwords(strtolower($client['firstname'].' '.$client['lastname'])).' - Project';
						}
						$sql3 = "
							INSERT INTO
								`mod_projects`
									(`name`, `description`, `client_id`, `status`, `created`)
								VALUES
									('$description', 'Project for Client ID {$client['id']}', {$client['id']}, 1, $time);
							";
						if(mysql_query($sql3)) {
							$time = time();
							$last_insert_id = mysql_insert_id();
							if (!isset($default_task_name)) {
								$description = $default_task_name;
							} elseif (trim($client['companyname']) != '') {
								$description = ucwords(strtolower($client['companyname'])).' - Task';
							} else {
								$description = ucwords(strtolower($client['firstname'].' '.$client['lastname'])).' - Task';
							}
							$sql4 = "
								INSERT INTO
									`mod_tasks`
										(`name`, `description`, `bill_to_client`, `rate`, `project_id`, `created`)
									VALUES
										('$description', 'Task for Client ID {$client['id']}', 1, $default_hourly_rate, $last_insert_id, $time);
								";
							mysql_query($sql4);
						}
					}
				}
			}
		}
	

?>