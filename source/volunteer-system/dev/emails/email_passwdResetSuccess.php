<?php

require('emails/getSigInfo_plainText.php');

function sendPasswdResetEmail($acct, $tmpPasswd) {

	$volunteersAdminInfo = mysqli_fetch_assoc( getVolunteersAdminInfo() );
	
	$to = $acct['email'];
	$from = "From: ".$volunteersAdminInfo['title']." <".$volunteersAdminInfo['email'].">";
	$subject = "Kids On The Move - Password Reset";
	
	$email_body = "Dear ".$acct['firstname'].",\n";
	$email_body .= "\n";
	$email_body .= "We received a request to reset your Kids On The Move password. Your new temporary password is below. Please click (or copy/paste into your browser) the link provided in this email. Then follow the on-screen instructions to log in and change your password.\n";
	$email_body .= "\n";
	$email_body .= "Temporary Password: ".$tmpPasswd."\n";
	$email_body .= "\n";
	$email_body .= "Login Link: https://kotm.org/volunteer-system/app/?page=changePassword&sess=null\n";
	$email_body .= "\n";
	$email_body .= "If you did not request this change, please contact us at ".$volunteersAdminInfo['phone'].".\n";
	$email_body .= "\n";
	$email_body .= getSigInfo_plainText();
	
	return mail($to, $subject, $email_body, $from);
	
}

?>