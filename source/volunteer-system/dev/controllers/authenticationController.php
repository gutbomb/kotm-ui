<?php

if(isset($_SESSION['user']) && isset($_REQUEST['logout'])) {
	//user requested to logout
	unset($_SESSION['user']);
	unset($_REQUEST);
	$REDIRECT = "?";
}
else if(!isset($_SESSION['user']) && isset($_REQUEST['login_uname']) && isset($_REQUEST['login_password'])) {
	//user requested to login
	$_REQUEST['login_uname'] = truncate(trim($_REQUEST['login_uname']), 100);
	$acct = mysqli_fetch_assoc( getAccountByUname($_REQUEST['login_uname']) );
	if($acct && password_verify($_REQUEST['login_password'], $acct['password'])) {
		// Login succeeded.
		if(password_needs_rehash($acct['password'], PASSWORD_DEFAULT)) {
			// default PHP hashing algorithm has changed (presumably because the new one is better)
			// rehash the user's password using then new default
			$newHash = password_hash($_REQUEST['login_password'], PASSWORD_DEFAULT);
			updatePassword($_REQUEST['editAcctUname'], $newHash);
		}
		$_SESSION['user'] = $acct;
		updateLastLoginDate($_SESSION['user']['uname']);
		if($ACCESS_LEVEL[$PAGE] == "logout") {
			// If the user is on a page that can only be accessed by a logged-out user, then send them to the 
			// default page. Send them to default page via redirect to eliminate form post from browser history, 
			// so reload or back/forward won't cause issues (e.g., produce a broken iframe by trying to repeat 
			// the post).
			$REDIRECT = "?";
		}
		else {
			// User is on a page that can be accessed by a logged-in user, so keep them on this page. However,
			// do so via redirect (for the same reason described above). Forward the pre-login GETs and POSTs 
			// (stored by the login process) to the redirect to preserve them so the page to which we're 
			// returning the user (via the redirect) loads properly. Remove from forwarding GETs/POSTs any
			// extra variables that may get added in the error case below, just to clean things up.
			$getsPosts = unserialize($_REQUEST['preserveGetsPosts']);
			unset($getsPosts['showLogin']);
			unset($getsPosts['login_uname']);
			$REDIRECT = "?page=".$PAGE."&preserveGetsPosts=".urlencode(serialize($getsPosts));
		}
	}
	else {
		// Login failed.
		// Return user back to the page they were on and show an error message. Return the user via redirect
		// (for the same reason described above). Queue up the error messages in the session so they get shown 
		// only once and not repeated on back/forward navigation. Forward the pre-login GETs and POSTs (stored
		// by the login process) to the redirect to preserve them so the page to which we're returning the user
		// (via the redirect) loads properly. Add to the forwarding GETs/POSTs a flag to signal to the page
		// loading that it should show the login.
		setErrorMsg("Username or password is invalid.");
		forwardAllMsgs();
		$getsPosts = unserialize($_REQUEST['preserveGetsPosts']);
		$getsPosts['showLogin'] = '1';
		$getsPosts['login_uname'] = $_REQUEST['login_uname'];
		$REDIRECT = "?page=".$PAGE."&preserveGetsPosts=".urlencode(serialize($getsPosts));
	}
	unset($acct);
}
else if(isset($_SESSION['user'])) {
	//update user session info
	$acct = mysqli_fetch_assoc( getAccountByUname($_SESSION['user']['uname']) );
	if($acct) {
		$_SESSION['user'] = $acct;
	}
	unset($acct);
}

// When we transact a login, we want to preserve get and post requests (but not cookies) that were set when the 
// user came to the page from which they are logging in. This is because the login functionality is at the top 
// of every page in the header. Thus, the user can log in on any page, and after logging in, we want the page to 
// still be in the same state it was in before. If there are any gets or posts that you don't want to persist 
// after login, then you should clear them before the header is loaded on the page from which the user will log 
// in. I'd suggest clearing them in the transaction manager that handles them. Any gets or posts cleared before 
// the header is loaded, will not be preserved when the user logs in. Gets and posts that are not cleared prior 
// to the header loading will be serialized and stored in a hidden field (named 'preserveGetsPosts') on the page, 
// which, in fact, we are loading below to restore them.
restoreAnyGetsPosts();

?>