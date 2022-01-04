<?php

// Truncate any input that is too long ////////////////////////////////////////////////////////////////////////

$_REQUEST['uname'] = truncate(trim($_REQUEST['uname']), 100);
$_REQUEST['password1'] = truncate($_REQUEST['password1'], 50);
$_REQUEST['password2'] = truncate($_REQUEST['password2'], 50);
$_REQUEST['firstname'] = truncate(trim($_REQUEST['firstname']), 50);
$_REQUEST['lastname'] = truncate(trim($_REQUEST['lastname']), 50);
$_REQUEST['birthmonth'] = truncate(trim($_REQUEST['birthmonth']), 2);
$_REQUEST['birthday'] = truncate(trim($_REQUEST['birthday']), 2);
$_REQUEST['birthyear'] = truncate(trim($_REQUEST['birthyear']), 4);
$_REQUEST['email'] = truncate(trim($_REQUEST['email']), 50);
$_REQUEST['address1'] = truncate(trim($_REQUEST['address1']), 100);
$_REQUEST['address2'] = truncate(trim($_REQUEST['address2']), 100);
$_REQUEST['city'] = truncate(trim($_REQUEST['city']), 50);
$_REQUEST['state'] = truncate(trim($_REQUEST['state']), 2);
$_REQUEST['zip'] = truncate(trim($_REQUEST['zip']), 5);
$_REQUEST['phone1part1'] = truncate(trim($_REQUEST['phone1part1']), 3);
$_REQUEST['phone1part2'] = truncate(trim($_REQUEST['phone1part2']), 3);
$_REQUEST['phone1part3'] = truncate(trim($_REQUEST['phone1part3']), 4);
$_REQUEST['phone2part1'] = truncate(trim($_REQUEST['phone2part1']), 3);
$_REQUEST['phone2part2'] = truncate(trim($_REQUEST['phone2part2']), 3);
$_REQUEST['phone2part3'] = truncate(trim($_REQUEST['phone2part3']), 4);
$_REQUEST['student'] = truncate(trim($_REQUEST['student']), 3);
$_REQUEST['school'] = truncate(trim($_REQUEST['school']), 100);

$_REQUEST['arrestedAffirm'] = truncate(trim($_REQUEST['arrestedAffirm']), 3);
$_REQUEST['sexOffenderAgree'] = truncate(trim($_REQUEST['sexOffenderAgree']), 3);
$_REQUEST['drugAffirm'] = truncate(trim($_REQUEST['drugAffirm']), 3);
$_REQUEST['soundMindAgree'] = truncate(trim($_REQUEST['soundMindAgree']), 3);
$_REQUEST['noPhotoAgree'] = truncate(trim($_REQUEST['noPhotoAgree']), 3);
$_REQUEST['noInfoAgree'] = truncate(trim($_REQUEST['noInfoAgree']), 3);
$_REQUEST['usePhotoAgree'] = truncate(trim($_REQUEST['usePhotoAgree']), 3);
$_REQUEST['kotmAgree'] = truncate(trim($_REQUEST['kotmAgree']), 3);
$_REQUEST['notResponsibleAgree'] = truncate(trim($_REQUEST['notResponsibleAgree']), 3);

// Validate the input //////////////////////////////////////////////////////////////////////////////////////

if( !isReqSet("uname") ||
	(!isReqSet("password1") && !isReqSet("password2")) ||
	!isReqSet("firstname") ||
	!isReqSet("lastname") ||
	(!isReqSet("birthmonth") && !isReqSet("birthday") && !isReqSet("birthyear")) ||
	!isReqSet("email") ||
	!isReqSet("address1") ||
	!isReqSet("city") ||
	!isReqSet("state") ||
	!isReqSet("zip") ||
	(!isReqSet("phone1part1") && !isReqSet("phone1part2") && !isReqSet("phone1part3")) ||
	!isReqSet("student") ||
	($_REQUEST['student'] == 'yes' && !isReqSet("school"))
	) {

	setErrorMsg("Some required fields are blank.");
}

if(isReqSet("uname") && !isValidIdentifierChars($_REQUEST['uname'])) {
	setErrorMsg("Username may only consist of letters, numbers, and underscore.");
}

if(isReqSet("uname") && unameExists($_REQUEST['uname'])) {
	setErrorMsg("Username unavailable. Please select a different username.");
}

if((isReqSet("password1") || isReqSet("password2")) && $_REQUEST['password1'] != $_REQUEST['password2']) {
	setErrorMsg("Passwords do not match.");
}

if((isReqSet("birthmonth") || isReqSet("birthday") || isReqSet("birthyear")) &&
	!isValidBirthdate($_REQUEST['birthmonth'], $_REQUEST['birthday'], $_REQUEST['birthyear'])) {
	setErrorMsg("Invalid date of birth.");
}

if(isReqSet("email") && !isValidEmailAddress($_REQUEST['email'])) {
	setErrorMsg("Invalid email address.");
}

if(isReqSet("zip") && !isValidZipCode($_REQUEST['zip'])) {
	setErrorMsg("Invalid zip code.");
}

if((isReqSet("phone1part1") || isReqSet("phone1part2") || isReqSet("phone1part3")) &&
	!isValidPhoneNumber($_REQUEST['phone1part1'], $_REQUEST['phone1part2'], $_REQUEST['phone1part3'])) {
	setErrorMsg("Invalid primary phone number.");
}

if((isReqSet("phone2part1") || isReqSet("phone2part2") || isReqSet("phone2part3")) &&
	!isValidPhoneNumber($_REQUEST['phone2part1'], $_REQUEST['phone2part2'], $_REQUEST['phone2part3'])) {
	setErrorMsg("Invalid alternate phone number.");
}

if(isReqSet("student") && $_REQUEST['student'] != "yes" && $_REQUEST['student'] != "no") {
	setErrorMsg("Invalid response to student status question.");
}

if($_REQUEST['arrestedAffirm'] != "yes" || $_REQUEST['sexOffenderAgree'] != "yes" || $_REQUEST['drugAffirm'] != "yes" || $_REQUEST['soundMindAgree'] != "yes"
			|| $_REQUEST['noPhotoAgree'] != "yes" || $_REQUEST['noInfoAgree'] != "yes" || $_REQUEST['usePhotoAgree'] != "yes" || $_REQUEST['kotmAgree'] != "yes"
			|| $_REQUEST['notResponsibleAgree'] != "yes")
{
	setErrorMsg("You have not affirmed/agreed with all of the required statements. If you cannot do so at this time, we ask that you not volunteer. Please direct any questions to ".getVolunteersAdminEmailLink("color:#05aea8;").".");
}

// Create the account if no errors ///////////////////////////////////////////////////////////////////////////

if(!isErrors()) {
	$newAcct['uname'] = $_REQUEST['uname'];
	$newAcct['password'] = password_hash($_REQUEST['password1'], PASSWORD_DEFAULT);
	$newAcct['firstname'] = $_REQUEST['firstname'];
	$newAcct['lastname'] = $_REQUEST['lastname'];
	$newAcct['birthdate'] = $_REQUEST['birthyear']."-".$_REQUEST['birthmonth']."-".$_REQUEST['birthday'];
	$newAcct['email'] = $_REQUEST['email'];
	$newAcct['address1'] = $_REQUEST['address1'];
	$newAcct['address2'] = $_REQUEST['address2'];
	$newAcct['city'] = $_REQUEST['city'];
	$newAcct['state'] = $_REQUEST['state'];
	$newAcct['zip'] = $_REQUEST['zip'];
	$newAcct['phone1'] = $_REQUEST['phone1part1']."-".$_REQUEST['phone1part2']."-".$_REQUEST['phone1part3'];
	$newAcct['phone2'] = isReqSet("phone2part1") ? $_REQUEST['phone2part1']."-".$_REQUEST['phone2part2']."-".$_REQUEST['phone2part3'] : "";
	$newAcct['student'] = ($_REQUEST['student'] == "yes") ? '1' : '0';
	$newAcct['school'] = ($_REQUEST['student'] == "yes") ? $_REQUEST['school'] : "";
	
	if(createAccount($newAcct)) {
		require_once('emails/email_createAcctSuccess.php');
		sendCreateAcctSuccessEmail($newAcct);
		// Redirect to eliminate form post from browser history, so reload or back/forward won't cause issues 
		// (e.g., produce a broken iframe by trying to repeat the post).
		$REDIRECT = "?page=createAcctSuccess";
		unset($_REQUEST);
	}
	else {
		$link = getVolunteersAdminEmailLink("color:#05aea8;");
		$link = ($link == "") ? "" : " (".$link.")";
		setErrorMsg("An unknown error occurred. Please try again. If the problem persists, let us know".$link."!");
	}
}
else {
	setErrorMsg("Correct any errors and re-enter your password.");
}

// this 'if' conditional should *not* be an 'else' or 'else if'
// the error message in the 'else' within the 'if' above needs to cascade to here
if(isErrors()) {
	// Redirect to eliminate form post from browser history, so reload or back/forward won't cause issues 
	// (e.g., produce a broken iframe by trying to repeat the post). Forward GETs/POSTs so page re-loads
	// properly after redirect. Queue up the error messages in the session so they get shown only once and 
	// not repeated on back/forward navigation.
	forwardAllMsgs();
	// Unset the submit flag (so we don't end up with an infinite loop of replaying the submit, which has
	// an error, when we forward the GETs/POSTs)
	unset($_REQUEST['createAcctSubmit']);
	$REDIRECT = "?page=".$PAGE."&preserveGetsPosts=".getEncodedGetsPostsString();
	unset($_REQUEST);
}

?>