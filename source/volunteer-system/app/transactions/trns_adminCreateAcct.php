<?php
	if ($_REQUEST['isSpecialEventVolunteerDate'] === '1') {
		$redirectParam = 'manageSpecialEventDateId';
	} else {
		$redirectParam = 'manageVDateId';
	}
	if (emailExists($_REQUEST['email'])) {
		$acct = mysqli_fetch_assoc(getAccountByEmail($_REQUEST['email']));
		$bypassCreation = true;
	} else {
		$bypassCreation = false;
	}

	function randomPassword() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

	function addUserToEvent($username) {
		$newVolunteer['uname'] = $username;
		$newVolunteer['volunteerDateId'] = $_REQUEST['manageVDateId'];
		$newVolunteer['signupDate'] = date('Y-m-d H:i:s');
		if($_REQUEST['isSpecialEventVolunteerDate'] === '1') {
			if(!createSpecialEventVolunteer($newVolunteer)) {
				setErrorMsg("An error occurred while adding volunteer. Please try again!");
			}
		} else {
			if(!createVolunteer($newVolunteer)) {
				setErrorMsg("An error occurred while adding volunteer. Please try again!");
			}
		}
	}

	if(!$bypassCreation) {
		$password = randomPassword();

		$_REQUEST['firstname'] = truncate(trim($_REQUEST['firstname']), 50);
		$_REQUEST['lastname'] = truncate(trim($_REQUEST['lastname']), 50);
		$_REQUEST['email'] = truncate(trim($_REQUEST['email']), 50);
		$emailArr = explode('@', $_REQUEST['email']);
		if(unameExists($emailArr[0])) {
			$username = $emailArr[0].rand(100,999);
		} else {
			$username = $emailArr[0];
		}

		// Validate the input //////////////////////////////////////////////////////////////////////////////////////

		if( !isReqSet("firstname") ||
			!isReqSet("lastname") ||
			!isReqSet("email")) {

				setErrorMsg("Some required fields are blank.");
		}

		if(isReqSet("email") && !isValidEmailAddress($_REQUEST['email'])) {
			setErrorMsg("Invalid email address.");
		}

		// Create the account if no errors ///////////////////////////////////////////////////////////////////////////

		if(!isErrors()) {
			$emailArr = explode('@', $_REQUEST['email']);
			$newAcct['uname'] = $username;
			$newAcct['password'] = password_hash($password, PASSWORD_DEFAULT);
			$newAcct['firstname'] = $_REQUEST['firstname'];
			$newAcct['lastname'] = $_REQUEST['lastname'];
			$newAcct['birthdate'] = date('Y-m-d H:i:s');
			$newAcct['email'] = $_REQUEST['email'];
			$newAcct['address1'] = '';
			$newAcct['address2'] = '';
			$newAcct['city'] = '';
			$newAcct['state'] = '';
			$newAcct['zip'] = '';
			$newAcct['phone1'] = '';
			$newAcct['phone2'] = '';
			$newAcct['emergencyName'] = '';
			$newAcct['emergencyRelationship'] = '';
			$newAcct['emergencyPhone'] = '';
			$newAcct['student'] = '0';
			$newAcct['school'] = '';

			if(createAccount($newAcct)) {
				require_once('emails/email_adminCreateAcctSuccess.php');
				sendAdminCreateAcctSuccessEmail($newAcct, $password);
				addUserToEvent($username);
				// Redirect to eliminate form post from browser history, so reload or back/forward won't cause issues 
				// (e.g., produce a broken iframe by trying to repeat the post).
				$REDIRECT = "?page=" . $_REQUEST['page'] . "&" . $redirectParam . "=" . $_REQUEST['manageVDateId'];
				unset($_REQUEST);
			} else {
				$link = getVolunteersAdminEmailLink("color:#05aea8;");
				$link = ($link == "") ? "" : " (".$link.")";
				setErrorMsg($username . "An unknown error occurred. Please try again. If the problem persists, let us know".$link."!");
			}
		}
		else {
			setErrorMsg("Correct any errors");
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
			unset($_REQUEST['adminCreateAcctSubmit']);
			$REDIRECT = "?page=" . $_REQUEST['page'] . "&" . $redirectParam . "=" . $_REQUEST['manageVDateId'];
			unset($_REQUEST);
		}
	} else {
		addUserToEvent($acct['uname']);
		$REDIRECT = "?page=" . $_REQUEST['page'] . "&" . $redirectParam . "=" . $_REQUEST['manageVDateId'];
		unset($_REQUEST);
	}
?>