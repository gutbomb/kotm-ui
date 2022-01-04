<?php

$currentDate = getdate();
$months = array('placeholder', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
$oldestVDateYear = getOldestVolunteerOrSpecialEventDateYear();

if(!isset($_REQUEST['month']) && !isset($_REQUEST['year'])) {
	// page just loaded for the first time, so default all event types to 'on'
	$_REQUEST['childcareShow'] = "on";
	$_REQUEST['respiteShow'] = "on";
	$_REQUEST['specialEventsShow'] = "on";
}
else {
	// this is an update to the page by the user, so don't scroll to top
	setSkipScrollTop(true);
}

if(!isset($_REQUEST['month'])) {
	$_REQUEST['month'] = $currentDate['mon'];
}
else {
	$_REQUEST['month'] = truncate(trim($_REQUEST['month']), 2);
}
if(!isset($_REQUEST['year'])) {
	$_REQUEST['year'] = $currentDate['year'];
}
else {
	$_REQUEST['year'] = truncate(trim($_REQUEST['year']), 4);
}

$volunteerSignups = array();
$volunteerSignupsDbResult = getAllVolunteerOrSpecialEventSignupsInMonth($_REQUEST['month'], $_REQUEST['year']);
if($volunteerSignupsDbResult) {
	foreach( $volunteerSignupsDbResult as $volunteerSignup ) {
		// filter out any event types not set to show
		if(!isset($_REQUEST['childcareShow']) && $volunteerSignup['type'] == "Childcare") {
			continue;
		}
		else if(!isset($_REQUEST['respiteShow']) && $volunteerSignup['type'] == "Respite") {
			continue;
		}
		else if(!isset($_REQUEST['specialEventsShow']) && $volunteerSignup['type'] == "Special Events") {
			continue;
		}
		// DB sorted the entries on retrieval (by type, location, school, showed); now organize them
		if($volunteerSignup['student'] == 0) {
			$volunteerSignups[$volunteerSignup['type']][$volunteerSignup['location']]['NON_STUDENT'][$volunteerSignup['showed']][] = $volunteerSignup;
		}
		else {
			$volunteerSignups[$volunteerSignup['type']][$volunteerSignup['location']][$volunteerSignup['school']][$volunteerSignup['showed']][] = $volunteerSignup;
		}
	}
}

printPageMessagesAndTitle("Admin Services - Participation Reports<div class='spacer'></div><a href='?page=adminServices'>(done)</a>");

?>
<form class="text-indent" method="get" action="" name="adminOrgParticipationReportsForm" id="adminOrgParticipationReportsForm">
	<input type="hidden" name="page" id="page" value="adminOrgParticipationReports"/>
	<table class="adminOrgParticipationReportsSelect invisible-table">
		<tr>
			<td>
				Month: 
				<select name="month" id="month" onchange="updateReport()">
					<?php
					for($i=1; $i<=12; $i++) {
						echo "<option value='$i'";
						if($i == $_REQUEST['month']) {
							echo " selected";
						}
						echo ">".$months[$i]."</option>\n";
					}
					?>
				</select>
				<div class="spacer" style="width:9px;"></div>
				Year: 
				<select name="year" id="year" onchange="updateReport()">
					<?php
					for($i=$oldestVDateYear; $i<=$currentDate['year']; $i++) {
						echo "<option value='$i'";
						if($i == $_REQUEST['year']) {
							echo " selected";
						}
						echo ">$i</option>\n";
					}
					?>
				</select>
				<div class="spacer" style="width:9px;"></div>
				Childcare
				<input type="checkbox" id="childcareShow" name="childcareShow" onchange="updateReport()"<?php if(isset($_REQUEST['childcareShow'])) echo " checked"; ?>>
				<div class="spacer" style="width:9px;"></div>
				Respite
				<input type="checkbox" id="respiteShow" name="respiteShow" onchange="updateReport()"<?php if(isset($_REQUEST['respiteShow'])) echo " checked"; ?>>
				<div class="spacer" style="width:9px;"></div>
				Special Events
				<input type="checkbox" id="specialEventsShow" name="specialEventsShow" onchange="updateReport()"<?php if(isset($_REQUEST['specialEventsShow'])) echo " checked"; ?>>
			</td>
		</tr>
	</table>
</form>
<br/>
<div class="text-indent">
	Download Report:
	<a href="javascript:downloadReport('xls');">Excel</a>,
	<a href="javascript:downloadReport('html');">HTML</a>
</div>
<table class="adminManageVolunteers table-indent" style="table-layout:fixed; word-wrap:break-word;">
	<col style="width:113px;"/>
	<col style="width:94px;"/>
	<col style="width:160px;"/>
	<col style="width:81px;"/>
	<col style="width:84px;"/>
	<col style="width:85px;"/>
	<tr>
		<th colspan="6">
			<?php echo $months[$_REQUEST['month']]." ".$_REQUEST['year']; ?><br/>
			Volunteer Signups by Event Type, Location, &amp; School
			<div style="font-weight:normal;">
				<?php if(isset($_REQUEST['childcareShow'])){ echo "Childcare: Included"; } else{ echo "<span style=\"color:red;\">Childcare: Excluded</span>"; } ?><br/>
				<?php if(isset($_REQUEST['respiteShow'])){ echo "Respite: Included"; } else{ echo "<span style=\"color:red;\">Respite: Excluded</span>"; } ?><br/>
				<?php if(isset($_REQUEST['specialEventsShow'])){ echo "Special Events: Included"; } else{ echo "<span style=\"color:red;\">Special Events: Excluded</span>"; } ?>
			</div>
		</th>
	</tr>
	<?php
	if(count($volunteerSignups) > 0) {
		?>
		<tr style="text-align:left;">
			<th>Event Type</th>
			<th>Location</th>
			<th>School</th>
			<th>Showed</th>
			<th>No-Shows</th>
			<th>Not Recorded</th>
		</tr>
		<?php
		$totalShowed = 0;
		$totalNoShows = 0;
		$totalNotRecorded = 0;
		foreach($volunteerSignups as $type => $locations) {
			foreach($locations as $location => $schools) {
				foreach($schools as $school => $statuses) {
					$showed = (isset($statuses[1])) ? count($statuses['1']) : 0;
					$noShows = (isset($statuses[0])) ? count($statuses['0']) : 0;
					$notRecorded = (isset($statuses[""])) ? count($statuses['']) : 0;
					echo "<tr>";
					echo "<td>$type</td>";
					echo "<td>".htmlentities($location)."</td>";
					echo "<td>".htmlentities($school)."</td>";
					echo "<td>$showed</td>";
					echo "<td>$noShows</td>";
					echo "<td>$notRecorded</td>";
					echo "</tr>";
					$totalShowed += $showed;
					$totalNoShows += $noShows;
					$totalNotRecorded += $notRecorded;
				}
			}
		}
		echo "<tr>";
		echo "<td colspan='3' style='text-align:right; font-weight:bold;'>Totals:</td>";
		echo "<td style='font-weight:bold;'>$totalShowed</td>";
		echo "<td style='font-weight:bold;'>$totalNoShows</td>";
		echo "<td style='font-weight:bold;'>$totalNotRecorded</td>";
		echo "</tr>";
	}
	else {
		if( !isset($_REQUEST['childcareShow']) && !isset($_REQUEST['respiteShow']) && !isset($_REQUEST['specialEventsShow']) ) {
			?>
			<tr>
				<td colspan="6" style="text-align:left;">No event types selected.</td>
			</tr>
			<?php
		}
		else {
			?>
			<tr>
				<td colspan="6" style="text-align:left;">None</td>
			</tr>
			<?php
		}
	}
	?>
</table>
<form method="get" action="" target="downloadReport_<?php echo $_REQUEST['year']."-".$_REQUEST['month']."-".$_REQUEST['childcareShow']."-".$_REQUEST['respiteShow']."-".$_REQUEST['specialEventsShow']; ?>" name="downloadReportForm" id="downloadReportForm">
	<input type="hidden" name="page" id="page" value="downloadOrgParticipationReport"/>
	<input type="hidden" name="month" id="month" value="<?php echo $_REQUEST['month']; ?>"/>
	<input type="hidden" name="year" id="year" value="<?php echo $_REQUEST['year']; ?>"/>
	<input type="hidden" name="childcareShow" id="childcareShow" value="<?php if(isset($_REQUEST['childcareShow'])){ echo "true"; } else{ echo "false"; } ?>"/>
	<input type="hidden" name="respiteShow" id="respiteShow" value="<?php if(isset($_REQUEST['respiteShow'])){ echo "true"; } else{ echo "false"; } ?>"/>
	<input type="hidden" name="specialEventsShow" id="specialEventsShow" value="<?php if(isset($_REQUEST['specialEventsShow'])){ echo "true"; } else{ echo "false"; } ?>"/>
</form>
<script type="text/javascript">
function updateReport() {
	document.getElementById('adminOrgParticipationReportsForm').submit();
}

function downloadReport(format) {
	if(format == 'html') {
		document.getElementById('downloadReportForm').submit();
	}
	else {
		window.location = "?page=downloadOrgParticipationReport&month=<?php echo $_REQUEST['month']; ?>&year=<?php echo $_REQUEST['year']; ?>&childcareShow=<?php if(isset($_REQUEST['childcareShow'])){ echo 'true'; } else{ echo 'false'; } ?>&respiteShow=<?php if(isset($_REQUEST['respiteShow'])){ echo 'true'; } else{ echo 'false'; } ?>&specialEventsShow=<?php if(isset($_REQUEST['specialEventsShow'])){ echo 'true'; } else{ echo 'false'; } ?>&format=xls";
	}
}
</script>