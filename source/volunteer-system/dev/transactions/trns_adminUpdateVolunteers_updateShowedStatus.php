<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['manageVDateId'] = truncate(trim($_REQUEST['manageVDateId']), 50);
// max volunteersNeeded (based on field input maxlength + server side truncate) = 999
// max volunteer id length (based on uname maxlength) = 100
// encoded format = (up to 999) volunteer ids separated by (up to 998) "\r\n" delimiters
// Thus, max possible length of selectedVolunteers = 999*100 + 998*2 = 101896
$_REQUEST['selectedVolunteers'] = truncate(trim($_REQUEST['selectedVolunteers']), 101896);
$_REQUEST['showedStatus'] = truncate(trim($_REQUEST['showedStatus']), 12);
$_REQUEST['isSpecialEventVolunteerDate'] = truncate(trim($_REQUEST['isSpecialEventVolunteerDate']), 1);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if(!isReqSet("manageVDateId")) {
	setErrorMsg("No event selected.");
}

if(!isReqSet("showedStatus")) {
	setErrorMsg("Showed status not set.");
}

if( isReqSet("showedStatus") && 
	$_REQUEST['showedStatus'] != "Yes" &&
	$_REQUEST['showedStatus'] != "No" &&
	$_REQUEST['showedStatus'] != "Not Recorded" ) {
	
	setErrorMsg("Invalid showed status.");
}

if(!isReqSet("selectedVolunteers")) {
	setErrorMsg("No volunteers selected.");
}

// Set the showed value if no errors /////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$_REQUEST['selectedVolunteers'] = str_replace("\r", "", $_REQUEST['selectedVolunteers']);
	$selectedVolunteers = explode("\n", $_REQUEST['selectedVolunteers']);
	if($_REQUEST['showedStatus'] == "Yes") {
		$showedStatus = "1";
	}
	else if($_REQUEST['showedStatus'] == "No") {
		$showedStatus = "0";
	}
	else {
		$showedStatus = "NULL";
	}
	
	if($_REQUEST['isSpecialEventVolunteerDate'] == "0") {
		$success = updateVolunteersSetShowed($_REQUEST['manageVDateId'], $selectedVolunteers, $showedStatus);
	}
	else {
		$success = updateSpecialEventVolunteersSetShowed($_REQUEST['manageVDateId'], $selectedVolunteers, $showedStatus);
	}
	
	if($success) {
		setMsg("Showed status updated.", true);
		// Force the data on the page to refresh from the database
		$tmpManageVDateId = $_REQUEST['manageVDateId'];
		$isSpecialEventVolunteerDate = $_REQUEST['isSpecialEventVolunteerDate'];
		unset($_REQUEST);
		if($isSpecialEventVolunteerDate == "0") {
			$_REQUEST['manageVDateId'] = $tmpManageVDateId;
		}
		else {
			$_REQUEST['manageSpecialEventDateId'] = $tmpManageVDateId;
		}
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
		if($_REQUEST['isSpecialEventVolunteerDate'] == "1") {
			$_REQUEST['manageSpecialEventDateId'] = $_REQUEST['manageVDateId'];
			unset($_REQUEST['manageVDateId']);
		}
	}
}
else if($_REQUEST['isSpecialEventVolunteerDate'] == "1") {
	$_REQUEST['manageSpecialEventDateId'] = $_REQUEST['manageVDateId'];
	unset($_REQUEST['manageVDateId']);
}

?>