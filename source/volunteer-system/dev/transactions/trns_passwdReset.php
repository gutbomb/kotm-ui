<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['uname'] = truncate(trim($_REQUEST['uname']), 100);
$_REQUEST['birthmonth'] = truncate(trim($_REQUEST['birthmonth']), 2);
$_REQUEST['birthday'] = truncate(trim($_REQUEST['birthday']), 2);
$_REQUEST['birthyear'] = truncate(trim($_REQUEST['birthyear']), 4);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if( !isReqSet("uname") ||
	(!isReqSet("birthmonth") && !isReqSet("birthday") && !isReqSet("birthyear")) ) {

	setErrorMsg("All fields are required.");
}

if(isReqSet("uname") && !isValidIdentifierChars($_REQUEST['uname'])) {
	setErrorMsg("Invalid username.");
}

if((isReqSet("birthmonth") || isReqSet("birthday") || isReqSet("birthyear")) &&
	!isValidBirthdate($_REQUEST['birthmonth'], $_REQUEST['birthday'], $_REQUEST['birthyear'])) {
	setErrorMsg("Invalid date of birth.");
}

// Reset password if no errors //////////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$birthday = (strlen($_REQUEST['birthday']) != 2) ? "0".$_REQUEST['birthday'] : $_REQUEST['birthday'];
	$birthmonth = (strlen($_REQUEST['birthmonth']) != 2) ? "0".$_REQUEST['birthmonth'] : $_REQUEST['birthmonth'];
	$birthyear = $_REQUEST['birthyear'];
	$birthdate = $birthyear."-".$birthmonth."-".$birthday;
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