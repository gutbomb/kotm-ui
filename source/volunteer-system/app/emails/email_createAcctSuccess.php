<?php

require('emails/getSigInfo_plainText.php');

function sendCreateAcctSuccessEmail($newAcct) {

	$volunteersAdminInfo = mysqli_fetch_assoc( getVolunteersAdminInfo() );

	$to = $newAcct['email'];
	$from = "From: ".$volunteersAdminInfo['title']." <".$volunteersAdminInfo['email'].">";
	$subject = "Kids On The Move - Account Created";

	$email_body = "Dear ".$newAcct['firstname'].",\n";
	$email_body .= "\n";
	$email_body .= "Welcome to the Kids On The Move family! Our program could not continue without the caring support of individuals like yourself!\n";
	$email_body .= "\n";
	$email_body .= "With your new account, you can access:\n";
	$email_body .= "    • Your contact information.\n";
	$email_body .= "    • Dates you've signed up to volunteer.\n";
	$email_body .= "    • Your volunteer history.\n";
	$email_body .= "\n";
	$email_body .= "To access your account, go to: https://kotm.org/volunteer-portal\n";
	$email_body .= "\n";
	$email_body .= "If you have any questions or comments, e-mail us at ".$volunteersAdminInfo['email']." or call at ".$volunteersAdminInfo['phone'].". We welcome your feedback!\n";
	$email_body .= "\n";
	$email_body .= getSigInfo_plainText();
	
	return mail($to, $subject, $email_body, $from);

}

?>