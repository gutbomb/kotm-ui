<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['deleteVDateId'] = truncate(trim($_REQUEST['deleteVDateId']), 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

$scheduled = mysqli_fetch_assoc( getCountVolunteersForVolunteerDateById($_REQUEST['deleteVDateId']) );
if($scheduled['count(*)'] > 0) {
	setErrorMsg("Cannot delete event for which volunteers are scheduled.");
}

$currDate = getMySqlDate(time());
$vDate = mysqli_fetch_assoc( getVolunteerDateById($_REQUEST['deleteVDateId']) );
if(!isset($vDate)) {
	setErrorMsg("Event does not exist.");
}
if($vDate['date'] < $currDate) {
	setErrorMsg("Cannot delete past event.");
}

// Delete the volunteer date if no errors /////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(deleteVolunteerDate($_REQUEST['deleteVDateId'])) {
		setMsg("Event deleted.", true);
		unset($_REQUEST);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

?>