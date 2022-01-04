<?php

// All dates, times, and timestamps are INPUT to the DAO (dao.php) in Mountain Time and OUTPUT from the DAO in Mountain Time.
// Throughout the PHP code we handle everything in Mountain Time, and the entire app is in Mountain Time, irrespective of the user's locale.
// See dao.php for details about timezone handling at the DB level.
date_default_timezone_set('America/Denver');

session_start();
require_once('utils/util.php');

// Used for password reset. Passed by the email login link.
// Used to ensure that the link won't take the user accidentally 
// to the change password page for another user. So will always
// force logout by clearing any user from the ssession.
if( isset($_REQUEST['sess']) && $_REQUEST['sess'] == "null" ) {
	unset($_SESSION['user']);
	unset($_REQUEST['sess']);
}

// Determines which data source to use and how to format the confirmation email links.
$STAGE = "PROD"; // default = production

if($_SERVER["HTTPS"] != "on") {
	// Force HTTPS
	executeRedirect("https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
}
else if(!isset($_REQUEST['page']) || $_REQUEST['page'] == "") {
	// Force $_REQUEST['page'] to be set
	executeRedirect("?page=signup"); // default page
}
else {
	if(dirname($_SERVER['PHP_SELF']) == "/volunteer-system/dev") {
		$STAGE = "DEV"; // development
	}
	require_once('controllers/mainController.php');
}

function executeRedirect($redirectURL) {
	?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
		<head>
			<title>Redirect</title>
			<meta http-equiv="refresh" content="0; url=<?php echo $redirectURL; ?>">
		</head>
		<body>
		</body>
	</html>
	<?php
}

?>
