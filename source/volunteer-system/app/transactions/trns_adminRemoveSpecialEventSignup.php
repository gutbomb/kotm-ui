<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['removeSignupUname'] = truncate(trim($_REQUEST['removeSignupUname']), 100);
$_REQUEST['removeSignupSpecialEventDateId'] = truncate(trim($_REQUEST['removeSignupSpecialEventDateId']), 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

$currDate = getMySqlDate(time());
$specialEventDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['removeSignupSpecialEventDateId']) );

// Remove the sign-up /////////////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(deleteSpecialEventVolunteer($_REQUEST['removeSignupUname'], $_REQUEST['removeSignupSpecialEventDateId'])) {
		unset($_REQUEST['removeSignupUname']);
		unset($_REQUEST['removeSignupSpecialEventDateId']);
		unset($_REQUEST['removeSignup']);
		setSkipScrollTop(true);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

?>