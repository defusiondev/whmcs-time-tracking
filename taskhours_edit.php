<?php
if( $_SERVER["REQUEST_METHOD"] != "POST") {

	$id = (filter_var($_REQUEST['id'], FILTER_VALIDATE_INT)) ? $_REQUEST['id'] : 0;
	$sql = "SELECT * FROM `mod_taskhours` WHERE `id` = $id";
	$exec = mysql_query($sql);
	$taskhour = mysql_fetch_array($exec);

?>
	<form name="taskhours_edit" method="post" action="<?php echo $modulelink?>&taskhours=edit">
		<table width="700" border="0">
		  <tr valign="top">
			<td>
				<table width="100%" border="0">
				  <tr>
					<td valign="top" class="text_newproject">Task Hours</td>
				  </tr>
				  <tr>
					<td valign="top" class="txt_projectinfo">
						Task Info
					</td>
				  </tr>
				  <tr>
					<td valign="top"><table width="100%" border="0" cellspacing="0">
					  <tr>
						<td width="21%" class="textname_box">Task Hours</td>
						<td width="79%" class="text_project1">
					<input type="text" name="hours" value="<?php echo $taskhour['hours'];?>" />
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">Notes</td>
						<td class="text_project1">
							<textarea cols="20" rows="3" name="notes"><?php echo $taskhour['notes'];?></textarea>
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">&nbsp;</td>
						<td class="text_project1">
							<input type="hidden" name="id" value="<?php echo $id;?>" />
							<input type="submit" name="submit" value="Update Task Hour" />
						</td>
					  </tr>
					  <tr>
						<td class="textname_box">&nbsp;</td>
						<td class="text_project1">&nbsp;</td>
					  </tr>
					</table></td>
				  </tr>
				  <tr>
					<td valign="top">&nbsp;</td>
				  </tr>
				  <tr>
					<td valign="top">&nbsp;</td>
				  </tr>
				</table>
			</td>
		  </tr>
		</table>
	</form>
<?php
} else {

	$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

	$hours = filter_var($_POST['hours'], FILTER_SANITIZE_STRING);
	$notes = filter_var($_POST['notes'], FILTER_SANITIZE_STRING);
	$time = time();
	$sql = "
		UPDATE `mod_taskhours`
		SET `hours` = '$hours', `notes`='$notes'
		WHERE `id` = $id;
		";
	if(mysql_query($sql)) {
		header("Location:$modulelink&timesheet=list&msgid=16");
	} else {
		header("Location:$modulelink&timesheet=list&msgid=17");
	}
}
?>