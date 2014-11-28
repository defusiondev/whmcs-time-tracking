<?php
	if(isset($_REQUEST['msgid']) && is_numeric($_REQUEST['msgid'])) {
		$msg = msgid2value($_REQUEST['msgid']);
		$html .= "<div style='color:orange; font-weight:bold;'>$msg</div>";
	}
	$sql = "
		SELECT p.id, p.name, p.modified, p.last_invoiced as liv, c.firstname, c.lastname
		FROM `mod_projects` as p, `tblclients` as c
		WHERE
			p.client_id = c.id
		ORDER BY c.firstname
		;";
	if($exec = mysql_query($sql)) {
		if (mysql_num_rows($exec) > 0) {
			$html .= '
				<script language="JavaScript">
					function doDelete(id) {
						if (confirm("Are you sure you want to delete this Project?")) {
							window.location="'.$modulelink.'&project=delete&id="+id;
						}
					}
				</script>
					';
			
			$html .= "<b><a href='$modulelink&project=add'>Add new Project</a></b><br /><br />";
			$html .= "
				<div class='tablebg'>
				";
			$html .= "
				<table class='datatable' cellpadding='3' cellspacing='1' border='0' width='100%'>
					<thead>
						<tr>
							<th>
								PID
							</th>
							<th>
								Client
							</th>
							<th>
								Name
							</th>
							<th>
								No Charge hours
							</th>
							<th>
								Billable hours
							</th>
							<th>
								Hours Invoiced
							</th>
							<th>
								Total hours
							</th>
							<th>
								&nbsp;
							</th>
							<th>
								&nbsp;
							</th>
						</tr>
					</thead>
				";
			$i = 0;
			while($project = mysql_fetch_array($exec)) {
				$sql2 = "
					SELECT th.hours as thhours, th.billed as thbilled
					FROM `mod_taskhours` as th, `mod_tasks` as t
					WHERE
						t.project_id = {$project['id']}
						AND
						th.task_id = t.id
					;";
				if($exec2 = mysql_query($sql2)) {
					$unbilled_hours = 0;
					$invoiced_hours = 0;
					$billed_hours = 0;
					$total_hours = 0;
					$total_billed = 0;
					while($taskhours = mysql_fetch_array($exec2)) {
						if($taskhours['thbilled'] == 0) {
							$unbilled_hours += $taskhours['thhours'];
						}
						if($taskhours['thbilled'] == 1) {
							$billed_hours += $taskhours['thhours'];
						}
						if($taskhours['thbilled'] == 2) {
							$invoiced_hours += $taskhours['thhours'];
						}
						$total_hours += $taskhours['thhours'];
					}
					$total_billed += $billed_hours;
					$project['unbilledhours'] = $unbilled_hours;
					$project['totalhours'] = $total_hours;
					$project['billed'] = $total_billed;
					$project['invoiced'] = $invoiced_hours;
				}
				$i++;
				
				$html .= "
					<tbody>
						<tr>
							<td>
								$i
							</td>
							<td>
								{$project['firstname']} {$project['lastname']}
							</td>							
							<td>
								{$project['name']}
							</td>
							<td>
								".(isset($project['unbilledhours']) ? $project['unbilledhours'] : 0)."
							</td>
							<td>
								".( ($project['billed'] > 0 ) ? $project['billed'] : 0)."
							</td>
							<td>
								{$project['invoiced']}
							</td>
							<td>
								".(isset($project['totalhours']) ? $project['totalhours'] : 0)."
							</td>
							<td>
								<a href='$modulelink&project=edit&id={$project['id']}'>edit</a>
							</td>
							<td>
								<a href='#' onclick='doDelete({$project['id']})'>delete</a>
							</td>
						</tr>
					";
			}
			$html .= "
					</tbody>
				</table>
				";
			$html .= "</div>";
		} else {
			$html .= "No Project in database.<br /> <a href='$modulelink&project=add'>Click here</a> to add new project.";
		}
	} else {
		$html .= "Error in query : <b>$query</b>";
	}
?>