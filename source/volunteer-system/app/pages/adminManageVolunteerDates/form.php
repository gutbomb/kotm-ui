<?php

	if(!isset($_REQUEST['startHour'])) {
		$_REQUEST['startHour'] = 5;
	}
	
	if(!isset($_REQUEST['startMinutes'])) {
		$_REQUEST['startMinutes'] = 45;
	}
	
	if(!isset($_REQUEST['endHour'])) {
		$_REQUEST['endHour'] = 9;
	}
	
	if(!isset($_REQUEST['endMinutes'])) {
		$_REQUEST['endMinutes'] = 30;
	}
	
	$startAmChecked = "";
	$startPmChecked = "";
	
	if(isset($_REQUEST['startTimeAmPm'])) {
		if(strcmp($_REQUEST['startTimeAmPm'], "am") == 0) {
			$startAmChecked = "checked";
		}
		else {
			$startPmChecked = "checked";
		}
	}
	else
	{
		$startPmChecked = "checked";
	}
	
	$endAmChecked = "";
	$endPmChecked = "";
	
	if(isset($_REQUEST['endTimeAmPm'])) {
		if(strcmp($_REQUEST['endTimeAmPm'], "am") == 0) {
			$endAmChecked = "checked";
		}
		else {
			$endPmChecked = "checked";
		}
	}
	else
	{
		$endPmChecked = "checked";
	}

?>
<h2>Childcare &amp; Respite</h2>
<form method="get" action="" name="adminAddVolunteerDateForm" id="adminAddVolunteerDateForm">
	<input type="hidden" name="page" id="page" value="adminManageVolunteerDates">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<table class="adminAddVolunteerDate" style="padding-bottom:10px;">
		<tr>
			<td>
				Type:&nbsp;
				<select name="eventType" id="eventType">
					<option value="Childcare">Childcare</option>
					<option value="Respite">Respite</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Location:&nbsp;
				<select name="volunteerLocation" id="volunteerLocation">
					<option value="Lehi">Lehi</option>
					<option value="Orem">Orem</option>
					<option value="Springville">Springville</option>
					<option value="St. George">St. George</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Date:&nbsp;
				<input type="hidden" id="volunteerMonthLength">
				<input type="hidden" id="volunteerDayLength">
				<input name="volunteerMonth" id="volunteerMonth" maxlength="2" style="width:18px;" value="<?php echo htmlentities($_REQUEST['volunteerMonth']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'volunteerDay');"> /
				<input name="volunteerDay" id="volunteerDay" maxlength="2" style="width:18px;" value="<?php echo htmlentities($_REQUEST['volunteerDay']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'volunteerYear');"> /
				<input name="volunteerYear" id="volunteerYear" maxlength="4" style="width:30px;" value="<?php echo htmlentities($_REQUEST['volunteerYear']); ?>">&nbsp;
				(mm/dd/yyyy)
			</td>
		</tr>
		<tr>
			<td>
				Start time:&nbsp;
				<input name="startHour" id="startHour" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['startHour']); ?>">
				: <input name="startMinutes" id="startMinutes" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['startMinutes']); ?>">&nbsp;
				<input type ="radio" name="startTimeAmPm" value="am" <?php echo htmlentities($startAmChecked); ?>>AM
				<input type ="radio" name="startTimeAmPm" value="pm" <?php echo htmlentities($startPmChecked); ?>>PM
			</td>
		</tr>
		<tr>
			<td>
				End time:&nbsp;
				<input name="endHour" id="endHour" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['endHour']); ?>">
				: <input name="endMinutes" id="endMinutes" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['endMinutes']); ?>">&nbsp;
				<input type ="radio" name="endTimeAmPm" value="am" <?php echo htmlentities($endAmChecked); ?>>AM
				<input type ="radio" name="endTimeAmPm" value="pm" <?php echo htmlentities($endPmChecked); ?>>PM
			</td>
		</tr>
		<tr>
			<td>
				Volunteers Needed (number):&nbsp;
				<input name="volunteersNeeded" id="volunteersNeeded" maxlength="3" style="width:25px;" value="<?php echo htmlentities($_REQUEST['volunteersNeeded']); ?>">
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" name="adminAddVolunteerDateSubmit" id="adminAddVolunteerDateSubmit" value="Add Event"/>
			</td>
		</tr>
	</table>
</form>