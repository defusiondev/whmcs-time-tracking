<?php
	/**
	 * Load html for AJAX request
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

	//echo serialize($data);
	$currency_xml = new SimpleXMLElement($data);
	$currency_symbol = $currency_xml->currencies->currency[0]->prefix;

	$m = $_REQUEST['m'];
	$d = $_REQUEST['d'];
	$y = (filter_var($_REQUEST['y'], FILTER_VALIDATE_INT)) ? $_REQUEST['y'] : 2000;

	$time1 = mktime(0, 0, 0, $m, $d, $y);
	$time2 = mktime(23, 59, 59, $m, $d, $y);


	$today = date("F d, Y", $time1);
	$sql = "
		SELECT t.*, p.name as pname, th.hours as thhours, th.notes as thnotes, th.billed as thbilled, th.id as thid
		FROM `mod_tasks` as t, `mod_projects` as p, `mod_taskhours` as th
		WHERE
			p.id = t.project_id
			AND
			t.id = th.task_id
			AND
			t.bill_to_client = 1
			AND
			th.created BETWEEN $time1 AND $time2
			; ";
	if($exec = mysql_query($sql)) {
		$ajax .= "
			<div class='tablebg'>
			";
		$ajax .= "
			<table class='datatable' cellpadding='3' cellspacing='1' border='0' width='100%'>
				<thead>
					<tr>
						<th width='10%'>
							Project
						</th>
						<th width='10%'>
							Task
						</th>
						<th width='20%'>
							Description
						</th>
						<th width='10%'>
							Rate
						</th>
						<th width='10%'>
							Hours
						</th>
						<th width='15%'>
							Hours x Rate
						</th>
						<th width='15%'>
							&nbsp;
						</th>
						<th>
							&nbsp;
						</th>
					</tr>
				</thead>
				<tbody>
			";
		if(mysql_num_rows($exec) > 0 ) {
			$billed = 0;
			$unbilled = 0;
			$total = 0;
			$invoiced = 0;
			$modulelink .= "addonmodules.php?module=time_tracking";
			while( $task = mysql_fetch_array($exec)) {
				if($task['thbilled'] == 1) {
					$billed += $task['thhours'];
				} elseif($task['thbilled'] == 0) {
					$unbilled += $task['thhours'];
				} elseif($task['thbilled'] == 2) {
					$invoiced += $task['thhours'];
				}
				$ajax .= "
						<tr>
							<td>
								{$task['pname']}
							</td>
							<td>
								{$task['name']}
							</td>
							<td>
								{$task['thnotes']}
							</td>
							<td align='center'>
								$currency_symbol".(($task['bill_to_client'] == 1) ? $task['rate'] : '0')."
							</td>
							<td align='center'>
								{$task['thhours']}
							</td>
							<td align='left' style='padding-left:5px;'>
								$currency_symbol".($task['thhours'] * $task['rate'])."
							</td>
							<td>
								".(
									($task['thbilled'] == 1)
									?
										"Billed" . "
										(
										<span
											style='
												cursor:pointer;
												color:blue;
												text-decoration:underline;
												'
												onclick='markUnbilled({$task['thid']});'>
										Unbill
										</span>
										)"
									:
										(
										($task['thbilled'] == 2)
										?
										"Invoiced	( <span
												style='
													cursor:pointer;
													color:blue;
													text-decoration:underline;
													'
												onclick='
													if(
														confirm(\"Are you sure? This will reset the selected hours and they will no longer appear as being invoiced. Click OK if you would like to do this. \")
														) {
														markBilled({$task['thid']})
														}
														;'>
											Bill
											</span> )
										"
										:
										"	<span
												style='
													cursor:pointer;
													color:blue;
													text-decoration:underline;
													'
												onclick='markBilled({$task['thid']});'>
											Bill
											</span>
										"
										)
									)."
							</td>
							<td>
								<a href='$modulelink&taskhours=edit&id={$task['thid']}'>Edit</a>
							</td>
						</tr>
					";
			}
			$total = $billed + $unbilled + $invoiced;
				$ajax .= "
						<tr>
							<td colspan='6'>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td colspan='3'>
								&nbsp;
							</td>
							<td align='right' style='padding-right:5px;'>
								Billed hours:
							</td>
							<td align='left' style='padding-left:5px;'>
								<b>$billed</b>
							</td>
							<td>
								&nbsp;
							</td>
							<td>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td colspan='3'>
								&nbsp;
							</td>
							<td align='right' style='padding-right:5px;'>
								Invoiced hours:
							</td>
							<td align='left' style='padding-left:5px;'>
								<b>$invoiced</b>
							</td>
							<td>
								&nbsp;
							</td>
							<td>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td colspan='3'>
								&nbsp;
							</td>
							<td align='right' style='padding-right:5px;'>
								Unbilled hours:
							</td>
							<td align='left' style='padding-left:5px;'>
								<b>$unbilled</b>
							</td>
							<td>
								&nbsp;
							</td>
							<td>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td colspan='2'>
								&nbsp;
							</td>
							<td colspan='2' align='right' style='padding-right:5px;'>
								<b>Total hours for $today:</b>
							</td>
							<td align='left' style='padding-left:5px;'>
								<b>$total</b>
							</td>
							<td>
								&nbsp;
							</td>
							<td>
								&nbsp;
							</td>
						</tr>
					";
		} else {
			$date = date("F d, Y", $time1);
			$ajax .= "
				<tr>
					<td colspan='6'>
						<div class='tablebg' style='text-align:center'>No results for <b>$date</b></div>
					</td>
				</tr>
			";
		}
		$ajax .= "
				</tbody>
			</table>
			";
		$ajax .= "</div>";
	}
?>