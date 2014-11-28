<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Add hours / hourly rate to the task
} else {
	// Display "Add Hours" form

	$m = $_REQUEST['m'];
	$d = $_REQUEST['d'];
	$y = (filter_var($_REQUEST['y'], FILTER_VALIDATE_INT)) ? $_REQUEST['y'] : 2000;

	$time1 = mktime(0, 0, 0, $m, $d, $y);
	$date = date("F d, Y", $time1);
	$filePath = substr($modulelink, 0, -43);
	//$ajax .= "<div class='tablebg' style='text-align:center'>Log Hours: <b>$date</b></div><br />";
	if( !isset($_SERVER['HTTP_REFERER']) ) {
		$ajax .= '<script type="text/javascript" src="../time_tracking/js/jquery.js"></script>';
		$filePath = '../../';
	}
	$ajax .= "
		<div id='js'>
			<script type='text/javascript'>
				function loadTaskForProject() {
					project_id = $(\"#project\").val();
					//alert(project_id);
					url = '../".$filePath ."modules/admin/time_tracking/time_tracking.php?' + 'ajax=true&action=loadTaskForProject&p=' + project_id;
					//alert(url);
					$(\"#taskdiv\").load(url);
				}
			</script>
		</div>
		";
?><script type="text/javascript">
			$.extend({URLEncode:function(c){var o='';var x=0;c=c.toString();var r=/(^[a-zA-Z0-9_.]*)/;
			  while(x<c.length){var m=r.exec(c.substr(x));
				if(m!=null && m.length>1 && m[1]!=''){o+=m[1];x+=m[1].length;
				}else{if(c[x]==' ')o+='+';else{var d=c.charCodeAt(x);var h=d.toString(16);
				o+='%'+(h.length<2?'0':'')+h.toUpperCase();}x++;}}return o;},
			URLDecode:function(s){var o=s;var binVal,t;var r=/(%[^%]{2})/;
			  while((m=r.exec(o))!=null && m.length>1 && m[1]!=''){b=parseInt(m[1].substr(1),16);
			  t=String.fromCharCode(b);o=o.replace(m[1],t);}return o;}
			});

			function add_form_data_to_db() {
				var dateStr = $('#actualDate').attr("value");
				dateArray = dateStr.split('/');
				
				var project_id = $("#project").val();
				var task_id = $("#task").val();
				var hours = $("#hours").attr("value");
				var notes = $("#notes_tasks").attr("value");
				var url =	"../" +
						"modules/admin/time_tracking/time_tracking.php?" +
						"ajax=true&action=saveHoursForProject&p="+project_id+
						"&t="+task_id+
						"&h="+hours+
						"&tm="+dateArray[0]+
						"&td="+dateArray[1]+
						"&ty="+dateArray[2]+
						"&n="+$.URLEncode(notes)
					;
				//alert(url);
				$('#taskHourUpdater').load(url);
			}
		</script><?php
		$ajax .= '
		<table border="0">
		  <tr>
			<td width="291" valign="top" class="main_table1">
				<table width="294" border="0" align="right" class="padding001">
				<tr>
				<td width="288" valign="top" class="head_logmnain">
					Log Hours: '.$date.'
				</td>
			  </tr>
			  <tr>
				<td valign="top" class="text_project">
					<h3>Project:</h3>
				</td>
			  </tr>
			  <tr>
				<td valign="top" class="text_project">';
					$sql = "SELECT * FROM `mod_projects` order by name";
					$exec = mysql_query($sql);
					$select = '';
					if(mysql_num_rows($exec) > 0) {
						$select = "<select id='project' name='project' onchange='loadTaskForProject();'>";
						$select .= "<option value='0' selected='selected'>Select a Project</option>";
						while($project = mysql_fetch_array($exec)) {
							$select .= "<option value='{$project['id']}' >";
							$select .= $project['name'];
							$select .= "</option>";
						}
						$select .= "</select>";
					} else {
						$select = "No project in database";
					}
					$ajax .= $select;
			$ajax .= '
				</td>
			  </tr>
			  <tr>
				<td valign="top"></td>
			  </tr>
			  <tr>
				<td valign="top" class="text_project">
					<h3>Select a task</h3>
				</td>
			  </tr>
			  <tr>
				<td valign="top" class="text_project">';
					$sql = "SELECT * FROM `mod_tasks` order by name";
					$exec = mysql_query($sql);
					$select = '';
					if(mysql_num_rows($exec) > 0) {
						$select = "<select name='task'>";
						while($project = mysql_fetch_array($exec)) {
							$select .= "<option value='{$project['id']}' >";
							$select .= $project['name'];
							$select .= "</option>";
						}
						$select .= "</select>";
					} else {
						$select = "---";
					}
			$ajax .= '
					<div id="taskdiv">
						<select id="task" name="task">
							<option value="0">---</option>
						</select>
					</div>
				</td>
			  </tr>
			  <tr>
				<td valign="top" class="text_project">
					<h3>Hours</h3>
				</td>
			  </tr>
			  <tr>
				<td valign="top" class="text_project">
					<input id="hours" type="text" name="hours" />
			  </tr>
					<tr>
					  <td valign="top" class="text_project">
						<h3>Notes</h3>
					</td>
					</tr>
					<tr>
					  <td valign="top" class="text_project">
						<textarea id="notes_tasks" name="notes_tasks"></textarea>
					  </td>
					</tr>
					<tr>
						<td valign="top" class="text_project" style="width:100px;">
							<input type="button" name="addTaskHour" value="Add Hours" onclick="add_form_data_to_db();" />
						</td>
					</tr>
			</table></td>
		  </tr>
		</table>';
		//$ajax .= $select;
		$ajax .= '<div id="taskHourUpdater"></div>';
}
?>