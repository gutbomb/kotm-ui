<?php

$currentDate = getdate();
$months = array('placeholder', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

if(!isset($_REQUEST['month']) && !isset($_REQUEST['year'])) {
	// This shouldn't happen, but just in case, if month and year are not set, then
	// assume nothing is set and default everything to on.
	$_REQUEST['childcareShow'] = "true";
	$_REQUEST['respiteShow'] = "true";
	$_REQUEST['specialEventsShow'] = "true";
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
		if($_REQUEST['childcareShow'] == "false" && $volunteerSignup['type'] == "Childcare") {
			continue;
		}
		else if($_REQUEST['respiteShow'] == "false" && $volunteerSignup['type'] == "Respite") {
			continue;
		}
		else if($_REQUEST['specialEventsShow'] == "false" && $volunteerSignup['type'] == "Special Events") {
			continue;
		}
		// sort by location, school, showed
		if($volunteerSignup['student'] == 0) {
			$volunteerSignups[$volunteerSignup['type']][$volunteerSignup['location']]['NON_STUDENT'][$volunteerSignup['showed']][] = $volunteerSignup;
		}
		else {
			$volunteerSignups[$volunteerSignup['type']][$volunteerSignup['location']][$volunteerSignup['school']][$volunteerSignup['showed']][] = $volunteerSignup;
		}
	}
}

// default format == 'html'
if( isset($_REQUEST['format']) && $_REQUEST['format'] == "xls" ) {
	$typesForFilename = "";
	if($_REQUEST['childcareShow'] == "true") {
		$typesForFilename .= "_childcare";
	}
	if($_REQUEST['respiteShow'] == "true") {
		$typesForFilename .= "_respite";
	}
	if($_REQUEST['specialEventsShow'] == "true") {
		$typesForFilename .= "_specialEvents";
	}
	if($typesForFilename === "") {
		$typesForFilename = "_noEventTypesSelected";
	}

	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"participationReport_".$_REQUEST['year']."-".$_REQUEST['month']."$typesForFilename.xls\"");

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
	<col style="width:120px;"/>
	<col style="width:120px;"/>
	<col style="width:210px;"/>
	<col style="width:120px;"/>
	<col style="width:120px;"/>
	<col style="width:120px;"/>
	<tr style="border-width: 1px 1px 0px 1px;">
		<th colspan="6" style="border-width: 1px 1px 0px 1px;"><?php echo $months[$_REQUEST['month']]." ".$_REQUEST['year']; ?></th>
	</tr>
	<tr style="border-width: 0px 1px 0px 1px;">
		<th colspan="6" style="border-width: 0px 1px 0px 1px;">Volunteer Signups by Event Type, Location, &amp; School</th>
	</tr>
	<tr style="border-width: 0px 1px 0px 1px;">
		<th colspan="6" style="border-width: 0px 1px 0px 1px; font-weight:normal;<?php if($_REQUEST['childcareShow'] == "false"){ echo " color:red;"; } ?>">
			Childcare: <?php if($_REQUEST['childcareShow'] == "true"){ echo "Included"; } else{ echo "Excluded"; } ?>
		</th>
	</tr>
	<tr style="border-width: 0px 1px 0px 1px;">
		<th colspan="6" style="border-width: 0px 1px 0px 1px; font-weight:normal;<?php if($_REQUEST['respiteShow'] == "false"){ echo " color:red;"; } ?>">
			Respite: <?php if($_REQUEST['respiteShow'] == "true"){ echo "Included"; } else{ echo "Excluded"; } ?>
		</th>
	</tr>
	<tr style="border-width: 0px 1px 1px 1px;">
		<th colspan="6" style="border-width: 0px 1px 1px 1px; font-weight:normal;<?php if($_REQUEST['specialEventsShow'] == "false"){ echo " color:red;"; } ?>">
			Special Events: <?php if($_REQUEST['specialEventsShow'] == "true"){ echo "Included"; } else{ echo "Excluded"; } ?>
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
		if( $_REQUEST['childcareShow'] == "false" && $_REQUEST['respiteShow'] == "false" && $_REQUEST['specialEventsShow'] == "false" ) {
			?>
			<tr>
				<td colspan="6">No event types selected.</td>
			</tr>
			<?php
		}
		else {
			?>
			<tr>
				<td colspan="6">None</td>
			</tr>
			<?php
		}
	}
	?>
</table>