<?php
if( $_SERVER["REQUEST_METHOD"] == "POST") {
	$client = getClients();
	$currency_symbol = getCurrencies();
	$payment_methods = getPaymethods(); 
	$select = '<select name="payment_method">';
	foreach ($payment_methods as $k => $v) {
		$select .= '<option value="'.$k.'">'.$v.'</option>';
	}
	$select .= '</select>';
	$tax_rates = getTaxRates();
	$select2 = '<select name="tax_rate">';
	foreach ($tax_rates as $k => $v) {
		$select2 .= '<option value="'.$k.'">'.$v.'</option>';
	}
	$select2 .= '</select>';

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
	$time_liv = time();
	$update_liv = "UPDATE `mod_projects` SET `last_invoiced` = $time_liv WHERE `id` = $project_id LIMIT 1;";
	mysql_query($update_liv);

	$sql = "
		SELECT t.*,
			p.name as pname,
			th.hours as thhours,
			th.notes as thnotes,
			th.billed as thbilled,
			th.id as thid,
			th.created as thcreated,
			c.firstname,
			c.lastname
		FROM
			`mod_tasks` as t,
			`mod_projects` as p,
			`mod_taskhours` as th,
			`tblclients` as c
		WHERE
			p.id = t.project_id
				AND
			t.id = th.task_id
				AND
			t.bill_to_client = 1
				AND
			th.created BETWEEN $time1 AND $time2
				AND
			p.id = $project_id
				AND
			c.id = p.client_id
		ORDER BY th.created";
		
	if($_REQUEST['report_type'] == 'rt3') {
		$sql = "
			SELECT
				t.*,
				p.name as pname,
				th.hours as thhours,
				th.notes as thnotes,
				th.billed as thbilled,
				th.id as thid,
				th.created as thcreated,
				c.firstname,
				c.lastname
			FROM
			`mod_tasks` as t,
			`mod_projects` as p,
			`mod_taskhours` as th,
			`tblclients` as c
			WHERE
				p.id = t.project_id
				AND
				t.id = th.task_id
				AND
				t.bill_to_client = 1
				AND
				th.created BETWEEN $time1 AND $time2
				AND
				c.id = p.client_id
				ORDER BY th.created";
	}
	$sql_project = "SELECT * FROM `mod_projects` WHERE `id` = $project_id LIMIT 1;";
	
	if($client_id == 'All') {
		$sql_project = "SELECT * FROM `mod_projects` LIMIT 1;";
	}
	
	if($exec_p = mysql_query($sql_project)) {
		$info = mysql_fetch_array($exec_p);
	}
	
	if($exec = mysql_query($sql)) {
		$html .= "<div class='tablebg'>";
		$html .= "<table class='datatable' cellpadding='3' cellspacing='1' border='0' width='100%'>
				<thead>
					<tr>
						<th>
							#
						</th>
						<th>
							Client Name
						</th>
						<th>
							Project
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
							Status
						</th>
						<th>
							Total
						</th>
						<th>
							Date
						</th>
					</tr>
				</thead>
				<tbody>";
				
		if (mysql_num_rows($exec) > 0) {
			$i = 0;
			$total_hours = 0;
			$total_amount = 0;
			while($project = mysql_fetch_array($exec)) {
				$total_hours += $project['thhours'];
				$total_amount += ( $project['thhours'] * $project['rate'] );
				$i++;
				$html .= "
						<tr>
							<td>
								$i
							</td>
							<td>
								{$project['firstname']} {$project['lastname']}
							</td>
							<td>
								{$project['pname']}
							</td>
							<td>
								{$project['name']}
							</td>
							<td>
								{$project['thnotes']}
							</td>
							<td>
								$currency_symbol"."{$project['rate']}
							</td>
							<td>
								{$project['thhours']}
							</td>
							<td>". ( $project['thbilled'] == 1 ? "Billed" : ($project['thbilled'] == 2 ? "Invoiced" : "Unbilled") )."</td>
							<td>
								$currency_symbol".( $project['thhours'] * $project['rate'])."
							</td>
							<td>
								".( date("F d, Y", $project['thcreated']) )."
							</td>
						</tr>
					";
			}
			$html .= "
				<tr>
					<td colspan='7'>
						&nbsp;<br />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						Total Hours:
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
						Total Amount:
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
			$html .= "
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
		$html .= "</div>";
	}
}
?>