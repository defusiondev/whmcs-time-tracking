<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$client = getClients($_REQUEST['client_id']);
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
	
	$sql = "SELECT t.*, p.name as pname, th.hours as thhours, th.notes as thnotes, th.billed as thbilled, th.id as thid, th.created as thcreated
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
				ORDER BY th.created";
				
	$sql_project = "SELECT * FROM `mod_projects` WHERE `id` = $project_id LIMIT 1";
		
	if ($exec_p = mysql_query($sql_project)) {
		$info = mysql_fetch_array($exec_p);
	}
	
	foreach ($client as $cl) {
		$cname = $cl;
	}

	if($exec = mysql_query($sql)) {
		$html .= "<table>
			<thead>
				<tr>
					<th>Client </th>
					<th> : </th>
					<th>".$cname. "</th>
				</tr>
				<tr>
					<th>Project </th>
					<th> : </th>
					<th>". ($info['name']) . "</th>
				</tr>
				<tr>
					<th>Date Range </th>
					<th> : </th>
					<th>". (date("F d, Y", $time1))." - ".(date("F d, Y", $time2)). "</th>
				</tr>
			</thead>
		</table>";
		
		$html .= "<div class='tablebg'>";
		
		$html .= "<table class='datatable' cellpadding='3' cellspacing='1' border='0' width='100%'>
				<thead>
					<tr>
						<th>
							#
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
								$currency_symbol"."{$project['rate']}
							</td>
							<td>
								{$project['thhours']}
							</td>
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
						&nbsp;<br /><br />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<strong>Total Hours:</strong>
					</td>
					<td colspan='1'>
						<strong>$total_hours</strong>
					</td>
					<td colspan='4'>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<strong>Total Amount:</strong>
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
			$i = 0;
			$html .= "
				<tr>
					<td colspan='7'>
						<div class='tablebg' style='text-align:center'>* No Results</div>
					</td>
				</tr>
			";
		}
		$html .= "
				</tbody>
			</table>
			";
		$html .= "</div>";
		$forms = "
			<form action='$modulelink&invoice=email' method='post'>
				<input type='hidden' name='project_id' value='$project_id' />
				<input type='hidden' name='client_id' value='$client_id' />
				<input type='hidden' name='date1' value='{$_REQUEST['date1']}' />
				<input type='hidden' name='date2' value='{$_REQUEST['date2']}' />
				<input type='submit' name='email' value='Email to client' />
			</form>
			<br />
			<div style='border:1px solid #CCCCCC; padding: 10px; width:auto;'>
				<h3>Create Invoice</h3><br/>
				<form action='$modulelink&invoice=create' method='post'>
					<input type='hidden' name='project_id' value='$project_id' />
					<input type='hidden' name='client_id' value='$client_id' />
					<input type='hidden' name='date1' value='{$_REQUEST['date1']}' />
					<input type='hidden' name='date2' value='{$_REQUEST['date2']}' />
					<input type='hidden' name='total_amount' value='$total_amount' />
					<p>Payment Method:<br/>$select</p>
					<p>Tax Rate:<br/>$select2</p>
					<p>Amount to Discount:<br/><input type='text' name='discount' value='0' /></p>
					<br />
					<input type='submit' name='submit' value='Create Invoice' />
				</form>
			</div>
			<br />";
		if($_REQUEST['report_type'] == 'rt1' && $i > 0) {
			$html = $forms . $html;
		}

	}
} else {
	$clients = getClients();
	$select = '<select id="client_id" name="client_id" onchange="loadProjectForClient()"><option value="0">Select Client</option>';
	foreach ($clients as $k => $v) {
		if (isset($project) && $project['client_id'] == $k) 
			$select .= '<option selected="selected" value="'.$k.'">'.$v.'</option>';			
		else 
			$select .= '<option value="'.$k.'">'.$v.'</option>';
	}
	$select .= '</select>';
?>
<script type="text/javascript">
	$(function() {
		$("#date1").datepicker();
		$("#date2").datepicker();
	});
	function loadProjectForClient() {
		client_id = $("#client_id").val();
		url = '../modules/admin/time_tracking/time_tracking.php?' + 'ajax=true&action=fetchProjectsForClient&client_id=' + client_id;
		$("#projectDiv").load(url);
	}
</script>
	<form action="<?php echo $modulelink?>&invoice=list" name="invoice" method="post">
		<table>
			<tr>
				<td class="text_project1">
					<b>Select Client</b>:
				</td>
				<td class="text_project1">
				<?php
					echo $select;
				?>
				</td>
			</tr>
			<tr>
				<td class="text_project1">
					<b>Select Project</b>:
				</td>
				<td class="text_project1">
					<div id="projectDiv">
						<select id="project_id" name="project_id">
							<option value="0">---</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td class="text_project1">
					<b>Date Range</b>:
				</td>
				<td class="text_project1">
					<input type="text" name="date1" id="date1" value="<?php echo date('m/d/Y', strtotime('-1 month')) ?>" />
					&nbsp; &nbsp; to &nbsp; &nbsp;
					<input type="text" name="date2" id="date2" value="<?php echo date('m/d/Y') ?>" />
				</td>
			</tr>
			<tr>
				<td class="text_project1">
					<b>Report Type</b>:
				</td>
				<td style="padding:5px 0 0 20px;">
					<p style="margin-bottom:4px;"><label><input type="radio" name="report_type" id="rt1" value="rt1" />
					Invoice Report</label></p>
					<p style="margin-bottom:4px;"><label><input type="radio" name="report_type" id="rt2" value="rt2" checked="checked" />
					All Hours Report</label></p>
					<p style="margin-bottom:4px;"><label><input type="radio" name="report_type" id="rt3" value="rt3" />
					All Clients Report</label></p>
				</td>
			</tr>
			<tr>
				<td class="text_project1">
					&nbsp;
				</td>
				<td>
					<input type="submit" name="generate" id="generate" value="View Report" />
				</td>
			</tr>
		</table>
	</form>
<?php
}
?>