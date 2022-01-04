<?php

$currDate = getMySqlDate(time());
if(isset($_REQUEST['adminViewArchivedVolunteerDates']) && $_REQUEST['adminViewArchivedVolunteerDates'] == 'true') {
	if(isset($_REQUEST['adminViewArchivedVolunteerDates_eventType'])) {
		$vDatesDbResult = getAllVolunteerDatesByTypeBefore($_REQUEST['adminViewArchivedVolunteerDates_eventType'], $currDate);
	}
	$vDates = array();
	if(isset($vDatesDbResult)) {
		while( $vDate = mysqli_fetch_assoc($vDatesDbResult) ) {
			$vDates[] = $vDate;
		}
	}
}
else if(isset($_REQUEST['adminViewArchivedSpecialEventDates']) && $_REQUEST['adminViewArchivedSpecialEventDates'] == 'true') {
	$specialEventDatesDbResult = getAllSpecialEventDatesBefore($currDate);
	$specialEventDates = array();
	if(isset($specialEventDatesDbResult)) {
		while( $specialEventDate = mysqli_fetch_assoc($specialEventDatesDbResult) ) {
			$specialEventDates[] = $specialEventDate;
		}
	}
}
else {
	$vDatesDbResult_childcare = getAllVolunteerDatesByTypeSince("Childcare", $currDate);
	$vDatesDbResult_respite = getAllVolunteerDatesByTypeSince("Respite", $currDate);
	$specialEventDatesDbResult = getAllSpecialEventDatesSince($currDate);
	$vDates_childcare = array();
	if(isset($vDatesDbResult_childcare)) {
		while( $vDate = mysqli_fetch_assoc($vDatesDbResult_childcare) ) {
			$vDates_childcare[] = $vDate;
		}
	}
	$vDates_respite = array();
	if(isset($vDatesDbResult_respite)) {
		while( $vDate = mysqli_fetch_assoc($vDatesDbResult_respite) ) {
			$vDates_respite[] = $vDate;
		}
	}
	if(isset($specialEventDatesDbResult)) {
		while( $specialEventDate = mysqli_fetch_assoc($specialEventDatesDbResult) ) {
			$specialEventDates[] = $specialEventDate;
		}
	}
}

printPageMessagesAndTitle("Admin Services - Volunteer Events");

if(isset($_REQUEST['adminViewArchivedSpecialEventDates']) && $_REQUEST['adminViewArchivedSpecialEventDates'] == 'true') {
	?>
	<div class="text-indent">
		Back to <a href="?page=adminManageVolunteerDates">Current Events</a>
		<br/>
	</div>
	<table class="adminVolunteerDateWrapper table-indent" style="margin-top:18px; margin-bottom:10px; width:618px;">
		<tr>
			<td><h2 style="margin:0px; display:inline-block;"><?php echo htmlentities("Special Events"); ?> History</h2><div class="spacer"></div><i>(events prior to today, <?php echo formatDateAsText_monthDayYear($currDate); ?>)</i></td>
			<td style="text-align:right; padding-top:1px;"><b>Count: <?php echo count($specialEventDates); ?></b></td>
		</tr>
	</table>
	<table class="adminManageVolunteerDates table-indent" style="table-layout:fixed; word-wrap:break-word;">
		<col style="width:149px;"/>
		<col style="width:123px;"/>
		<col style="width:69px;"/>
		<col style="width:90px;"/>
		<col style="width:82px;"/>
		<col style="width:104px;"/>
		<tr>
			<th>Date/Time<br/><span style="font-weight:normal;">(Mountain Time)</span></th>
			<th>Location</th>
			<th>Needed</th>
			<th>Scheduled</th>
			<th>Confirmed</th>
			<th>Showed<br/><span style="font-weight:normal;">(Yes/No/NR)</span></th>
		</tr>
		<?php
		if(count($specialEventDates) == 0) {
			?>
			<tr><td colspan="6" style="text-align:left;">None</td></tr>
			<?php
		}
		else {
			for($i=count($specialEventDates)-1; $i>=0; $i--) {
				$specialEventDate = $specialEventDates[$i];
				$formattedDate = formatDateAsText_monthDayYear($specialEventDate['date']);
				
				if(($specialEventDate['hasStartEndTime'] == "0")) {
					$combinedTime = "Anytime";
				}
				else {
					$startTime = strftime("%-l:%M %P", strtotime($specialEventDate['startTime']));
					$endTime = strftime("%-l:%M %P", strtotime($specialEventDate['endTime']));
					$combinedTime = htmlentities($startTime)." - ".htmlentities($endTime);
				}
	
				$scheduled = mysqli_fetch_assoc( getCountVolunteersForSpecialEventDateById($specialEventDate['id']) );
				$confirmed = mysqli_fetch_assoc( getCountVolunteersConfirmedForSpecialEventDateById($specialEventDate['id']) );
				$showed = getCountVolunteersShowedForSpecialEventDateById($specialEventDate['id']);
				$showed['NR'] = (int)$scheduled['count(*)'] - ((int)$showed['yes'] + (int)$showed['no']);
				echo "<tr>";
				echo "	<td><a href=\"javascript:manageSpecialEventDate('".addslashes($specialEventDate['id'])."');\">".htmlentities($formattedDate)."</a><br/>".$combinedTime."</td>";
				echo "  <td>".htmlentities($specialEventDate['location'])."</td>";
				echo "	<td>".htmlentities($specialEventDate['volunteersNeeded'])."</td>";
				echo "	<td>".$scheduled['count(*)']."</td>";
				echo "	<td>".$confirmed['count(*)']."</td>";
				echo "	<td>".$showed['yes']." / ".$showed['no']." / ".$showed['NR']."</td>";
				echo "</tr>";
			}
		}
		?>
	</table>
	<?php
}
else if(isset($_REQUEST['adminViewArchivedVolunteerDates']) && $_REQUEST['adminViewArchivedVolunteerDates'] == 'true') {
	if(isset($_REQUEST['adminViewArchivedVolunteerDates_eventType'])) {
		$eventType = $_REQUEST['adminViewArchivedVolunteerDates_eventType'];
	}
	else {
		$eventType = "Error";
	}
	?>
	<div class="text-indent">
		Back to <a href="?page=adminManageVolunteerDates">Current Events</a>
		<br/>
	</div>
	<table class="adminVolunteerDateWrapper table-indent" style="margin-top:18px; margin-bottom:10px; width:618px;">
		<tr>
			<td><h2 style="margin:0px; display:inline-block;"><?php echo htmlentities($eventType); ?> History</h2><div class="spacer"></div><i>(events prior to today, <?php echo formatDateAsText_monthDayYear($currDate); ?>)</i></td>
			<td style="text-align:right; padding-top:1px;"><b>Count: <?php echo count($vDates); ?></b></td>
		</tr>
	</table>
	<table class="adminManageVolunteerDates table-indent" style="table-layout:fixed; word-wrap:break-word;">
		<col style="width:149px;"/>
		<col style="width:114px;"/>
		<col style="width:72px;"/>
		<col style="width:93px;"/>
		<col style="width:85px;"/>
		<col style="width:104px;"/>
		<tr>
			<th>Date/Time<br/><span style="font-weight:normal;">(Mountain Time)</span></th>
			<th>Location</th>
			<th>Needed</th>
			<th>Scheduled</th>
			<th>Confirmed</th>
			<th>Showed<br/><span style="font-weight:normal;">(Yes/No/NR)</span></th>
		</tr>
		<?php
		if(count($vDates) == 0) {
			?>
			<tr><td colspan="6" style="text-align:left;">None</td></tr>
			<?php
		}
		else {
			for($i=count($vDates)-1; $i>=0; $i--) {
				$vDate = $vDates[$i];
				$formattedDate = formatDateAsText_monthDayYear($vDate['date']);
				$startTime = strftime("%-l:%M %P", strtotime($vDate['startTime']));
				$endTime = strftime("%-l:%M %P", strtotime($vDate['endTime']));
				$scheduled = mysqli_fetch_assoc( getCountVolunteersForVolunteerDateById($vDate['id']) );
				$confirmed = mysqli_fetch_assoc( getCountVolunteersConfirmedForVolunteerDateById($vDate['id']) );
				$showed = getCountVolunteersShowedForVolunteerDateById($vDate['id']);
				$showed['NR'] = (int)$scheduled['count(*)'] - ((int)$showed['yes'] + (int)$showed['no']);
				echo "<tr>";
				echo "	<td><a href=\"javascript:manageVolunteerDate('".addslashes($vDate['id'])."');\">".htmlentities($formattedDate)."</a><br/>".htmlentities($startTime)." - ".htmlentities($endTime)."</td>";
				echo "  <td>".htmlentities($vDate['location'])."</td>";
				echo "	<td>".htmlentities($vDate['volunteersNeeded'])."</td>";
				echo "	<td>".$scheduled['count(*)']."</td>";
				echo "	<td>".$confirmed['count(*)']."</td>";
				echo "	<td>".$showed['yes']." / ".$showed['no']." / ".$showed['NR']."</td>";
				echo "</tr>";
			}
		}
		?>
	</table>
	<?php
}
else {
	?>
	<table class="adminVolunteerDateWrapper text-indent">
		<tr>
			<td style="width: 270px;"><?php require_once('pages/adminManageVolunteerDates/form.php'); ?></td>
			<td><?php require_once('pages/adminManageVolunteerDates/specialEventForm.php'); ?></td>
		</tr>
	</table>
	<form method="get" action="" name="adminDeleteVolunteerDateForm" id="adminDeleteVolunteerDateForm">
		<input type="hidden" name="page" id="page" value="adminManageVolunteerDates">
		<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
		<input type="hidden" name="deleteVDate" id="deleteVDate" value="false"/>
		<input type="hidden" name="deleteVDateId" id="deleteVDateId" value=""/>
	</form>
	<form method="get" action="" name="adminDeleteSpecialEventDateForm" id="adminDeleteSpecialEventDateForm">
		<input type="hidden" name="page" id="page" value="adminManageVolunteerDates">
		<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
		<input type="hidden" name="deleteSpecialEventDate" id="deleteSpecialEventDate" value="false"/>
		<input type="hidden" name="deleteSpecialEventDateId" id="deleteSpecialEventDateId" value=""/>
	</form>
	<form method="get" action="" name="adminViewArchivedVolunteerDatesForm" id="adminViewArchivedVolunteerDatesForm">
		<input type="hidden" name="page" id="page" value="adminManageVolunteerDates">
		<input type="hidden" name="adminViewArchivedVolunteerDates" id="adminViewArchivedVolunteerDates" value="true"/>
		<input type="hidden" name="adminViewArchivedVolunteerDates_eventType" id="adminViewArchivedVolunteerDates_eventType" value=""/>
	</form>
	<form method="get" action="" name="adminViewArchivedSpecialEventDatesForm" id="adminViewArchivedSpecialEventDatesForm">
		<input type="hidden" name="page" id="page" value="adminManageVolunteerDates">
		<input type="hidden" name="adminViewArchivedSpecialEventDates" id="adminViewArchivedSpecialEventDates" value="true"/>
	</form>
	<?php
	printVolunteerDateTable("Childcare", $vDates_childcare);
	echo "<br/>";
	printVolunteerDateTable("Respite", $vDates_respite);
	echo "<br/>";
	printVolunteerDateTable("Special Events", $specialEventDates);
}

function printVolunteerDateTable($type, $dates)
{
	?>
	<table class="adminVolunteerDateWrapper text-indent" style="margin-top:10px; margin-bottom:10px; width:618px;">
		<tr>
			<td><h2 style="margin:0px;"><?php echo htmlentities($type); ?></h2></td>
			<td style="text-align:right;">
				<?php
				if($type == "Special Events") {
					?>
					<a href="javascript:viewArchivedSpecialEventDates();">View History</a>
					<?php
				}
				else {
					?>
					<a href="javascript:viewArchivedVolunteerDates('<?php echo addslashes($type); ?>');">View History</a>
					<?php
				}
				?>
			</td>
		</tr>
	</table>
	<table class="adminManageVolunteerDates table-indent" style="table-layout:fixed; word-wrap:break-word;">
		<col style="width:149px;"/>
		<col style="width:128px;"/>
		<col style="width:76px;"/>
		<col style="width:97px;"/>
		<col style="width:100px;"/>
		<col style="width:67px;"/>
		<tr>
			<th>Date/Time<br/><span style="font-weight:normal;">(Mountain Time)</span></th>
			<th>Location</th>
			<th>Needed</th>
			<th>Scheduled</th>
			<th>Confirmed</th>
			<th>Delete</th>
		</tr>
		<?php
		if(count($dates) == 0) {
			?>
			<tr><td colspan="6" style="text-align:left;">None</td></tr>
			<?php
		}
		else {
			for($i=0; $i<count($dates); $i++) {
				$date = $dates[$i];
				printVolunteerDateRow($type, $date);
			}
		}
		?>
	</table>
	<?php
}

function printVolunteerDateRow($type, $date)
{
	$isSpecial = isset($date['hasStartEndTime']);
	$location = $date['location'];
	$formattedDate = formatDateAsText_monthDayYear($date['date']);
	if( $isSpecial && $date['hasStartEndTime'] == "0" ) {
		$combinedTime = "Anytime";
		$combinedTimeAlt = "Anytime";
	}
	else {
		$startTime = strftime("%-l:%M %P", strtotime($date['startTime']));
		$endTime = strftime("%-l:%M %P", strtotime($date['endTime']));
		$combinedTime = htmlentities($startTime)." - ".htmlentities($endTime);
		$combinedTimeAlt = $startTime." - ".$endTime;
	}
	if($isSpecial) {
		$scheduled = mysqli_fetch_assoc( getCountVolunteersForSpecialEventDateById($date['id']) );
		$confirmed = mysqli_fetch_assoc( getCountVolunteersConfirmedForSpecialEventDateById($date['id']) );
	}
	else {
		$scheduled = mysqli_fetch_assoc( getCountVolunteersForVolunteerDateById($date['id']) );
		$confirmed = mysqli_fetch_assoc( getCountVolunteersConfirmedForVolunteerDateById($date['id']) );
	}
	echo "<tr>";
	echo "	<td>";
	if($isSpecial) {
		echo "<a href=\"javascript:manageSpecialEventDate('".addslashes($date['id'])."');\">";
	}
	else {
		echo "<a href=\"javascript:manageVolunteerDate('".addslashes($date['id'])."');\">";
	}
	echo htmlentities($formattedDate)."</a><br/>".$combinedTime."</td>";
	echo "  <td>".$location."</td>";
	echo "	<td>".htmlentities($date['volunteersNeeded'])."</td>";
	echo "	<td>".$scheduled['count(*)']."</td>";
	echo "	<td>".$confirmed['count(*)']."</td>";
	echo "	<td>";
	if($isSpecial) {
		echo "<a href=\"javascript:deleteSpecialEventDate('".addslashes($date['id'])."', '".addslashes($location)."', '".addslashes($formattedDate)."', '".addslashes($combinedTimeAlt)."');\" class='deleteImgLink'>";
	}
	else {
		echo "<a href=\"javascript:deleteVolunteerDate('".addslashes($date['id'])."', '".addslashes($type)."', '".addslashes($date['location'])."', '".addslashes($formattedDate)."', '".addslashes($combinedTimeAlt)."');\" class='deleteImgLink'>";
	}
	echo "<img src='images/pageElements/delete.svg'/></a></td>";
	echo "</tr>";
}

?>
<form method="get" action="" name="adminManageVDatesManageVDateForm" id="adminManageVDatesManageVDateForm">
	<input type="hidden" name="page" id="page" value="adminManageVolunteerDate">
	<input type="hidden" name="manageVDateId" id="manageVDateId" value=""/>
</form>
<form method="get" action="" name="adminManageSpecialEventDatesManageVDateForm" id="adminManageSpecialEventDatesManageVDateForm">
	<input type="hidden" name="page" id="page" value="adminManageSpecialEventDate">
	<input type="hidden" name="manageSpecialEventDateId" id="manageSpecialEventDateId" value=""/>
</form>
<script type="text/javascript">
var volLocOptions = document.getElementById('volunteerLocation');
if(volLocOptions != null) {
	volLocOptions = volLocOptions.getElementsByTagName('*');
	for(var i=0; i<volLocOptions.length; i++) {
		var volLocOption = volLocOptions[i];
		if( volLocOption.value == "<?php echo addslashes($_REQUEST['volunteerLocation']); ?>" ) {
			volLocOption.selected = true;
			break;
		}
	}
}

var eventTypeOptions = document.getElementById('eventType');
if(eventTypeOptions != null) {
	eventTypeOptions = eventTypeOptions.getElementsByTagName('*');
	for(var i=0; i<eventTypeOptions.length; i++) {
		var eventTypeOption = eventTypeOptions[i];
		if( eventTypeOption.value == "<?php echo addslashes($_REQUEST['eventType']); ?>" ) {
			eventTypeOption.selected = true;
			break;
		}
	}
}

function setFieldLength(field) {
	document.getElementById(field.id+'Length').value = field.value.length;
}

function changeFocus(currElem, nextElemId) {
	var nextElem = document.getElementById(nextElemId);
	if( currElem.value.length == currElem.maxLength && 
		document.getElementById(currElem.id+'Length').value < currElem.maxLength &&
		nextElem.value.length == 0 ) {
		
		nextElem.focus();
	}
}

function viewArchivedVolunteerDates(type) {
	document.getElementById('adminViewArchivedVolunteerDates_eventType').value = type;
	document.getElementById('adminViewArchivedVolunteerDatesForm').submit();
}

function viewArchivedSpecialEventDates() {
	document.getElementById('adminViewArchivedSpecialEventDatesForm').submit();
}

function deleteVolunteerDate(deleteVDateId, deleteVDateType, deleteVDateLocation, deleteVDateDate, deleteVDateTime) {
	parent.popupConfirm("<p>Delete event?</p><p>"+deleteVDateType+" - "+deleteVDateLocation+"<br/>"+deleteVDateDate+"<br/>"+deleteVDateTime+"</p>").then(function(result){
		if( result === "OK" ) {
			document.getElementById('deleteVDate').value = "true";
			document.getElementById('deleteVDateId').value = deleteVDateId;
			document.getElementById('adminDeleteVolunteerDateForm').submit();
		}
	});
}

function deleteSpecialEventDate(deleteSpecialEventDateId, deleteSpecialEventDateLocation, deleteSpecialEventDateDate, deleteSpecialEventDateTime) {
	parent.popupConfirm("<p>Delete event?</p><p>Special Event - "+deleteSpecialEventDateLocation+"<br/>"+deleteSpecialEventDateDate+"<br/>"+deleteSpecialEventDateTime).then(function(result){
		if( result === "OK" ) {
			document.getElementById('deleteSpecialEventDate').value = "true";
			document.getElementById('deleteSpecialEventDateId').value = deleteSpecialEventDateId;
			document.getElementById('adminDeleteSpecialEventDateForm').submit();
		}
	});
}

function manageVolunteerDate(manageVDateId) {
	document.getElementById('manageVDateId').value = manageVDateId;
	document.getElementById('adminManageVDatesManageVDateForm').submit();
}

function manageSpecialEventDate(manageSpecialEventDateId) {
	document.getElementById('manageSpecialEventDateId').value = manageSpecialEventDateId;
	document.getElementById('adminManageSpecialEventDatesManageVDateForm').submit();
}
</script>