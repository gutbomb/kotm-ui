<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['deleteSpecialEventDateId'] = truncate(trim($_REQUEST['deleteSpecialEventDateId']), 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

$scheduled = mysqli_fetch_assoc( getCountVolunteersForSpecialEventDateById($_REQUEST['deleteSpecialEventDateId']) );
if($scheduled['count(*)'] > 0) {
	setErrorMsg("Cannot delete event for which volunteers are scheduled.");
}

$currDate = getMySqlDate(time());
$specialEventDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['deleteSpecialEventDateId']) );
if(!isset($specialEventDate)) {
	setErrorMsg("Event does not exist.");
}
else if($specialEventDate['date'] < $currDate) {
	setErrorMsg("Cannot delete past event.");
}

// Delete the volunteer date if no errors /////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(deleteSpecialEventDate($_REQUEST['deleteSpecialEventDateId'])) {
		setMsg("Event deleted.", true);
		unset($_REQUEST);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

?>