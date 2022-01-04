<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['deleteSignupSpecialEventDateId'] = truncate(trim($_REQUEST['deleteSignupSpecialEventDateId']), 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

$currDate = getMySqlDate(time());
$specialEventDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['deleteSignupSpecialEventDateId']) );
if($specialEventDate['date'] < $currDate) {
	setErrorMsg("Cannot delete sign-up for past event.");
}

// Delete the sign-up /////////////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(deleteSpecialEventVolunteer($_SESSION['user']['uname'], $_REQUEST['deleteSignupSpecialEventDateId'])) {
		// check if the administrator needs to be notified of the schedule change
		$numDaysWarning = 7;
		$oneWeekFromNow = getMySqlDate(strtotime("+$numDaysWarning days"));
		if($oneWeekFromNow >= $specialEventDate['date']) { // within one week
			require_once('emails/specialEvents/email_signupSpecialEventDeleteNotification.php');
			sendSpecialEventSignupDeleteNotificationEmail($_SESSION['user'], $specialEventDate, $numDaysWarning);
		}
		setMsg("Sign-up deleted.", true);
		unset($_REQUEST['deleteSignupSpecialEventDateId']);
		unset($_REQUEST['deleteSpecialEventSignup']);
	}
	else {
		$link = getVolunteersAdminEmailLink("color:#05aea8;");
		$link = ($link == "") ? "" : " (".$link.")";
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, let us know".$link."!");
	}
}

?>