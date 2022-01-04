<?php

require_once('db/dao.php');
require_once('config/accessControlDefs.php');

stripGPCSlashes();

$PAGE_TYPE = 'pages';
$PAGE = $_REQUEST['page']; // ".htaccess" requires "<app/dev>/index.php", which in turn ensures this REQUEST is set.

require_once('controllers/authenticationController.php');
if(isset($REDIRECT)) {
	executeRedirect($REDIRECT);
	exit(0);
}

require_once('controllers/accessController.php');
if(isset($REDIRECT)) {
	executeRedirect($REDIRECT);
	exit(0);
}

if($PAGE_TYPE != 'systemPages') {
	require_once('controllers/transactionController.php');
	if(isset($REDIRECT)) {
		executeRedirect($REDIRECT);
		exit(0);
	}
}

// Do we have a stashed messages to restore?
if(isset($_SESSION['msgs'])) {
	setMsgs($_SESSION['msgs']);
	unset($_SESSION['msgs']);
}
if(isset($_SESSION['msgTitle'])) {
	setMsgTitle($_SESSION['msgTitle']);
	unset($_SESSION['msgTitle']);
}
if(isset($_SESSION['emsgs'])) {
	setErrorMsgs($_SESSION['emsgs']);
	unset($_SESSION['emsgs']);
}

$PAGE_LOCATION = "$PAGE_TYPE/$PAGE/content.php";
if(!is_file($PAGE_LOCATION) || !is_readable($PAGE_LOCATION)) {
	$PAGE = "systemError_pageFileNotFound";
	$PAGE_TYPE = 'systemPages';
	$PAGE_LOCATION = "$PAGE_TYPE/$PAGE/content.php";
}

require_once('common/pageTemplate.php');

?>