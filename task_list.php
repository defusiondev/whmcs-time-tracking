<?php
	if(isset($_REQUEST['msgid']) && is_numeric($_REQUEST['msgid'])) {
		$msg = msgid2value($_REQUEST['msgid']);
		$html .= "<div style='color:orange; font-weight:bold;'>$msg</div>";
	}
	$sql = "
		SELECT t.*, p.name as pname, c.firstname as fname, c.lastname as lname
		FROM `mod_tasks` as t, `mod_projects` as p, `tblclients` as c
		WHERE p.id = t.project_id
		AND
		p.client_id = c.id
		ORDER BY c.firstname
		;";
	if($exec = mysql_query($sql)) {
		if (mysql_num_rows($exec) > 0) {
			$html .= '
				<script language="JavaScript">
					function doDelete(id) {
						if (confirm("Are you sure you want to delete this task?")) {
							window.location="'.$modulelink.'&task=delete&id="+id;
						}
					}
				</script>
					';
			$html .= "<b><a href='$modulelink&task=add'>Add new task</a></b><br /><br />";
			$html .= "
				<div class='tablebg'>
				";
			$html .= "
				<table class='datatable' cellpadding='3' cellspacing='1' border='0' width='100%'>
					<thead>
						<tr>
							<th>
								TID
							</th>
							<th>
								Client
							</th>
							<th>
								Project
							</th>
							<th>
								Task
							</th>
							<th>
								Description
							</th>
							<th>
								Rate
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
			while($task = mysql_fetch_array($exec)) {
				$i++;
				$html .= "
					<tbody>
						<tr>
							<td>
								$i
							</td>
							<td>
								{$task['fname']} {$task['lname']}
							</td>
							<td>
								{$task['pname']}
							</td>
							<td>
								{$task['name']}
							</td>
							<td>
								{$task['description']}
							</td>

							<td>
								{$task['rate']}
							</td>
							<td>
								<a href='$modulelink&task=edit&id={$task['id']}'>edit</a>
							</td>
							<td>
								<a href='#' onclick='doDelete({$task['id']})'>delete</a>
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
			$html .= "No task in database.<br /> <a href='$modulelink&task=add'>Click here</a> to add new task.";
		}
	} else {
		$html .= "Error in query : <b>$query</b>";
	}
?>