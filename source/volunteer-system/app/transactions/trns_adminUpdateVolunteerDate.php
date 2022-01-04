<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['manageVDateId'] = truncate(trim($_REQUEST['manageVDateId']), 50);
$_REQUEST['volunteersNeeded'] = truncate(trim($_REQUEST['volunteersNeeded']), 3);
$_REQUEST['startHour'] = truncate(trim($_REQUEST['startHour']), 2);
$_REQUEST['endHour'] = truncate(trim($_REQUEST['endHour']), 2);
$_REQUEST['startMinutes'] = truncate(trim($_REQUEST['startMinutes']), 2);
$_REQUEST['endMinutes'] = truncate(trim($_REQUEST['endMinutes']), 2);
$_REQUEST['startTimeAmPm'] = truncate(trim($_REQUEST['startTimeAmPm']), 2);
$_REQUEST['endTimeAmPm'] = truncate(trim($_REQUEST['endTimeAmPm']), 2);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////
$currDate = getMySqlDate(time());
$vDate = mysqli_fetch_assoc( getVolunteerDateById($_REQUEST['manageVDateId']) );
$startTime = formatTimeForSQL($_REQUEST['startTimeAmPm'], $_REQUEST['startHour'], $_REQUEST['startMinutes']);
$endTime = formatTimeForSQL($_REQUEST['endTimeAmPm'], $_REQUEST['endHour'], $_REQUEST['endMinutes']);

$validTimeFlag = 1;

if(!$vDate) {
	setErrorMsg("Event not found in database.");
}
else if($vDate['date'] < $currDate) {
	setErrorMsg("Cannot modify past event.");
}
else {

	if(	(!isReqSet("startHour") && !isReqSet("startMinutes")) || 
		(!isReqSet("endHour") && !isReqSet("endMinutes")) || 
		!isReqSet("startTimeAmPm") || 
		!isReqSet("endTimeAmPm") || 
		!isReqSet("volunteersNeeded") ) {
	
		setErrorMsg("All fields are required.");
	}

	if(isReqSet("volunteersNeeded")) {
		if(preg_match("/^[0-9]+$/", $_REQUEST['volunteersNeeded']) !== 1) {
			setErrorMsg("Volunteers needed must be a non-negative number.");
		}
		else {
			$scheduled = mysqli_fetch_assoc( getCountVolunteersForVolunteerDateById($_REQUEST['manageVDateId']) );
			if($scheduled['count(*)'] > $_REQUEST['volunteersNeeded']) {
				setErrorMsg("Volunteers needed cannot be less than number currently scheduled.");
			}
		}
	}

	if(volunteerDateTimeLocationAndTypeExists($vDate['date'], $startTime, $endTime, $vDate['location'], $vDate['type'])) {
		$otherDate = getVolunteerDateByDateStartTimeEndTimeLocationType($vDate['date'], $startTime, $endTime, $vDate['location'], $vDate['type']);
		if($vDate['id'] != $otherDate['id']) {
			setErrorMsg("A ".strtolower($vDate['type'])." event is already scheduled for that location, date, and time.");
		}
	}
	
	if(isReqSet("startHour") || isReqSet("startMinutes")) {
		if(!isValidHour($_REQUEST['startHour']) || !isValidMinute($_REQUEST['startMinutes'])) {
			setErrorMsg("Invalid start time.");
			$validTimeFlag = 0;
		}
	}
	else {
		$validTimeFlag = 0;
	}
	
	if(isReqSet("endHour") || isReqSet("endMinutes")) {
		if(!isValidHour($_REQUEST['endHour']) || !isValidMinute($_REQUEST['endMinutes'])) {
			setErrorMsg("Invalid end time.");
			$validTimeFlag = 0;
		}
	}
	else {
		$validTimeFlag = 0;
	}
	
	if($validTimeFlag == 1 && isReqSet("startTimeAmPm") && isReqSet("endTimeAmPm") && endTimeIsBeforeStartTime($startTime, $endTime)) {
		setErrorMsg("End time cannot be before start time.");
	}
}

// Update the volunteer date if no errors /////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$vDate['volunteersNeeded'] = $_REQUEST['volunteersNeeded'];
	$vDate['startTime'] = $startTime;
	$vDate['endTime'] = $endTime;
	
	if(updateVolunteerDate($_REQUEST['manageVDateId'], $vDate)) {
		setMsg("Event updated.", true);
		// Force the data on the page to refresh from the database
		$tmpManageVDateId = $_REQUEST['manageVDateId'];
		unset($_REQUEST);
		$_REQUEST['manageVDateId'] = $tmpManageVDateId;
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
		
	}
}

?>