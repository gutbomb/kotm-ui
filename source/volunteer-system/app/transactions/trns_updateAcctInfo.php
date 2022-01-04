<?php
// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

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

if( !isReqSet("firstname") ||
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

if(isReqSet("emergencyPhone1") && !isValidPhoneNumber($_REQUEST['emergencyPhone1'])) {
    setErrorMsg("Invalid emergency contact phone number.");
}
if(!isReqSet("emergencyRelationship")) {
    setErrorMsg("Emergency Contact relationship not set.");
}
// Update account information if no errors ///////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$acctInfo['firstname'] = $_REQUEST['firstname'];
	$acctInfo['lastname'] = $_REQUEST['lastname'];
	$acctInfo['birthdate'] = $_REQUEST['birthdate'];
	$acctInfo['email'] = $_REQUEST['email'];
	$acctInfo['address1'] = $_REQUEST['address1'];
	$acctInfo['address2'] = $_REQUEST['address2'];
	$acctInfo['city'] = $_REQUEST['city'];
	$acctInfo['state'] = $_REQUEST['state'];
	$acctInfo['zip'] = $_REQUEST['zip'];
    $acctInfo['phone1'] = $_REQUEST['phone1'];
    $acctInfo['phone2'] = isReqSet("phone2") ? $_REQUEST['phone2'] : "";
    $acctInfo['emergencyName'] = $_REQUEST['emergencyName'];
    $acctInfo['emergencyRelationship'] = $_REQUEST['emergencyRelationship'];
    $acctInfo['emergencyPhone'] = $_REQUEST['emergencyPhone'];
	$acctInfo['student'] = ($_REQUEST['student'] == "yes") ? '1' : '0';
	$acctInfo['school'] = ($_REQUEST['student'] == "yes") ? $_REQUEST['school'] : "";
	$acctInfo['howHeardAbout'] = $_REQUEST['howHeardAbout'];
	
	if(updateAcctInfo($_SESSION['user']['uname'], $acctInfo)) {
		setMsgTitle("Success!");
		setMsg("Account information updated.");
		forwardAllMsgs();
		$REDIRECT = "?page=manageAcct";
		unset($_REQUEST);
	}
	else {
		$link = getVolunteersAdminEmailLink("color:#05aea8;");
		$link = ($link == "") ? "" : " (".$link.")";
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, let us know".$link."!");
	}
}

?>