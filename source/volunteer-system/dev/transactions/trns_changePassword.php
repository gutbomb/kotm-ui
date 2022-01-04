<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['password'] = truncate($_REQUEST['password'], 50);
$_REQUEST['password1'] = truncate($_REQUEST['password1'], 50);
$_REQUEST['password2'] = truncate($_REQUEST['password2'], 50);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if(!password_verify($_REQUEST['password'], $_SESSION['user']['password'])) {
	setErrorMsg("Current password incorrect.");
}

if(!isReqSet("password1") && !isReqSet("password2")) {
	setErrorMsg("New Password cannot be empty.");
}

if((isReqSet("password1") || isReqSet("password2")) && $_REQUEST['password1'] != $_REQUEST['password2']) {
	setErrorMsg("New passwords do not match. Please try again.");
}

// Change password if no errors ////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(updatePassword($_SESSION['user']['uname'], password_hash($_REQUEST['password1'], PASSWORD_DEFAULT))) {
		// Redirect to eliminate form post from browser history, so reload or back/forward won't cause issues 
		// (e.g., produce a broken iframe by trying to repeat the post). Queue up the success message in 
		// the session so it gets shown only once and not repeated on back/forward navigation.
		setMsgTitle("Success!");
		setMsg("Password updated.");
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

// this 'if' conditional should *not* be an 'else' or 'else if'
// the error message in the 'else' within the 'if' above needs to cascade to here
if(isErrors()) {
	// Redirect to eliminate form post from browser history, so reload or back/forward won't cause issues 
	// (e.g., produce a broken iframe by trying to repeat the post). Don't need to forward any GETs/POSTs
	// because form fields are all passwords and need to get erased anyway. Queue up the error messages in 
	// the session so they get shown only once and not repeated on back/forward navigation.
	forwardAllMsgs();
	$REDIRECT = "?page=".$PAGE;
	unset($_REQUEST);
}

?>