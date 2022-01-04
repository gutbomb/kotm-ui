<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['websiteAdminName'] = truncate(trim($_REQUEST['websiteAdminName']), 100);
$_REQUEST['websiteAdminTitle'] = truncate(trim($_REQUEST['websiteAdminTitle']), 100);
$_REQUEST['websiteAdminPhone'] = truncate(trim($_REQUEST['websiteAdminPhone']), 50);
$_REQUEST['websiteAdminEmail'] = truncate(trim($_REQUEST['websiteAdminEmail']), 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if( !isReqSet("websiteAdminEmail") ) {
	setErrorMsg("Email field is required.");
}

if(isReqSet("websiteAdminEmail") && !isValidEmailAddress($_REQUEST['websiteAdminEmail'])) {
	setErrorMsg("Invalid email address.");
}

// Update website administrator information if no errors ///////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$websiteAdminInfo['name'] = $_REQUEST['websiteAdminName'];
	$websiteAdminInfo['title'] = $_REQUEST['websiteAdminTitle'];
	$websiteAdminInfo['phone'] = $_REQUEST['websiteAdminPhone'];
	$websiteAdminInfo['email'] = $_REQUEST['websiteAdminEmail'];
	
	if(setWebsiteAdminInfo($websiteAdminInfo)) {
		setMsg("App developer info saved.", true);
		unset($_REQUEST['adminSetWebsiteAdminSubmit']);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

?>