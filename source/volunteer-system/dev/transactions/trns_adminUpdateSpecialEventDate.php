<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['manageSpecialEventDateId'] = truncate(trim($_REQUEST['manageSpecialEventDateId']), 50);
$_REQUEST['location'] = truncate(trim($_REQUEST['location']), 300);
$_REQUEST['volunteersNeeded'] = truncate(trim($_REQUEST['volunteersNeeded']), 3);
$_REQUEST['startHour'] = truncate(trim($_REQUEST['startHour']), 2);
$_REQUEST['startMinutes'] = truncate(trim($_REQUEST['startMinutes']), 2);
$_REQUEST['startTimeAmPm'] = truncate(trim($_REQUEST['startTimeAmPm']), 2);
$_REQUEST['endHour'] = truncate(trim($_REQUEST['endHour']), 2);
$_REQUEST['endMinutes'] = truncate(trim($_REQUEST['endMinutes']), 2);
$_REQUEST['endTimeAmPm'] = truncate(trim($_REQUEST['endTimeAmPm']), 2);
$_REQUEST['specifiedTime'] = truncate(trim($_REQUEST['specifiedTime']), 3);
$_REQUEST['shortDescription'] = truncate(trim($_REQUEST['shortDescription']), 300);
$_REQUEST['longDescription'] = truncate(trim($_REQUEST['longDescription']), 1000);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////
$currDate = getMySqlDate(time());
$specialEventDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['manageSpecialEventDateId']) );
$startTime = formatTimeForSQL($_REQUEST['startTimeAmPm'], $_REQUEST['startHour'], $_REQUEST['startMinutes']);
$endTime = formatTimeForSQL($_REQUEST['endTimeAmPm'], $_REQUEST['endHour'], $_REQUEST['endMinutes']);

$validTimeFlag = 1;

if(!$specialEventDate) {
	setErrorMsg("Event not found in database.");
}
else if($specialEventDate['date'] < $currDate) {
	setErrorMsg("Cannot modify past event.");
}
else {

	if( 
		!isReqSet("location") || 
		!isReqSet("shortDescription") || 
		!isReqSet("longDescription") ||
		!isReqSet("volunteersNeeded") || 
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

	if(isReqSet("volunteersNeeded")) {
		if(preg_match("/^[0-9]+$/", $_REQUEST['volunteersNeeded']) !== 1) {
			setErrorMsg("Volunteers needed must be a non-negative number.");
		}
		else {
			$scheduled = mysqli_fetch_assoc( getCountVolunteersForSpecialEventDateById($_REQUEST['manageSpecialEventDateId']) );
			if($scheduled['count(*)'] > $_REQUEST['volunteersNeeded']) {
				setErrorMsg("Volunteers needed cannot be less than number currently scheduled.");
			}
		}
	}

	if( isReqSet("location") && isReqSet("specifiedTime") ) {
		
		if(specialEventDateTimeAndLocationExists($specialEventDate['date'], $_REQUEST['specifiedTime'], $startTime, $endTime, $_REQUEST['location'])) {
			$otherDate = getSpecialEventDateByDateStartTimeEndTimeLocation($specialEventDate['date'], $_REQUEST['specifiedTime'], $startTime, $endTime, $_REQUEST['location']);
			if($specialEventDate['id'] != $otherDate['id']) {
				setErrorMsg("A special event is already scheduled for that location, date, and time.");
			}
		}
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
}

// Update the volunteer date if no errors /////////////////////////////////////////////////////////////////////

if(!isErrors())
{
	$specialEventDate['location'] = $_REQUEST['location'];
	$specialEventDate['volunteersNeeded'] = $_REQUEST['volunteersNeeded'];
	$specialEventDate['shortDescription'] = $_REQUEST['shortDescription'];
	$specialEventDate['longDescription'] = $_REQUEST['longDescription'];
	if($_REQUEST['specifiedTime'] == "yes") {
		$specialEventDate['hasStartEndTime'] = '1';
		$specialEventDate['startTime'] = $startTime;
		$specialEventDate['endTime'] = $endTime;
	}
	else {
		$specialEventDate['hasStartEndTime'] = '0';
		$specialEventDate['startTime'] = "NULL";
		$specialEventDate['endTime'] = "NULL";
	}
	
	if(updateSpecialEventDate($_REQUEST['manageSpecialEventDateId'], $specialEventDate)) {
		setMsg("Event updated.", true);
		// Force the data on the page to refresh from the database
		$tmpManageVDateId = $_REQUEST['manageSpecialEventDateId'];
		unset($_REQUEST);
		$_REQUEST['manageSpecialEventDateId'] = $tmpManageVDateId;
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
		
	}
}

?>