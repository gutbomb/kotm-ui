<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['eventType'] = truncate(trim($_REQUEST['eventType']), 50);
$_REQUEST['volunteerLocation'] = truncate(trim($_REQUEST['volunteerLocation']), 50);
$_REQUEST['volunteerMonth'] = truncate(trim($_REQUEST['volunteerMonth']), 2);
$_REQUEST['volunteerDay'] = truncate(trim($_REQUEST['volunteerDay']), 2);
$_REQUEST['volunteerYear'] = truncate(trim($_REQUEST['volunteerYear']), 4);
$_REQUEST['volunteersNeeded'] = truncate(trim($_REQUEST['volunteersNeeded']), 3);
$_REQUEST['startHour'] = truncate(trim($_REQUEST['startHour']), 2);
$_REQUEST['startMinutes'] = truncate(trim($_REQUEST['startMinutes']), 2);
$_REQUEST['startTimeAmPm'] = truncate(trim($_REQUEST['startTimeAmPm']), 2);
$_REQUEST['endHour'] = truncate(trim($_REQUEST['endHour']), 2);
$_REQUEST['endtMinutes'] = truncate(trim($_REQUEST['endMinutes']), 2);
$_REQUEST['endTimeAmPm'] = truncate(trim($_REQUEST['endTimeAmPm']), 2);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////
$startTime = formatTimeForSQL($_REQUEST['startTimeAmPm'], $_REQUEST['startHour'], $_REQUEST['startMinutes']);
$endTime = formatTimeForSQL($_REQUEST['endTimeAmPm'], $_REQUEST['endHour'], $_REQUEST['endMinutes']);

$validTimeFlag = 1; // 0 if one part of the time inputs is invalid, 1 if all time inputs are valid

if( !isReqSet("eventType") ||
	!isReqSet("volunteerLocation") ||
	(!isReqSet("volunteerMonth") && !isReqSet("volunteerDay") && !isReqSet("volunteerYear")) ||
	!isReqSet("volunteersNeeded") ||
	(!isReqSet("startHour") && !isReqSet("startMinutes")) || 
	(!isReqSet("endHour") && !isReqSet("endMinutes")) || 
	!isReqSet("startTimeAmPm") ||
	!isReqSet("endTimeAmPm") ) {
	
	setErrorMsg("All fields are required.");
}

$dateIsPast = false;

if( isReqSet("volunteerMonth") || isReqSet("volunteerDay") || isReqSet("volunteerYear") ) {
	if( !isValidDate($_REQUEST['volunteerMonth'], $_REQUEST['volunteerDay'], $_REQUEST['volunteerYear']) ) {
		setErrorMsg("Invalid date.");
	}
	else if( isPastDate($_REQUEST['volunteerMonth'], $_REQUEST['volunteerDay'], $_REQUEST['volunteerYear']) ) {
		setErrorMsg("Date cannot be past.");
		$dateIsPast = true;
	}
}

if( !$dateIsPast &&
	isReqSet("eventType") &&
	isReqSet("volunteerLocation") &&
	isReqSet("volunteerMonth") && isReqSet("volunteerDay") && isReqSet("volunteerYear") ) {
	
	$date = $_REQUEST['volunteerYear']."-".$_REQUEST['volunteerMonth']."-".$_REQUEST['volunteerDay'];
	if(volunteerDateTimeLocationAndTypeExists($date, $startTime, $endTime, $_REQUEST['volunteerLocation'], $_REQUEST['eventType'])) {
		setErrorMsg("A ".strtolower($_REQUEST['eventType'])." event is already scheduled for that location, date, and time.");
	}
}

if(isReqSet("volunteersNeeded") && preg_match("/^[0-9]+$/", $_REQUEST['volunteersNeeded']) !== 1) {
	setErrorMsg("Volunteers needed must be a non-negative number.");
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

// Add the volunteer date if no errors ///////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$newVDate['type'] = $_REQUEST['eventType'];
	$newVDate['location'] = $_REQUEST['volunteerLocation'];
	$newVDate['date'] = $_REQUEST['volunteerYear']."-".$_REQUEST['volunteerMonth']."-".$_REQUEST['volunteerDay'];
	$newVDate['volunteersNeeded'] = $_REQUEST['volunteersNeeded'];
	$newVDate['startTime'] = $startTime;
	$newVDate['endTime'] = $endTime;

	if(createVolunteerDate($newVDate)) {
		setMsgTitle("Success!");
		setMsg("Event added.", true);
		unset($_REQUEST);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

?>