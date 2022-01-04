<?php

// If there is no transaction ID, then we'll let the transaction proceed. The purpose of
// the IDs is just to make sure accidental replays don't happen due to browser navigation.
// And we'll make sure to include transaction IDs where necessary to ensure that happens.
// If a user wants to modify a request and drop the transaction ID, they're free to do that.
// It will only impact them and their data (we trust admins not to do this), and the 
// system is designed to protect data integrity against repeats at the data level (e.g.,
// deleting something twice or creating something twice may result in an error message,
// but it won't crash the system or hurt data integrity. So we're really not worried about
// transaction replays from a hacking standpoint, rather we want to protect user experience,
// as well as, for admins, a few types of replays would be problematic (like resending 
// reminder emails). So transaction IDs are for convenience. Moreover, some transactions
// may need to be linked directly to from an external URL (e.g., the URL provided in 
// reminder emails that volutneers click to confirm they are still planning to come).
// In those cases, we'd prefer not to use a transaction ID -- replays are just fine, and
// if we embedd an ID, then the URL (which can be easily saved and accessed again in the 
// future -- unlike for browser history) could get clicked months later after it has been
// purged from the used-IDs cache, and so would effectively create a replay anyway. Plus
// it's makes the URL much longer and grittier looking (so we'll try to employ them only
// for form submissions where the URL is not readily visible to the end user since this
// whole app is running inside an iframe).

$TRANSACTION_VALID = true; // default if there's no transaction ID (see comment above)

if(isset($_REQUEST['transId']) and !validateTransactionIdEncoded($_REQUEST['transId'])) {
	// A transaction ID has been provided, but it's not valid (either corrupt, manipulated,
	// made up, or most likely, already used, i.e., a replay) -- so invalidate this 
	// transaction request.
	$TRANSACTION_VALID = false;
}

// Admin Update Volunteer Coordinator Info
if($PAGE == "adminServices" && isset($_REQUEST['adminSetVolunteersAdminSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateVolunteersAdminInfo.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST);
	}
}

// Admin Update Developer Info
if($PAGE == "adminServices" && isset($_REQUEST['adminSetWebsiteAdminSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateWebsiteAdminInfo.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST);
	}
}

// Create Account
if($PAGE == "createAcct" && isset($_REQUEST['createAcctSubmit'])) {
	// No transaction IDs needed here, this is a post w/redirect pattern to prevent replays
	require_once('transactions/trns_createAcct.php');
}

// Change Username
if($PAGE == "changeUsername" && isset($_REQUEST['changeUsernameSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_changeUsername.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST);
	}
}

// Change Password
if($PAGE == "changePassword" && isset($_REQUEST['changePasswordSubmit'])) {
	// No transaction IDs needed here, this is a post w/redirect pattern to prevent replays
	require_once('transactions/trns_changePassword.php');
}

// Update Account Information
if($PAGE == "updateAcctInfo" && isset($_REQUEST['updateAcctInfoSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_updateAcctInfo.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST);
	}
}

// Admin Create Account
if($PAGE == "adminManageVolunteerDate" && isset($_REQUEST['adminCreateAcctSubmit']) || 
   $PAGE == "adminManageSpecialEventDate" && isset($_REQUEST['adminCreateAcctSubmit'])) {
	// No transaction IDs needed here, this is a post w/redirect pattern to prevent replays
	require_once('transactions/trns_adminCreateAcct.php');
}

// Admin Edit Account
if($PAGE == "adminManageAcct" && isset($_REQUEST['adminEditAcctSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminEditAcct.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		$tmpEditAcctUname = $_REQUEST['editAcctUname'];
		unset($_REQUEST);
		$_REQUEST['editAcctUname'] = $tmpEditAcctUname;
	}
}

// Admin Reset Password
if($PAGE == "adminManageAcct" && isset($_REQUEST['adminPasswdResetSubmit'])) {
	// No transaction IDs needed here, this is a post w/redirect pattern to prevent replays
	require_once('transactions/trns_adminPasswdReset.php');
}

// Reset Password
if($PAGE == "passwdReset" && isset($_REQUEST['passwdResetSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_passwdReset.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST);
	}
}

// Admin Delete Account
if($PAGE == "adminManageAccts" && isset($_REQUEST['deleteAcct']) && $_REQUEST['deleteAcct'] == 'true') {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminDeleteAcct.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST['deleteAcctUname']);
		unset($_REQUEST['deleteAcct']);
	}
}

// Admin Add Volunteer Date
if($PAGE == "adminManageVolunteerDates" && isset($_REQUEST['adminAddVolunteerDateSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminAddVolunteerDate.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST);
	}
}

// Admin Add Special Event Date
if($PAGE == "adminManageVolunteerDates" && isset($_REQUEST['adminAddSpecialEventDateSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminAddSpecialEventDate.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST);
	}
}

// Admin Delete Volunteer Date
if($PAGE == "adminManageVolunteerDates" && isset($_REQUEST['deleteVDate']) && $_REQUEST['deleteVDate'] == 'true') {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminDeleteVolunteerDate.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST);
	}
}

// Admin Delete Special Event Date
if($PAGE == "adminManageVolunteerDates" && isset($_REQUEST['deleteSpecialEventDate']) && $_REQUEST['deleteSpecialEventDate'] == 'true') {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminDeleteSpecialEventDate.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST);
	}
}

// Admin Update Volunteer Date
if($PAGE == "adminManageVolunteerDate" && isset($_REQUEST['adminUpdateVolunteersDateSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateVolunteerDate.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		$tmpManageVDateId = $_REQUEST['manageVDateId'];
		unset($_REQUEST);
		$_REQUEST['manageVDateId'] = $tmpManageVDateId;
	}
}

// Admin Update Special Event Date
if($PAGE == "adminManageSpecialEventDate" && isset($_REQUEST['adminUpdateSpecialEventDateSubmit'])) {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateSpecialEventDate.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		$tmpManageVDateId = $_REQUEST['manageSpecialEventDateId'];
		unset($_REQUEST);
		$_REQUEST['manageSpecialEventDateId'] = $tmpManageVDateId;
	}
}

// User Delete Volunteer Sign-up (not admin)
if($PAGE == "myVolunteerSchedule" && isset($_REQUEST['deleteSignup']) && $_REQUEST['deleteSignup'] == "true") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_volunteerDeleteSignup.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST['deleteSignupVDateId']);
		unset($_REQUEST['deleteSignup']);
	}
}

// User Delete Special Event Sign-up (not admin)
else if($PAGE == "myVolunteerSchedule" && isset($_REQUEST['deleteSpecialEventSignup']) && $_REQUEST['deleteSpecialEventSignup'] == "true") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_volunteerDeleteSpecialEventSignup.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST['deleteSignupSpecialEventDateId']);
		unset($_REQUEST['deleteSpecialEventSignup']);
	}
}

// Admin Remove Volunteer Sign-up
if($PAGE == "adminManageVolunteerDate" && isset($_REQUEST['removeSignup']) && $_REQUEST['removeSignup'] == "true") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminRemoveSignup.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST['removeSignupUname']);
		unset($_REQUEST['removeSignupVDateId']);
		unset($_REQUEST['removeSignup']);
	}
}

// Admin Remove Special Event Sign-up
if($PAGE == "adminManageSpecialEventDate" && isset($_REQUEST['removeSignup']) && $_REQUEST['removeSignup'] == "true") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminRemoveSpecialEventSignup.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		unset($_REQUEST['removeSignupUname']);
		unset($_REQUEST['removeSignupSpecialEventDateId']);
		unset($_REQUEST['removeSignup']);
	}
}

// Admin Set Showed Status
if($PAGE == "adminManageVolunteerDate" && isset($_REQUEST['volunteerAction']) && $_REQUEST['volunteerAction'] == "updateShowedStatus") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateVolunteers_updateShowedStatus.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		$tmpManageVDateId = $_REQUEST['manageVDateId'];
		unset($_REQUEST);
		$_REQUEST['manageVDateId'] = $tmpManageVDateId;
	}
}

// Admin Set Showed Status - Special Event
if($PAGE == "adminManageSpecialEventDate" && isset($_REQUEST['volunteerAction']) && $_REQUEST['volunteerAction'] == "updateShowedStatus") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateVolunteers_updateShowedStatus.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		$tmpManageVDateId = $_REQUEST['manageVDateId'];
		unset($_REQUEST);
		$_REQUEST['manageSpecialEventDateId'] = $tmpManageVDateId;
	}
}

// Admin Send Reminder/Confirm Email
if($PAGE == "adminManageVolunteerDate" && isset($_REQUEST['volunteerAction']) && $_REQUEST['volunteerAction'] == "sendReminderConfirmEmail") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateVolunteers_sendReminderConfirmEmail.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		$tmpManageVDateId = $_REQUEST['manageVDateId'];
		unset($_REQUEST);
		$_REQUEST['manageVDateId'] = $tmpManageVDateId;
	}
}

// Admin Send Reminder/Confirm Email - Special Event
if($PAGE == "adminManageSpecialEventDate" && isset($_REQUEST['volunteerAction']) && $_REQUEST['volunteerAction'] == "sendReminderConfirmEmail") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateVolunteers_sendSpecialEventReminderConfirmEmail.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		$tmpManageVDateId = $_REQUEST['manageVDateId'];
		unset($_REQUEST);
		$_REQUEST['manageSpecialEventDateId'] = $tmpManageVDateId;
	}
}

// Admin Resend Sign-up Email
if($PAGE == "adminManageVolunteerDate" && isset($_REQUEST['volunteerAction']) && $_REQUEST['volunteerAction'] == "resendSignupEmail") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateVolunteers_resendSignupEmail.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		$tmpManageVDateId = $_REQUEST['manageVDateId'];
		unset($_REQUEST);
		$_REQUEST['manageVDateId'] = $tmpManageVDateId;
	}
}

// Admin Resend Sign-up Email - Special Event
if($PAGE == "adminManageSpecialEventDate" && isset($_REQUEST['volunteerAction']) && $_REQUEST['volunteerAction'] == "resendSignupEmail") {
	if($TRANSACTION_VALID) { // this transaction type uses a transaction ID
		require_once('transactions/trns_adminUpdateVolunteers_resendSpecialEventSignupEmail.php');
	}
	else {
		// Force the page to refresh the form's data from DB on browser back/forward/reload.
		// So the user doesn't see old or bad data still haning around and think it's current.
		$tmpManageVDateId = $_REQUEST['manageVDateId'];
		unset($_REQUEST);
		$_REQUEST['manageSpecialEventDateId'] = $tmpManageVDateId;
	}
}

// Confirm Sign-up
if($PAGE == "confirmSignup") {
	// No transaction IDs needed here, this is a post w/redirect pattern to prevent replays
	require_once('transactions/trns_confirmSignup.php');
}

// Confirm Sign-up Special Event
if($PAGE == "confirmSignupSpecialEvent") {
	// No transaction IDs needed here, this is a post w/redirect pattern to prevent replays
	require_once('transactions/trns_confirmSignupSpecialEvent.php');
}

// Sign Up To Volunteer
if( ($PAGE == "signupConfirm" || $PAGE == "specialEventSignupConfirm") && !$TRANSACTION_VALID ) { // this transaction type uses a transaction ID
	// Force the page to refresh the form's data from DB on browser back/forward/reload.
	// So the user doesn't see old or bad data still haning around and think it's current.
	$PAGE = "signup";
	unset($_REQUEST);
}
else if($PAGE == "signupConfirm" && isset($_REQUEST['signupVDateId']) && volunteerExists($_SESSION['user']['uname'], $_REQUEST['signupVDateId'])) {
	setErrorMsg("You're already signed up for this event!");
	$PAGE = "signup";
	unset($_REQUEST);
}
else if($PAGE == "specialEventSignupConfirm" && isset($_REQUEST['signupSpecialEventDateId']) && specialEventVolunteerExists($_SESSION['user']['uname'], $_REQUEST['signupSpecialEventDateId'])) {
	setErrorMsg("You're already signed up for this event!");
	$PAGE = "signup";
	unset($_REQUEST);
}
else if($PAGE == "signupConfirm") {
	if(isset($_REQUEST['signupVDateId'])) {
		$vDate = mysqli_fetch_assoc( getVolunteerDateById( truncate(trim($_REQUEST['signupVDateId']), 50) ) );
		if($vDate) {
			$ageRestrictionErrorMsg = getAnyAgeRestrictionErrorMsg($_SESSION['user']['birthdate'], $vDate['type']);
			if($ageRestrictionErrorMsg != "") {
				setErrorMsg($ageRestrictionErrorMsg);
				$PAGE = "signup";
				unset($_REQUEST);
			}
			else {			
				$scheduled = mysqli_fetch_assoc( getCountVolunteersForVolunteerDateById($vDate['id']) );
				$needed = ( (int)$vDate['volunteersNeeded'] - (int)$scheduled['count(*)'] );		
				if($needed <= 0) {
					setErrorMsg("Event is full.", !isset($_REQUEST['updateAcctInfoSubmit']));
					$PAGE = "signup";
					unset($_REQUEST);
				}
				else if(isset($_REQUEST['updateAcctInfoSubmit'])) {
					require_once('transactions/trns_signupConfirm.php');
				}
			}
			
		}
		else {
			setErrorMsg("Event not found.", !isset($_REQUEST['updateAcctInfoSubmit']));
			$PAGE = "signup";
			unset($_REQUEST);
		}
	}
	else {
		setErrorMsg("No event selected.", !isset($_REQUEST['updateAcctInfoSubmit']));
		$PAGE = "signup";
		unset($_REQUEST);
	}
}
else if($PAGE == "specialEventSignupConfirm") {
	if(isset($_REQUEST['signupSpecialEventDateId'])) {
		$specialEventDate = mysqli_fetch_assoc( getSpecialEventDateById( truncate(trim($_REQUEST['signupSpecialEventDateId']), 50) ) );
		if($specialEventDate) {
			$scheduled = mysqli_fetch_assoc( getCountVolunteersForSpecialEventDateById($specialEventDate['id']) );
			$needed = ( (int)$specialEventDate['volunteersNeeded'] - (int)$scheduled['count(*)'] );		
			if($needed <= 0) {
				setErrorMsg("Event is full.", !isset($_REQUEST['updateAcctInfoSubmit']));
				$PAGE = "signup";
				unset($_REQUEST);
			}
			else if(isset($_REQUEST['updateAcctInfoSubmit'])) {
				require_once('transactions/trns_specialEventSignupConfirm.php');
			}
		}
		else {
			setErrorMsg("Event not found.", !isset($_REQUEST['updateAcctInfoSubmit']));
			$PAGE = "signup";
			unset($_REQUEST);
		}
	}
	else {
		setErrorMsg("No event selected.", !isset($_REQUEST['updateAcctInfoSubmit']));
		$PAGE = "signup";
		unset($_REQUEST);
	}
}

?>