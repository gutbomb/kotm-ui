<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['editAcctUname'] = truncate(trim($_REQUEST['editAcctUname']), 100);
$_REQUEST['password1'] = truncate($_REQUEST['password1'], 50);
$_REQUEST['password2'] = truncate($_REQUEST['password2'], 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if(!unameExists($_REQUEST['editAcctUname'])) {
	setErrorMsg("Username not found in database.");
}

if(!isReqSet("password1") && !isReqSet("password2")) {
	setErrorMsg("Password cannot be empty.");
}

if((isReqSet("password1") || isReqSet("password2")) && $_REQUEST['password1'] != $_REQUEST['password2']) {
	setErrorMsg("Passwords do not match. Please try again.");
}

// Reset password if no errors //////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(updatePassword($_REQUEST['editAcctUname'], password_hash($_REQUEST['password1'], PASSWORD_DEFAULT))) {
		setMsg("Password updated.", true);
	}
	else {
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, contact the app developer.");
	}
}

// For BOTH success AND error cases...
// Redirect to eliminate form post from browser history, so reload or back/forward won't cause issues 
// (e.g., produce a broken iframe by trying to repeat the post). Don't need to forward any GETs/POSTs
// because form fields are all passwords and need to get erased anyway. Queue up any messages in 
// the session so they get shown only once and not repeated on back/forward navigation.
forwardAllMsgs();
$REDIRECT = "?page=".$PAGE."&editAcctUname=".$_REQUEST['editAcctUname'];
unset($_REQUEST);

?>