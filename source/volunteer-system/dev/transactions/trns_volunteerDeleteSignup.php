<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['deleteSignupVDateId'] = truncate(trim($_REQUEST['deleteSignupVDateId']), 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

$currDate = getMySqlDate(time());
$vDate = mysqli_fetch_assoc( getVolunteerDateById($_REQUEST['deleteSignupVDateId']) );
if($vDate['date'] < $currDate) {
	setErrorMsg("Cannot delete sign-up for past event.");
}

// Delete the sign-up /////////////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(deleteVolunteer($_SESSION['user']['uname'], $_REQUEST['deleteSignupVDateId'])) {
		// check if the administrator needs to be notified of the schedule change
		$numDaysWarning = 7;
		$oneWeekFromNow = getMySqlDate(strtotime("+$numDaysWarning days"));
		if($oneWeekFromNow >= $vDate['date']) { // within one week
			if($vDate['type'] == "Respite") {
				require_once('emails/respite/email_signupDeleteNotification.php');
			}
			else {
				require_once('emails/childcare/email_signupDeleteNotification.php');
			}
			sendSignupDeleteNotificationEmail($_SESSION['user'], $vDate, $numDaysWarning);
		}
		setMsg("Sign-up deleted.", true);
		unset($_REQUEST['deleteSignupVDateId']);
		unset($_REQUEST['deleteSignup']);
	}
	else {
		$link = getVolunteersAdminEmailLink("color:#05aea8;");
		$link = ($link == "") ? "" : " (".$link.")";
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, let us know".$link."!");
	}
}

?>