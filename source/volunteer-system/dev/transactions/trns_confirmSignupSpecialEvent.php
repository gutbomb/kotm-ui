<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['uname'] = truncate(trim($_REQUEST['uname']), 100);
$_REQUEST['vDateId'] = truncate(trim($_REQUEST['vDateId']), 50);
$_REQUEST['confirmId'] = truncate(trim($_REQUEST['confirmId']), 20);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if( !isReqSet("uname") ||
	!isReqSet("vDateId") ||
	!isReqSet("confirmId") ) {
	
	// this message does not get directly shown, but is needed to mark that an error occurred
	setErrorMsg("Error confirming sign-up.", false);
}
else {
	$confirmId = mysqli_fetch_assoc( getSpecialEventVolunteerConfirmId($_REQUEST['uname'], $_REQUEST['vDateId']) );
	$vDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['vDateId']) );
	if( !isset($confirmId['confirmId']) || 
		$confirmId['confirmId'] == null || 
		$confirmId['confirmId'] != $_REQUEST['confirmId'] ||
		$vDate['date'] < getMySqlDate(time())) {
		
		// this message does not get directly shown, but is needed to mark that an error occurred
		setErrorMsg("Error confirming sign-up.", false);
	}
}

// Confirm the sign-up if no errors /////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(setSpecialEventVolunteerConfirmed($_REQUEST['uname'], $_REQUEST['vDateId'])) {
		// Success!
	}
	else {
		// this message does not get directly shown, but is needed to mark that an error occurred
		setErrorMsg("Error confirming sign-up.", false);
	}
}

?>