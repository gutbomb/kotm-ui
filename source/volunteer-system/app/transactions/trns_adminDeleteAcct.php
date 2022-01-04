<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['deleteAcctUname'] = truncate(trim($_REQUEST['deleteAcctUname']), 100);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

	/* 
	Nothing need be done here.
	The form does not allow users to directly affect these request values.
	The database also protects itself by sanitizing all input. Bogus data
	will simply result in a database error and a generic failure message.
	*/

// Delete the account /////////////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(deleteAccount($_REQUEST['deleteAcctUname'])) {
		setMsg("Account deleted.", true);
		unset($_REQUEST['deleteAcctUname']);
		unset($_REQUEST['deleteAcct']);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

?>