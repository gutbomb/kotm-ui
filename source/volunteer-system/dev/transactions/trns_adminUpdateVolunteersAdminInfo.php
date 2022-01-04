<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['volunteersAdminName'] = truncate(trim($_REQUEST['volunteersAdminName']), 100);
$_REQUEST['volunteersAdminTitle'] = truncate(trim($_REQUEST['volunteersAdminTitle']), 100);
$_REQUEST['volunteersAdminPhone'] = truncate(trim($_REQUEST['volunteersAdminPhone']), 50);
$_REQUEST['volunteersAdminEmail'] = truncate(trim($_REQUEST['volunteersAdminEmail']), 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if( !isReqSet("volunteersAdminName") || !isReqSet("volunteersAdminTitle") || !isReqSet("volunteersAdminPhone") || !isReqSet("volunteersAdminEmail") ) {
	setErrorMsg("All fields are required.");
}

if(isReqSet("volunteersAdminEmail") && !isValidEmailAddress($_REQUEST['volunteersAdminEmail'])) {
	setErrorMsg("Invalid email address.");
}

// Update volunteer coordinator information if no errors ///////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$volunteersAdminInfo['name'] = $_REQUEST['volunteersAdminName'];
	$volunteersAdminInfo['title'] = $_REQUEST['volunteersAdminTitle'];
	$volunteersAdminInfo['phone'] = $_REQUEST['volunteersAdminPhone'];
	$volunteersAdminInfo['email'] = $_REQUEST['volunteersAdminEmail'];
	
	if(setVolunteersAdminInfo($volunteersAdminInfo)) {
		setMsg("Volunteer coordinator info saved.", true);
		unset($_REQUEST['adminSetVolunteersAdminSubmit']);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

?>