<?php
if( $_SERVER["REQUEST_METHOD"] == "POST") {
	// Send Email to client
	//pr($_POST);

	/**
	 * Connect to API and fetch CLIENT details using client_id
	 */
	$postfields["username"] = $username;
	$postfields["password"] = md5($password);

	$postfields["clientid"] = trim($_POST['client_id']);
	$postfields["action"] = "getclientsdetails";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);

	$client = array ();
	$clientinfo = explode(';', $data);
	foreach ($clientinfo as $k => $v) {
		$d1 = explode('=', $v);
		$client[$d1[0]] = $d1[1];
	}
	//pr($client);

	/**
	 * Currency symbol code begin
	 */
	$postfields["username"] = $username;
	$postfields["password"] = md5($password);
	$postfields["action"] = "getcurrencies";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);


	$currency_xml = new SimpleXMLElement($data);
	$currency_symbol = $currency_xml->currencies->currency[0]->prefix;

	// Currency symbol code end

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
	$client_id = $_REQUEST['client_id'];

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
	$sql_project = "SELECT * FROM `mod_projects` WHERE `id` = $project_id LIMIT 1;";
	if($exec_p = mysql_query($sql_project)) {
		$info = mysql_fetch_array($exec_p);
	} else {
		//echo $sql_project;
	}

	if($exec = mysql_query($sql)) {

		$html .= "
			<br />
			<br />

		<table>
			<thead>
				<tr>
					<th>Client </th>
					<th> : </th>
					<th>". ucwords(strtolower($client['firstname'])) ." ". ucwords(strtolower($client['lastname'])). "</th>
				</tr>
				<tr>
					<th>Project </th>
					<th> : </th>
					<th>". ($info['name']) . "</th>
				</tr>
				<tr>
					<th>Date range </th>
					<th> : </th>
					<th>". (date("F d, Y", $time1))." - ".(date("F d, Y", $time2)). "</th>
				</tr>
			</thead>
		</table>
			";
		$email = "
			<br />
			<br />

		<table>
			<thead>
				<tr>
					<th>Client </th>
					<th> : </th>
					<th>". ucwords(strtolower($client['firstname'])) ." ". ucwords(strtolower($client['lastname'])). "</th>
				</tr>
				<tr>
					<th>Project </th>
					<th> : </th>
					<th>". ($info['name']) . "</th>
				</tr>
				<tr>
					<th>Date range </th>
					<th> : </th>
					<th>". (date("F d, Y", $time1))." - ".(date("F d, Y", $time2)). "</th>
				</tr>
			</thead>
		</table>
			";
		$html .= "
			<div class='tablebg'>
			";
		$email .= '
		<table width="900" border="0" cellpadding="0" cellspacing="0" style="color:#000; font-size:13px; font-weight:normal; font-family:Arial, Helvetica, sans-serif;">
		  <tr>
			<td>
				<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
					<thead>
						<tr bgcolor="#003399" >
							<td style="padding:0 0 0 10px; color:#FFFFFF;">S.No.</td>
							<td style="padding:0 0 0 10px; color:#FFFFFF;">Task</td>
							<td style="padding:0 0 0 10px; color:#FFFFFF;">Notes</td>
							<td style="padding:0 0 0 10px; color:#FFFFFF;">Rate</td>
							<td style="padding:0 0 0 10px; color:#FFFFFF;">Hours</td>
							<td style="padding:0 0 0 10px; color:#FFFFFF;">Total</td>
							<td style="padding:0 0 0 10px; color:#FFFFFF;">Date</td>
						</tr>
					</thead>
					<tbody>';
		$html .= "
			<table class='datatable' cellpadding='3' cellspacing='1' border='0' width='100%'>
				<thead>
					<tr>
						<th>
							S.No.
						</th>
						<th>
							Task
						</th>
						<th>
							Notes
						</th>
						<th>
							Rate
						</th>
						<th>
							Hours
						</th>
						<th>
							Total
						</th>
						<th>
							Date
						</th>
					</tr>
				</thead>
				<tbody>
			";
		if (mysql_num_rows($exec) > 0) {
			$i = 0;
			$total_hours = 0;
			$total_amount = 0;
			while($project = mysql_fetch_array($exec)) {
				$total_hours += $project['thhours'];
				$total_amount += ( $project['thhours'] * $project['rate'] );
				$i++;
				$email .= '
					<tr>
						<td style="padding:0 0 0 10px; color:#000000;">'.$i.'</td>
						<td style="padding:0 0 0 10px; color:#000000;">'. $project['name'] .'</td>
						<td style="padding:0 0 0 10px; color:#000000;">'. $project['thnotes'] .'</td>
						<td style="padding:0 0 0 10px; color:#000000;">'. $currency_symbol.$project['rate'] .'</td>
						<td style="padding:0 0 0 10px; color:#000000;">'. $project['thhours'] .'</td>
						<td style="padding:0 0 0 10px; color:#000000;">'. $currency_symbol.( $project['thhours'] * $project['rate'] ) .'</td>
						<td style="padding:0 0 0 10px; color:#000000;">'. ( date("F d, Y", $project['thcreated']) ) .'</td>
					</tr>
					';
				$html .= "
						<tr>
							<td>
								$i
							</td>
							<td>
								{$project['name']}
							</td>
							<td>
								{$project['thnotes']}
							</td>
							<td>
								{$project['rate']}
							</td>
							<td>
								{$project['thhours']}
							</td>
							<td>
								".( $project['thhours'] * $project['rate'])."
							</td>
							<td>
								".( date("F d, Y", $project['thcreated']) )."
							</td>
						</tr>
					";
			}
			$email .= "
				<tr>
					<td colspan='7'>
						&nbsp;<br /><br />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						Total hours:
					</td>
					<td colspan='1'>
						<b>$total_hours</b>
					</td>
					<td colspan='4'>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						Total amount:
					</td>
					<td colspan='1'>
						<b>$currency_symbol$total_amount</b>
					</td>
					<td colspan='4'>
						&nbsp;
					</td>
				</tr>
			";
		} else {
			$email .= "
				<tr>
					<td colspan='7'>
						<div class='tablebg' style='text-align:center'>No results</div>
					</td>
				</tr>
			";
		}
		$html .= "
				</tbody>
			</table>
			";
		$email .= '				</tbody>
							</table>
						</td>
					  </tr>
					</table>
					';
		$html .= "</div>";
	}


	$email2 = '
	<table width="900" border="0" cellpadding="0" cellspacing="0" style="color:#000; font-size:13px; font-weight:normal; font-family:Arial, Helvetica, sans-serif;">
	  <tr>
		<td>
			<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
				<thead>
					<tr bgcolor="#003399" >
						<td style="padding:0 0 0 10px; color:#FFFFFF;">Project</td>
						<td style="padding:0 0 0 10px; color:#FFFFFF;">Task</td>
						<td style="padding:0 0 0 10px; color:#FFFFFF;">Description</td>
						<td style="padding:0 0 0 10px; color:#FFFFFF;">Hours</td>
						<td style="padding:0 0 0 10px; color:#FFFFFF;">Rate</td>
						<td style="padding:0 0 0 10px; color:#FFFFFF;">Hours * Rate</td>
						<td style="padding:0 0 0 10px; color:#FFFFFF;">Billed</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding:0 0 0 10px; color:#000000;">Project2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
					</tr>
					<tr>
						<td style="padding:0 0 0 10px; color:#000000;">Project2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
						<td style="padding:0 0 0 10px; color:#000000;">Task2</td>
					</tr>
				</tbody>
			</table>
		</td>
	  </tr>
	</table>
	';

	$to = "{$client['email']}";

	$subject = 'Report of Billed Hours';

	$headers = "From: " .$billing_mail_from_name." <".$billing_mail_from.">" . "\r\n";
	$headers .= "Reply-To: ". $billing_mail_from . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html;\r\n";

	$message = $email;

	$html = '';
	if (mail($to, $subject, $message, $headers)) {
	  echo 'Your message has been sent.';
	} else {
	  echo 'There was a problem sending the email.';
	}


} else {
	header("Location:$modulelink&invoice=list");
}
?>