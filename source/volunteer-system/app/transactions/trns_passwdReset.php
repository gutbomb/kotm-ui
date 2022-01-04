<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['uname'] = truncate(trim($_REQUEST['uname']), 100);
$_REQUEST['birthdate'] = truncate(trim(date("Y-m-d", strtotime($_REQUEST['birthdate']))), 10);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if( !isReqSet("uname") ||
	!isReqSet("birthdate")) {

	setErrorMsg("All fields are required.");
}

if(isReqSet("uname") && !isValidIdentifierChars($_REQUEST['uname'])) {
	setErrorMsg("Invalid username.");
}

if(!isReqSet("birthdate")) {
	setErrorMsg("Invalid date of birth.");
}

// Reset password if no errors //////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
    $birthdate = $_REQUEST['birthdate'];
	$acct = mysqli_fetch_assoc( getAccountByUname($_REQUEST['uname']) );
	if($acct && $acct['birthdate'] == $birthdate) {
		$tmpPasswd = generateRandomPassword();
		if(updatePassword($acct['uname'], password_hash($tmpPasswd, PASSWORD_DEFAULT))) {
			require_once('emails/email_passwdResetSuccess.php');
			sendPasswdResetEmail($acct, $tmpPasswd);
			$REDIRECT = "?page=passwdResetSuccess";
			unset($_REQUEST);
		}
		else {
			$link = getVolunteersAdminEmailLink("color:#05aea8;");
			$link = ($link == "") ? "" : " (".$link.")";
			setErrorMsg("An unknown error occurred. Please try again. If the problem persists, let us know".$link."!");
		}
	}
	else {
		// given username doesn't exist, but we don't want to give that information away so send them to the success page
		$REDIRECT = "?page=passwdResetSuccess";
		unset($_REQUEST);
	}
}

?>