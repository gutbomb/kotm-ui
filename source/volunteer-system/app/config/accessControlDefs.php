<?php

///////////////////////////////////////////////////////////////////////////////////
// ACCESS CONTROL DEFINITIONS
///////////////////////////////////////////////////////////////////////////////////
// This file is for non-system pages (those in the "pages" directory). System Pages do 
// not use access control definitions.
//
// Non-system pages must be registered here in order to be accessible. If a page is 
// not registered, then the system will return a page not found error. Register pages 
// as follows:
//		$ACCESS_LEVEL['<pageName>'] = '<accessLevel>';
//
// Access Levels are as follows:
//		'any' - anyone can view the page at any time
//		'logout' - only logged out users can view the page
//		'login'- any logged in user can view the page
//		'admin' - only logged in admins can view the page
//
// Page access is enforced by the accessController.php and is dependent on both the
// page's assigned accessLevel and the user's status (i.e. not logged in, logged in without
// admin privileges, logged in with admin privileges).
//
// Each registered page should have its own directory in the "pages" directory. with the 
// same name as that specified here in the registry. Within each page directory there 
// should be a file named content.php, which will be loaded when the page is requested. 
// The content.php file should pull in all other content needed by the page (e.g. media, 
// documents, images, other php files).

///////////////////////////////////////////////////////////////////////////////////
// Anyone can view the page at anytime ('any')
$ACCESS_LEVEL['signup'] = 'any';
$ACCESS_LEVEL['confirmSignup'] = 'any';
$ACCESS_LEVEL['confirmSignupSpecialEvent'] = 'any';
$ACCESS_LEVEL['volunteerRules'] = 'any';
$ACCESS_LEVEL['faq'] = 'any';

///////////////////////////////////////////////////////////////////////////////////
// Only logged out users can view the page ('logout')
$ACCESS_LEVEL['createAcct'] = 'logout';
$ACCESS_LEVEL['createAcctSuccess'] = 'logout';
$ACCESS_LEVEL['passwdReset'] = 'logout';
$ACCESS_LEVEL['passwdResetSuccess'] = 'logout';

///////////////////////////////////////////////////////////////////////////////////
// Any logged in user can view the page ('login')
$ACCESS_LEVEL['manageAcct'] = 'login';
$ACCESS_LEVEL['changeUsername'] = 'login';
$ACCESS_LEVEL['changePassword'] = 'login';
$ACCESS_LEVEL['updateAcctInfo'] = 'login';
$ACCESS_LEVEL['signupConfirm'] = 'login';
$ACCESS_LEVEL['specialEventSignupConfirm'] = 'login';
$ACCESS_LEVEL['myVolunteerSchedule'] = 'login';

///////////////////////////////////////////////////////////////////////////////////
// Only logged in admins can view the page ('admin')
$ACCESS_LEVEL['adminServices'] = 'admin';
$ACCESS_LEVEL['adminManageAccts'] = 'admin';
$ACCESS_LEVEL['adminManageAcct'] = 'admin';
$ACCESS_LEVEL['adminManageVolunteerDates'] = 'admin';
$ACCESS_LEVEL['adminManageVolunteerDate'] = 'admin';
$ACCESS_LEVEL['adminManageSpecialEventDate'] = 'admin';
$ACCESS_LEVEL['downloadVolunteerDateSchedule'] = 'admin';
$ACCESS_LEVEL['adminOrgParticipationReports'] = 'admin';
$ACCESS_LEVEL['downloadOrgParticipationReport'] = 'admin';

?>