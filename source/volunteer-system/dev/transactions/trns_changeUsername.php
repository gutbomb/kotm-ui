<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['uname'] = truncate(trim($_REQUEST['uname']), 100);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if(!isReqSet("uname")) {
	setErrorMsg("Username cannot be empty.");
}

if(isReqSet("uname") && !isValidIdentifierChars($_REQUEST['uname'])) {
	setErrorMsg("Username may only consist of letters, numbers, and underscore.");
}

if(isReqSet("uname") && strtolower($_SESSION['user']['uname']) != strtolower($_REQUEST['uname']) && unameExists($_REQUEST['uname'])) {
	setErrorMsg("Username unavailable. Please select a different username.");
}

// Change username if no errors //////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	if(updateUname($_SESSION['user']['uname'], $_REQUEST['uname'])) {
		$_SESSION['user']['uname'] = $_REQUEST['uname'];
		setMsgTitle("Success!");
		setMsg("<p>Username updated.</p><p>".$_SESSION['user']['uname']."</p>");
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