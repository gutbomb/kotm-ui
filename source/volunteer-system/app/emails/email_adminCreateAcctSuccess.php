<?php

require('emails/getSigInfo_plainText.php');

function sendAdminCreateAcctSuccessEmail($newAcct, $password) {

	$volunteersAdminInfo = mysqli_fetch_assoc( getVolunteersAdminInfo() );

	$to = $newAcct['email'];
	$from = "From: ".$volunteersAdminInfo['title']." <".$volunteersAdminInfo['email'].">";
	$subject = "Kids On The Move - Account Created";

	$email_body = "Dear ".$newAcct['firstname'].",\n";
	$email_body .= "\n";
	$email_body .= "Welcome to the Kids On The Move family! Our program could not continue without the caring support of individuals like yourself!\n";
	$email_body .= "\n";
	$email_body .= "Please complete the volunteer account we created for you at www.kotm.org/volunteer-portal . After signing in, click \"Manage Account\" and then \"Update Account Information\". Please fill out this page! It only takes a moment.";
	$email_body .= "\n";
	$email_body .= "To sign in to your account, go to: https://kotm.org/volunteer-portal\n";
	$email_body .= "\n";
	$email_body .= "Your username is: ".$newAcct['uname']."\n";
	$email_body .= "Your password is: ".$password."\n";
	$email_body .= "\n";
	$email_body .= "With your new account, you can access:\n";
	$email_body .= "    • Your contact information.\n";
	$email_body .= "    • Dates you've signed up to volunteer.\n";
	$email_body .= "    • Your volunteer history.\n";
	$email_body .= "    • Change your username and/or password.\n";
	$email_body .= "\n";
	$email_body .= "By signing up to volunteer, either through a KOTM representative or by yourself, you are agreeing not to hold Kids On The Move liable for any acts, omissions, injuries, or damages, and you agree to accept any and all risks associated with volunteering for Kids On The Move, including all risks associated with COVID-19.";
	$email_body .= "\n";
	$email_body .= "If you have any questions or comments, e-mail us at ".$volunteersAdminInfo['email']." or call at ".$volunteersAdminInfo['phone'].". We welcome your feedback!\n";
	$email_body .= "\n";
	$email_body .= getSigInfo_plainText();
	
	return mail($to, $subject, $email_body, $from);

}

?>