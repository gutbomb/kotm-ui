<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['editAcctUname'] = truncate(trim($_REQUEST['editAcctUname']), 100);
$_REQUEST['admin'] = truncate(trim($_REQUEST['admin']), 3);
$_REQUEST['firstname'] = truncate(trim($_REQUEST['firstname']), 50);
$_REQUEST['lastname'] = truncate(trim($_REQUEST['lastname']), 50);
$_REQUEST['birthmonth'] = truncate(trim($_REQUEST['birthmonth']), 2);
$_REQUEST['birthday'] = truncate(trim($_REQUEST['birthday']), 2);
$_REQUEST['birthyear'] = truncate(trim($_REQUEST['birthyear']), 4);
$_REQUEST['email'] = truncate(trim($_REQUEST['email']), 50);
$_REQUEST['address1'] = truncate(trim($_REQUEST['address1']), 100);
$_REQUEST['address2'] = truncate(trim($_REQUEST['address2']), 100);
$_REQUEST['city'] = truncate(trim($_REQUEST['city']), 50);
$_REQUEST['state'] = truncate(trim($_REQUEST['state']), 2);
$_REQUEST['zip'] = truncate(trim($_REQUEST['zip']), 5);
$_REQUEST['phone1part1'] = truncate(trim($_REQUEST['phone1part1']), 3);
$_REQUEST['phone1part2'] = truncate(trim($_REQUEST['phone1part2']), 3);
$_REQUEST['phone1part3'] = truncate(trim($_REQUEST['phone1part3']), 4);
$_REQUEST['phone2part1'] = truncate(trim($_REQUEST['phone2part1']), 3);
$_REQUEST['phone2part2'] = truncate(trim($_REQUEST['phone2part2']), 3);
$_REQUEST['phone2part3'] = truncate(trim($_REQUEST['phone2part3']), 4);
$_REQUEST['student'] = truncate(trim($_REQUEST['student']), 3);
$_REQUEST['school'] = truncate(trim($_REQUEST['school']), 100);
$_REQUEST['howHeardAbout'] = truncate(trim($_REQUEST['howHeardAbout']), 500);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if( !isReqSet("admin") ||
	!isReqSet("firstname") ||
	!isReqSet("lastname") ||
	(!isReqSet("birthmonth") && !isReqSet("birthday") && !isReqSet("birthyear")) ||
	!isReqSet("email") ||
	!isReqSet("address1") ||
	!isReqSet("city") ||
	!isReqSet("state") ||
	!isReqSet("zip") ||
	(!isReqSet("phone1part1") && !isReqSet("phone1part2") && !isReqSet("phone1part3")) ||
	!isReqSet("student") ||
	($_REQUEST['student'] == 'yes' && !isReqSet("school")) ) {

	setErrorMsg("Some required fields are blank.");
}

if(!unameExists($_REQUEST['editAcctUname'])) {
	setErrorMsg("Username not found in database.");
}

if((isReqSet("birthmonth") || isReqSet("birthday") || isReqSet("birthyear")) &&
	!isValidBirthdate($_REQUEST['birthmonth'], $_REQUEST['birthday'], $_REQUEST['birthyear'])) {
	setErrorMsg("Invalid date of birth.");
}

if(isReqSet("email") && !isValidEmailAddress($_REQUEST['email'])) {
	setErrorMsg("Invalid email address.");
}

if(isReqSet("zip") && !isValidZipCode($_REQUEST['zip'])) {
	setErrorMsg("Invalid zip code.");
}

if((isReqSet("phone1part1") || isReqSet("phone1part2") || isReqSet("phone1part3")) &&
	!isValidPhoneNumber($_REQUEST['phone1part1'], $_REQUEST['phone1part2'], $_REQUEST['phone1part3'])) {
	setErrorMsg("Invalid primary phone number.");
}

if((isReqSet("phone2part1") || isReqSet("phone2part2") || isReqSet("phone2part3")) &&
	!isValidPhoneNumber($_REQUEST['phone2part1'], $_REQUEST['phone2part2'], $_REQUEST['phone2part3'])) {
	setErrorMsg("Invalid alternate phone number.");
}

if(isReqSet("student") && $_REQUEST['student'] != "yes" && $_REQUEST['student'] != "no") {
	setErrorMsg("Invalid response to student status question.");
}

// Update the account if no errors //////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$editAcct['admin'] = ($_REQUEST['admin'] == "yes") ? '1' : '0';
	$editAcct['firstname'] = $_REQUEST['firstname'];
	$editAcct['lastname'] = $_REQUEST['lastname'];
	$editAcct['birthdate'] = $_REQUEST['birthyear']."-".$_REQUEST['birthmonth']."-".$_REQUEST['birthday'];
	$editAcct['email'] = $_REQUEST['email'];
	$editAcct['address1'] = $_REQUEST['address1'];
	$editAcct['address2'] = $_REQUEST['address2'];
	$editAcct['city'] = $_REQUEST['city'];
	$editAcct['state'] = $_REQUEST['state'];
	$editAcct['zip'] = $_REQUEST['zip'];
	$editAcct['phone1'] = $_REQUEST['phone1part1']."-".$_REQUEST['phone1part2']."-".$_REQUEST['phone1part3'];
	$editAcct['phone2'] = isReqSet("phone2part1") ? $_REQUEST['phone2part1']."-".$_REQUEST['phone2part2']."-".$_REQUEST['phone2part3'] : "";
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