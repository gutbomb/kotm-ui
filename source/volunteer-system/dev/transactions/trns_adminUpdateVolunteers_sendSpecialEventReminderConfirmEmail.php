<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['manageVDateId'] = truncate(trim($_REQUEST['manageVDateId']), 50);
// max volunteersNeeded (based on field input maxlength + server side truncate) = 999
// max volunteer id length (based on uname maxlength) = 100
// encoded format = (up to 999) volunteer ids separated by (up to 998) "\r\n" delimiters
// Thus, max possible length of selectedVolunteers = 999*100 + 998*2 = 101896
$_REQUEST['selectedVolunteers'] = truncate(trim($_REQUEST['selectedVolunteers']), 101896);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if(!isReqSet("manageVDateId")) {
	setErrorMsg("No event selected.");
}
else {
	$vDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['manageVDateId']) );
	if($vDate) {
		$currDate = getMySqlDate(time());
		if($vDate['date'] < $currDate) {
			setErrorMsg("Cannot send reminder/confirm email for past event.");
		}
	}
	else {
		setErrorMsg("Event not found in database.");
	}
}

$accts = array();

if(!isReqSet("selectedVolunteers")) {
	setErrorMsg("No volunteers selected.");
}
else {
	$_REQUEST['selectedVolunteers'] = str_replace("\r", "", $_REQUEST['selectedVolunteers']);
	$selectedVolunteers = explode("\n", $_REQUEST['selectedVolunteers']);
	for($i=0; $i<count($selectedVolunteers); $i++) {
		$acct = mysqli_fetch_assoc( getAccountByUname($selectedVolunteers[$i]) );
		if($acct) {
			$accts[$i] = $acct;
		}
		else {
			setErrorMsg("System error. Email not sent. Please try again.");
			break;
		}
	}
}

// Send the emails if no errors /////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	require_once('emails/specialEvents/email_signupConfirmSpecialEvent.php');
	for($i=0; $i<count($accts); $i++) {
		$confirmId = mysqli_fetch_assoc( getSpecialEventVolunteerConfirmId($accts[$i]['uname'], $vDate['id']) );
		if($confirmId && $confirmId['confirmId'] != null) {
			$confirmId = $confirmId['confirmId'];
		}
		else {
			$confirmId = generateRandomPassword(20);
			setSpecialEventVolunteerConfirmId($accts[$i]['uname'], $vDate['id'], $confirmId);
		}
		if($STAGE == "DEV") {
			$link = "https://".$_SERVER["SERVER_NAME"]."/volunteer-system/dev/";
		}
		else {
			$link = "https://".$_SERVER["SERVER_NAME"]."/volunteer-system/app/";
		}
		$link .= "?page=confirmSignupSpecialEvent&uname=".$accts[$i]['uname']."&vDateId=".$vDate['id']."&confirmId=$confirmId";
		sendSignupConfirmSpecialEventEmail($accts[$i], $vDate, $link);
	}
	$volunteerList = str_replace("\n", ", ", $_REQUEST['selectedVolunteers']);
	setMsg("<p>Reminder/confirm email sent to:</p><p>$volunteerList</p>", true);
	// Force the data on the page to refresh from the database
	$tmpManageVDateId = $_REQUEST['manageVDateId'];
	unset($_REQUEST);
	$_REQUEST['manageSpecialEventDateId'] = $tmpManageVDateId;
}
else {
	$_REQUEST['manageSpecialEventDateId'] = $_REQUEST['manageVDateId'];
	unset($_REQUEST['manageVDateId']);
}

?>