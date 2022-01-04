<?php

$isSpecialEventVolunteerDate = $_REQUEST['isSpecialEventVolunteerDate'];

if($isSpecialEventVolunteerDate == '0') {
	$vDate = mysqli_fetch_assoc( getVolunteerDateById($_REQUEST['downloadScheduleVDateId']) );
	$scheduled = mysqli_fetch_assoc( getCountVolunteersForVolunteerDateById($vDate['id']) );
	$volunteers = getVolunteersByVolunteerDateId($vDate['id']);
}
else {
	$vDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['downloadScheduleVDateId']) );
	$scheduled = mysqli_fetch_assoc( getCountVolunteersForSpecialEventDateById($vDate['id']) );
	$volunteers = getSpecialEventVolunteersByVolunteerDateId($vDate['id']);
}

$location = $vDate['location'];
$currentVDate = ($vDate['date'] >= getMySqlDate(time()));

$eventType = "Special Event";
if($isSpecialEventVolunteerDate == '0') {
	$eventType = $vDate['type'];
}

// default format == 'html'
if( isset($_REQUEST['format']) && $_REQUEST['format'] == "xls" ) {
	$time = "Anytime";
	if( $isSpecialEventVolunteerDate == '0' || $vDate['hasStartEndTime'] != "0" ) {
		$startTime = strftime("%-l.%M%P", strtotime($vDate['startTime']));
		$endTime = strftime("%-l.%M%P", strtotime($vDate['endTime']));
		$time = htmlentities($startTime)."-".htmlentities($endTime);
	}
	
	$eventTypeForFilename = ucWords($eventType); // set up for camel case
	$eventTypeForFilename = preg_replace('/[^a-zA-Z0-9]+/', '', $eventTypeForFilename); // strip all but filename safe characters
	$eventTypeForFilename = substr($eventTypeForFilename, 0, 20); // limit to first 20 characters
	
	$locationForFilename = ucWords($location); // set up for camel case
	$locationForFilename = preg_replace('/[^a-zA-Z0-9]+/', '', $locationForFilename); // strip all but filename safe characters
	$locationForFilename = substr($locationForFilename, 0, 20); // limit to first 20 characters
	
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"volunteerSchedule_".$vDate['date']."_".$time."_".$eventTypeForFilename."_".$locationForFilename.".xls\"");

	//these headers fix a problem with IE downloading documents via https
	//for details see http://support.microsoft.com/kb/316431
	//and http://drupal.org/node/163298
	header("Cache-Control: private");
	header("Pragma: private");
}

// NOTE: borders only work with inline styling
?>
<style type="text/css">
	table {
		border-width: 1px 1px 1px 1px;
		border-spacing: 0px;
		border-style: solid solid solid solid;
		border-color: black black black black;
		border-collapse: collapse;
		background-color: white;
		table-layout: fixed;
		word-wrap: break-word;
		font-size: 13px;
		table-layout: fixed;
		width: 811px;
	}
	th {
		border-width: 1px 1px 1px 1px;
		padding: 4px 10px 4px 10px;
		border-style: solid solid solid solid;
		border-color: black black black black;
		background-color: white;
	}
	td {
		border-width: 1px 1px 1px 1px;
		padding: 4px 10px 4px 10px;
		border-style: solid solid solid solid;
		border-color: black black black black;
		background-color: white;
	}
</style>
<table>
	<col style="width:105px;"/>
	<col style="width:115px;"/>
	<col style="width:180px;"/>
	<col style="width:70px;"/>
	<col style="width:95px;"/>
	<col style="width:95px;"/>
	<col style="width:85px;"/>
	<col style="width:85px;"/>
	<tr style="border-width: 1px 1px 0px 1px;">
		<th colspan="8" style="border-width: 1px 1px 0px 1px;"><?php echo $eventType; ?> - Scheduled Volunteers</th>
	</tr>
	<tr style="border-width: 0px 1px 0px 1px;">
		<th colspan="8" style="border-width: 0px 1px 0px 1px; font-weight:normal;"><?php echo $location; ?></th>
	</tr>
	<tr style="border-width: 0px 1px 0px 1px;">
		<th colspan="8" style="border-width: 0px 1px 0px 1px; font-weight:normal;"><?php echo formatDateAsNumeric_monthDayYear($vDate['date']); ?></th>
	</tr>
	<tr style="border-width: 0px 1px 1px 1px;">
		<th colspan="8" style="border-width: 0px 1px 1px 1px; font-weight:normal;"><?php
			if ($isSpecialEventVolunteerDate == '1') {
				if ($vDate['hasStartEndTime'] == '0') {
					echo "Anytime";
				}
				else {
					echo strftime("%-l:%M %P", strtotime($vDate['startTime']))." - ".strftime("%-l:%M %P", strtotime($vDate['endTime']));
				}
			}
			else {
				echo strftime("%-l:%M %P", strtotime($vDate['startTime']))." - ".strftime("%-l:%M %P", strtotime($vDate['endTime']));
			}
			?></th>
	</tr>
	<?php
	if($scheduled['count(*)'] < 1) {
		?>
		<tr>
			<td colspan="8">None</td>
		</tr>
		<?php
	}
	else {
		?>
		<tr>
			<th>Username</th>
			<th>Name</th>
			<th>Email</th>
			<th>Phone</th>
			<th>Sign-up Date</th>
			<th>Date Confirmed</th>
			<th>City</th>
			<th>Date of Birth</th>
		</tr>
		<?php
		while($volunteer = mysqli_fetch_assoc($volunteers)) {
			$formattedDateSignupDate = formatDateAsText_monthDayYear($volunteer['signupDate'], true);
			$acct = mysqli_fetch_assoc( getAccountByUname($volunteer['uname']) );
			echo "<tr valign='top'>";
			echo "	<td>".addslashes($acct['uname'])."</td>";
			echo "	<td>".addslashes($acct['lastname']).", ".addslashes($acct['firstname'])."</td>";
			echo "	<td>".addslashes($acct['email'])."</td>";
			echo "	<td>".addslashes($acct['phone1']);
			if($acct['phone2'] != "") {
				echo "<br/>".addslashes($acct['phone2']);
			}
			echo "</td>";
			echo "	<td><center>".addslashes($formattedDateSignupDate)."</center></td>";
			echo "	<td><center>";
			if($volunteer['confirmed'] == "") {
				echo "None";
			}
			else {
				echo htmlentities( formatDateAsText_monthDayYear($volunteer['confirmed'], true) );
			}
			echo "</center></td>";
			echo "	<td><center>";
			echo $acct['city'];
			echo "</center></td>";
			echo "<td><center>".formatDateAsNumeric_monthDayYear($acct['birthdate'])."</center></td>";
			echo "</tr>";
		}
	}
	?>
</table>