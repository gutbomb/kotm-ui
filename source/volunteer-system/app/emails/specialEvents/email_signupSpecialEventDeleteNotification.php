<?php

function sendSpecialEventSignupDeleteNotificationEmail($acct, $vDate, $numDaysWarning) {
	
	global $STAGE;
	
	if($STAGE == "DEV") {
		$info = mysqli_fetch_assoc( getWebsiteAdminInfo() );
		$stage = "/dev/";
	}
	else {
		$info = mysqli_fetch_assoc( getVolunteersAdminInfo() );
		$stage = "/app/";
	}
	
	$acctURL = "https://".$_SERVER["SERVER_NAME"]."/volunteer-system$stage?page=adminManageAcct&id=".$acct['uname'];
	$vDateURL = "https://".$_SERVER["SERVER_NAME"]."/volunteer-system$stage?page=adminManageSpecialEventDate&id=".$vDate['id'];
	$infoURL = "https://".$_SERVER["SERVER_NAME"]."/volunteer-system$stage?page=adminServices&jumpId=adminSetWebsiteAdminForm";
	
	$to = $info['email'];
	$from = "From: no-reply@kotm.org";
	$subject = "Kids On The Move - Special Event Cancellation for ".formatDateAsNumeric_monthDayYear($vDate['date']);
	
	$email_body = "----------------(automatically generated message)----------------\n";
	$email_body .= "\n";
	$email_body .= "A volunteer has cancelled his/her sign-up for a Special Event.\n";
	$email_body .= "\n";
	$email_body .= $acct['firstname']." ".$acct['lastname']." (id: ".$acct['uname'].") has cancelled his/her appointment to volunteer on ".formatDateAsText_weekdayMonthDay($vDate['date']).".\n";
	$email_body .= "\n";
	$email_body .= "To review the updated schedule for ".formatDateAsNumeric_monthDayYear($vDate['date'])." visit:\n";
	$email_body .= $vDateURL."\n";
	$email_body .= "\n";
	$email_body .= "To view the volunteer's account visit:\n";
	$email_body .= $acctURL."\n";
	$email_body .= "\n";
	$email_body .= "This email has been sent because the cancellation was within ".$numDaysWarning." days of the event. If you have questions about how or why this email message was generated, contact the app developer:\n";
	$email_body .= $infoURL."\n";
	$email_body .= "\n";
	$email_body .= "----------------(automatically generated message)----------------\n";
	
	return mail($to, $subject, $email_body, $from);
	
}

?>