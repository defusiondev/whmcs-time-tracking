<?php
	if(isset($_REQUEST['msgid']) && is_numeric($_REQUEST['msgid'])) {
		$msg = msgid2value($_REQUEST['msgid']);
		$html .= "<div style='color:orange; font-weight:bold;'>$msg</div>";
	}
?><div class="calendar">
	<div class="calendar_middle" style="float:left">
		<div class="calendar_data">
			<link href="../modules/admin/time_tracking/css/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css" />
			<script type="text/javascript" src="../modules/admin/time_tracking/js/jquery.js"></script>
			<script type="text/javascript" src="../modules/admin/time_tracking/js/jquery.ui.js"></script>
			<script type="text/javascript" src="../modules/admin/time_tracking/js/jquery.verboseload.1.0.js"></script>
			<script type="text/javascript">
				function loadTodaysTasks() {
					var dateStr = $('#actualDate').attr("value");
					dateArray = dateStr.split('/');
					url = '../<?php echo substr($modulelink, 0, -43); ?>modules/admin/time_tracking/time_tracking.php?' + 'ajax=true&action=todaysTasks&m=' + dateArray[0] + '&d=' + dateArray[1] + '&y=' + dateArray[2];
					$('#todaysTasks').verboseLoad('<img src="../modules/admin/time_tracking/images/loading.gif" alt="Loading ..." />', url);
				}
				function addTaskHourRateForm() {
					var dateStr = $('#actualDate').attr("value");
					dateArray = dateStr.split('/');
					url = '../<?php echo substr($modulelink, 0, -43); ?>modules/admin/time_tracking/time_tracking.php?' + 'ajax=true&action=addTaskHourRate&m=' + dateArray[0] + '&d=' + dateArray[1] + '&y=' + dateArray[2];
					$('#addTaskHourRate').verboseLoad('<img src="../modules/admin/time_tracking/images/loading.gif" alt="Loading ..." />',url);
				}
				function markUnbilled(id) {
					url = '../<?php echo substr($modulelink, 0, -43); ?>modules/admin/time_tracking/time_tracking.php?' + 'ajax=true&action=markUnbilled&id=' + id;
					//alert(url);
 					$.ajax({url: url});
					loadFunctions();
				}
				function markBilled(id) {
					url = '../<?php echo substr($modulelink, 0, -43); ?>modules/admin/time_tracking/time_tracking.php?' + 'ajax=true&action=markBilled&id=' + id;
					//alert(url);
 					$.ajax({url: url});
					loadFunctions();
				}
				function loadFunctions() {
					var dateStr = $('#actualDate').attr("value");
					dateArray = dateStr.split('/');
					addTaskHourRateForm();
					loadTodaysTasks();
					var m = dateArray[0];
					var y = dateArray[2];
					url = '../<?php echo substr($modulelink, 0, -43); ?>modules/admin/time_tracking/time_tracking.php?' + 'ajax=true&action=getDates&m=' + m + '&y=' + y;
					var specialDaysData;
					$('#specialDaysData2').load(url, function() {
						specialDaysData = $('#specialDaysData2').html();
						//alert(specialDaysData);
						$('#datepicker').datepicker(
							"option",
							"beforeShowDay",
							function(thedate) {
								var theday    = thedate.getDate();
								if( specialDaysData.indexOf(theday) == -1 ) return [true,""];
								return [true, "ui-state-active"];
							}
						);
						/**
						$('#datepicker').datepicker(
							"option",
							"onChangeMonthYear",
							function() {
								loadFunctions();
							}
						);
						*/
					});
					//var specialDaysData = $('#specialDaysField').attr("value");
				}
				$(function() {
					//$("#datepicker").datepicker({ altField: '#actualDate' }).change(loadFunctions);
					$('#datepicker').datepicker({
						altField: '#actualDate'
					}).change(loadFunctions);
					loadFunctions();
				});
			</script>
			<div id="datepicker"></div>
			<input name="date" id="actualDate" style="display:none" />
			
			<!--[if !IE]>end section content footer<![endif]-->
		</div>
	</div>
	<input type="hidden" id="specialDaysField" style="display:none" value="1,2,6,9" />
	<div id="specialDaysData2" style="display:none"></div>
	<div id="addTaskHourRate" style="float:left; padding: 0px 0px 0px 50px; margin: 0px 0px 0px 20px">
		
	</div>
	<div style="clear:both"></div>
	<div class="calendar_bottom"></div>
</div>
<div id="todaysTasks" align="center">
	
</div>
