<?php
if( $_SERVER["REQUEST_METHOD"] == "POST") {
	// Create Invoice in WHMCS for the said cient
	//pr($_POST);
	//die;

	/**
	 * Invoice create in WHMCS
	 */
	$postfields["username"] = $username;
	$postfields["password"] = md5($password);

	$postfields["action"] = "createinvoice";
	$postfields["userid"] = $_POST['client_id'];
	$postfields["date"] = date("Ymd");
	$postfields["duedate"] = date("Ymd");
	$postfields["paymentmethod"] = $_POST['payment_method'];
	$postfields["taxrate"] = $_POST['tax_rate'];
	$postfields["sendinvoice"] = false;

	$d1 = explode('/', $_REQUEST['date1']);
	$m = $d1[0];
	$d = $d1[1];
	$y = $d1[2];
	$time1 = mktime(0, 0, 0, $m, $d, $y);


	$d2 = explode('/', $_REQUEST['date2']);
	$m = $d2[0];
	$d = $d2[1];
	$y = $d2[2];
	$time2 = mktime(23, 59, 59, $m, $d, $y);

	$project_id = $_REQUEST['project_id'];
	$discount = $_REQUEST['discount'];
	$inclusive = $_REQUEST['inclusive'];

	// Load items to be added in the invoice
	$sql = "
		SELECT t.*, p.name as pname, th.hours as thhours, th.notes as thnotes, th.billed as thbilled, th.id as thid, th.created as thcreated
		FROM `mod_tasks` as t, `mod_projects` as p, `mod_taskhours` as th
		WHERE
			p.id = t.project_id
			AND
			t.id = th.task_id
			AND
			t.bill_to_client = 1
			AND
			th.billed = 1
			AND
			th.created BETWEEN $time1 AND $time2
			AND
			p.id = $project_id
			ORDER BY th.created
			; ";
	if($exec = mysql_query($sql)) {
		$i = 1;
		while($task = mysql_fetch_array($exec)) {
			$postfields["itemdescription$i"] = $task['name'] . " ( ". date("d F Y", $task['thcreated']). " - " . $task['thhours'] ." hours )";
			$postfields["itemamount$i"] = ($task['thhours'] * $task['rate']);
			if($discount > 0) {
				if($postfields["itemamount$i"] > $discount) {
					$postfields["itemamount$i"] -= $discount;
					$discount = 0;
				}
				if($postfields["itemamount$i"] == $discount) {
					$postfields["itemamount$i"] = 0;
					$discount = 0;
				}
				if($postfields["itemamount$i"] < $discount) {
					$discount -= $postfields["itemamount$i"];
					$postfields["itemamount$i"] = 0;
				}
			}
			if($inclusive == 1) {
				$postfields["itemtaxed$i"] = 0;
			} else {
				$postfields["itemtaxed$i"] = 1;
			}
			$sql2 = "UPDATE mod_taskhours SET billed = 2 WHERE id = {$task['thid']} LIMIT 1;";
			mysql_query($sql2);
			$i++;
		}
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);

	$data = explode(';', $data);
	$result_type = explode('=', $data[0]);
	if($result_type[1] == 'success') {
		$invoiceId = explode('=', $data[1]);
	}

	echo "Generated Invoice ID: ".$invoiceId[1];
	// Invoice create in WHMCS code over

} else {
	header("Location:$modulelink&invoice=list");
}
?>