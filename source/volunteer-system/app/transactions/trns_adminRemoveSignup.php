<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['removeSignupUname'] = truncate(trim($_REQUEST['removeSignupUname']), 100);
$_REQUEST['removeSignupVDateId'] = truncate(trim($_REQUEST['removeSignupVDateId']), 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

$currDate = getMySqlDate(time());
$vDate = mysqli_fetch_assoc( getVolunteerDateById($_REQUEST['removeSignupVDateId']) );

// Remove the sign-up /////////////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(deleteVolunteer($_REQUEST['removeSignupUname'], $_REQUEST['removeSignupVDateId'])) {
		unset($_REQUEST['removeSignupUname']);
		unset($_REQUEST['removeSignupVDateId']);
		unset($_REQUEST['removeSignup']);
		setSkipScrollTop(true);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

?>