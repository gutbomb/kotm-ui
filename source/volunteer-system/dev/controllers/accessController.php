<?php

// identify the user's status
if(!isset($_SESSION['user'])) {
	//user NOT logged in
	$USER_STATUS = "logout";
}
else {
	//user logged in
	if($_SESSION['user']['admin']) {
		//admin user
		$USER_STATUS = "admin";
	}
	else {
		//non-admin user
		$USER_STATUS = "login";
	}
}

// determine if the user can access the requested page
if($PAGE_TYPE != "systemPages") {
	if(isset($ACCESS_LEVEL[$PAGE])) {
		$access = $ACCESS_LEVEL[$PAGE];
		if($access != "admin" && $access != "login" && $access != "logout" && $access != "any") {
			// configuration/coding error with page registry
			$PAGE_TYPE = 'systemPages';
			$PAGE = 'systemError_pageRegistry';
		}
		else if( $USER_STATUS == "logout") {
			// this case handles all access errors for logged out users
			if($access == "login" || $access == "admin") {
				$PAGE_TYPE = 'systemPages';
				$PAGE = 'userError_mustBeLoggedIn';
			}
		}
		else if($USER_STATUS == "login") {
			// this case handles all access errors for logged out users
			if($access == "admin") {
				$PAGE_TYPE = 'systemPages';
				$PAGE = 'userError_mustBeAdmin';
			}
			else if($access == "logout") {
				$PAGE_TYPE = 'systemPages';
				$PAGE = 'userError_mustBeLoggedOut';
			}
		}
		else if($USER_STATUS == "admin") {
			// this case handles all access errors for admin users
			if($access == "logout") {
				$PAGE_TYPE = 'systemPages';
				$PAGE = 'userError_mustBeLoggedOut';
			}
		}
	}
	else {
		// User requested a page that is not listed in the page registry.
		// All pages must be listed in the registry with a security status to be accessed.
		$PAGE_TYPE = 'systemPages';
		$PAGE = 'userError_pageNotFound';
	}
}

?>