<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['editAcctUname'] = truncate(trim($_REQUEST['editAcctUname']), 100);
$_REQUEST['admin'] = truncate(trim($_REQUEST['admin']), 3);
$_REQUEST['firstname'] = truncate(trim($_REQUEST['firstname']), 50);
$_REQUEST['lastname'] = truncate(trim($_REQUEST['lastname']), 50);
$_REQUEST['birthdate'] = truncate(trim(date("Y-m-d", strtotime($_REQUEST['birthdate']))), 10);
$_REQUEST['email'] = truncate(trim($_REQUEST['email']), 50);
$_REQUEST['address1'] = truncate(trim($_REQUEST['address1']), 100);
$_REQUEST['address2'] = truncate(trim($_REQUEST['address2']), 100);
$_REQUEST['city'] = truncate(trim($_REQUEST['city']), 50);
$_REQUEST['state'] = truncate(trim($_REQUEST['state']), 2);
$_REQUEST['zip'] = truncate(trim($_REQUEST['zip']), 5);
$_REQUEST['phone1'] = truncate(trim($_REQUEST['phone1']), 12);
$_REQUEST['phone2'] = truncate(trim($_REQUEST['phone2']), 12);
$_REQUEST['student'] = truncate(trim($_REQUEST['student']), 3);
$_REQUEST['school'] = truncate(trim($_REQUEST['school']), 100);
$_REQUEST['howHeardAbout'] = truncate(trim($_REQUEST['howHeardAbout']), 500);
$_REQUEST['emergencyName'] = truncate(trim($_REQUEST['emergencyName']), 50);
$_REQUEST['emergencyRelationship'] = truncate(trim($_REQUEST['emergencyRelationship']), 50);
$_REQUEST['emergencyPhone'] = truncate(trim($_REQUEST['emergencyPhone']), 12);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if( !isReqSet("admin") ||
	!isReqSet("firstname") ||
	!isReqSet("lastname") ||
	!isReqSet("birthdate") ||
	!isReqSet("email") ||
	!isReqSet("address1") ||
	!isReqSet("city") ||
	!isReqSet("state") ||
	!isReqSet("zip") ||
	!isReqSet("phone1") ||
	!isReqSet("student") ||
    !isReqSet("emergencyName") ||
    !isReqSet("emergencyRelationship") ||
    !isReqSet("emergencyPhone") ||
	($_REQUEST['student'] == 'yes' && !isReqSet("school")) ) {

	setErrorMsg("Some required fields are blank.");
}

if(!unameExists($_REQUEST['editAcctUname'])) {
	setErrorMsg("Username not found in database.");
}

if(!isReqSet("birthdate")) {
	setErrorMsg("Invalid date of birth.");
}

if(isReqSet("email") && !isValidEmailAddress($_REQUEST['email'])) {
	setErrorMsg("Invalid email address.");
}

if(isReqSet("zip") && !isValidZipCode($_REQUEST['zip'])) {
	setErrorMsg("Invalid zip code.");
}

if(isReqSet("phone1") && !isValidPhoneNumber($_REQUEST['phone1'])) {
    setErrorMsg("Invalid mobile phone number.");
}

if(isReqSet("phone2") && !isValidPhoneNumber($_REQUEST['phone2'])) {
    setErrorMsg("Invalid alternate phone number.");
}

if(isReqSet("student") && $_REQUEST['student'] != "yes" && $_REQUEST['student'] != "no") {
	setErrorMsg("Invalid response to student status question.");
}

if(!isReqSet("emergencyRelationship")) {
    setErrorMsg("Emergency contact relationship not set.");
}

if(isReqSet("emergencyPhone1") && !isValidPhoneNumber($_REQUEST['emergencyPhone1'])) {
    setErrorMsg("Invalid emergency contact phone number.");
}

// Update the account if no errors //////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$editAcct['admin'] = ($_REQUEST['admin'] == "yes") ? '1' : '0';
	$editAcct['firstname'] = $_REQUEST['firstname'];
	$editAcct['lastname'] = $_REQUEST['lastname'];
	$editAcct['birthdate'] = $_REQUEST['birthdate'];
	$editAcct['email'] = $_REQUEST['email'];
	$editAcct['address1'] = $_REQUEST['address1'];
	$editAcct['address2'] = $_REQUEST['address2'];
	$editAcct['city'] = $_REQUEST['city'];
	$editAcct['state'] = $_REQUEST['state'];
	$editAcct['zip'] = $_REQUEST['zip'];
	$editAcct['phone1'] = $_REQUEST['phone1'];
	$editAcct['phone2'] = isReqSet("phone2") ? $_REQUEST['phone2'] : "";
    $editAcct['emergencyName'] = $_REQUEST['emergencyName'];
    $editAcct['emergencyRelationship'] = $_REQUEST['emergencyRelationship'];
    $editAcct['emergencyPhone'] = $_REQUEST['emergencyPhone'];
	$editAcct['student'] = ($_REQUEST['student'] == "yes") ? '1' : '0';
	$editAcct['school'] = ($_REQUEST['student'] == "yes") ? $_REQUEST['school'] : "";
	$editAcct['howHeardAbout'] = $_REQUEST['howHeardAbout'];

	
	if(updateAcctInfo($_REQUEST['editAcctUname'], $editAcct)) {
		setMsg("Account information saved.", true);
		// Force the data on the page to refresh from the database
		$tmpEditAcctUname = $_REQUEST['editAcctUname'];
		unset($_REQUEST);
		$_REQUEST['editAcctUname'] = $tmpEditAcctUname;
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

?>