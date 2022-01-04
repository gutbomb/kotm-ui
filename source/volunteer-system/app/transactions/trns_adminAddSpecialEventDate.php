<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['location'] = truncate(trim($_REQUEST['specialEventLocation']), 300);
$_REQUEST['volunteerMonth'] = truncate(trim($_REQUEST['specialEventVolunteerMonth']), 2);
$_REQUEST['volunteerDay'] = truncate(trim($_REQUEST['specialEventVolunteerDay']), 2);
$_REQUEST['volunteerYear'] = truncate(trim($_REQUEST['specialEventVolunteerYear']), 4);
$_REQUEST['volunteersNeeded'] = truncate(trim($_REQUEST['specialEventVolunteersNeeded']), 3);
$_REQUEST['startHour'] = truncate(trim($_REQUEST['specialEventStartHour']), 2);
$_REQUEST['startMinutes'] = truncate(trim($_REQUEST['specialEventStartMinutes']), 2);
$_REQUEST['startTimeAmPm'] = truncate(trim($_REQUEST['specialEventStartTimeAmPm']), 2);
$_REQUEST['endHour'] = truncate(trim($_REQUEST['specialEventEndHour']), 2);
$_REQUEST['endMinutes'] = truncate(trim($_REQUEST['specialEventEndMinutes']), 2);
$_REQUEST['endTimeAmPm'] = truncate(trim($_REQUEST['specialEventEndTimeAmPm']), 2);
$_REQUEST['specifiedTime'] = truncate(trim($_REQUEST['specialEventSpecifiedTime']), 3);
$_REQUEST['shortDescription'] = truncate(trim($_REQUEST['specialEventShortDescription']), 300);
$_REQUEST['longDescription'] = truncate(trim($_REQUEST['specialEventLongDescription']), 1000);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////
$startTime = formatTimeForSQL($_REQUEST['startTimeAmPm'], $_REQUEST['startHour'], $_REQUEST['startMinutes']);
$endTime = formatTimeForSQL($_REQUEST['endTimeAmPm'], $_REQUEST['endHour'], $_REQUEST['endMinutes']);

$validTimeFlag = 1; // 0 if one part of the time inputs is invalid, 1 if all time inputs are valid

if( !isReqSet("location") ||
	(!isReqSet("volunteerMonth") && !isReqSet("volunteerDay") && !isReqSet("volunteerYear")) ||
	!isReqSet("volunteersNeeded") || 
	!isReqSet("shortDescription") || 
	!isReqSet("longDescription") ||
	!isReqSet("specifiedTime") || 
	($_REQUEST['specifiedTime'] != "yes" && $_REQUEST['specifiedTime'] != "no") ||
	($_REQUEST['specifiedTime'] == "yes" && ( 
		(!isReqSet("startHour") && !isReqSet("startMinutes")) || 
		(!isReqSet("endHour") && !isReqSet("endMinutes")) || 
		!isReqSet("startTimeAmPm") || 
		!isReqSet("endTimeAmPm")
	))) {
	
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
	isReqSet("location") && isReqSet("specifiedTime") &&
	isReqSet("volunteerMonth") && isReqSet("volunteerDay") && isReqSet("volunteerYear") ) {
	
	$date = $_REQUEST['volunteerYear']."-".$_REQUEST['volunteerMonth']."-".$_REQUEST['volunteerDay'];
	if(specialEventDateTimeAndLocationExists($date, $_REQUEST['specifiedTime'], $startTime, $endTime, $_REQUEST['location'])) {
		setErrorMsg("A special event is already scheduled for that location, date, and time.");
	}
}

if(isReqSet("volunteersNeeded") && preg_match("/^[0-9]+$/", $_REQUEST['volunteersNeeded']) !== 1) {
	setErrorMsg("Volunteers needed must be a non-negative number.");
}

if(isReqSet("specifiedTime") && $_REQUEST['specifiedTime'] == "yes") {

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

// Add the volunteer date if no errors ///////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$newSpecialEventDate['location'] = $_REQUEST['location'];
	$newSpecialEventDate['date'] = $_REQUEST['volunteerYear']."-".$_REQUEST['volunteerMonth']."-".$_REQUEST['volunteerDay'];
	$newSpecialEventDate['volunteersNeeded'] = $_REQUEST['volunteersNeeded'];
	$newSpecialEventDate['shortDescription'] = $_REQUEST['shortDescription'];
	$newSpecialEventDate['longDescription'] = $_REQUEST['longDescription'];
	if($_REQUEST['specifiedTime'] == "yes") {
		$newSpecialEventDate['hasStartEndTime'] = '1';
		$newSpecialEventDate['startTime'] = $startTime;
		$newSpecialEventDate['endTime'] = $endTime;
	}
	else {
		$newSpecialEventDate['hasStartEndTime'] = '0';
	}
	
	if(createSpecialEventDate($newSpecialEventDate)) {
		setMsgTitle("Success!");
		setMsg("Event added.", true);
		unset($_REQUEST);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

// this 'if' conditional should *not* be an 'else' or 'else if'
// the error message in the 'else' within the 'if' above needs to cascade to here
if(isErrors()) {
	// These variable names match those of the non-special events form on the same page.
	// If the transaction failed, then we don't want the data being propogated to that form.
	// The special events form uses different names, but those names get mapped into the ones
	// below at the top of this file, for legacy reasons. One day, good to just update
	// this file to use the special event form's naming rather than these overloaded names.
	unset($_REQUEST['location']);
	unset($_REQUEST['volunteerMonth']);
	unset($_REQUEST['volunteerDay']);
	unset($_REQUEST['volunteerYear']);
	unset($_REQUEST['volunteersNeeded']);
	unset($_REQUEST['startHour']);
	unset($_REQUEST['startMinutes']);
	unset($_REQUEST['startTimeAmPm']);
	unset($_REQUEST['endHour']);
	unset($_REQUEST['endtMinutes']);
	unset($_REQUEST['endTimeAmPm']);
	unset($_REQUEST['specifiedTime']);
	unset($_REQUEST['shortDescription']);
	unset($_REQUEST['longDescription']);
}

?>