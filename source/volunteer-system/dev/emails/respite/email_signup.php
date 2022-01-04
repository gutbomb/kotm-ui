<?php

require('emails/getSigInfo_plainText.php');

function sendSignupEmail($acct, $vDate) {
	
	$volunteersAdminInfo = mysqli_fetch_assoc( getVolunteersAdminInfo() );
	
	//$timeStartEarly = strftime( "%-l:%M %P", strtotime($vDate['startTime'])-900 );
	$timeStart = strftime("%-l:%M %P", strtotime($vDate['startTime']));
	$timeEnd = strftime("%-l:%M %P", strtotime($vDate['endTime']));
	
	$to = $acct['email'];
	$from = "From: ".$volunteersAdminInfo['title']." <".$volunteersAdminInfo['email'].">";
	$subject = "Kids On The Move - Scheduled to Volunteer";
	
	$email_body = "Dear ".$acct['firstname'].",\n";
	$email_body .= "\n";
	$email_body .= "Thank you for signing up to volunteer in our Respite program! It's a wonderful experience caring for a child with special needs. The parents, who get a break from 24/7 caregiving, are so grateful for your service!\n";
	$email_body .= "\n";
	$email_body .= "Location: ".$vDate['location']."\n";
	$email_body .= "Date: ".formatDateAsText_weekdayMonthDay($vDate['date'])."\n";
	$email_body .= "Time: ".$timeStart." - ".$timeEnd."\n";
	$email_body .= "\n";
	$email_body .= "ADDRESS:\n".getLocationAddress($vDate['location'])."\n";
	$email_body .= "\n";
	$email_body .= "DIRECTIONS: ".getLocationDirectionsURL($vDate['location'])."\n";
	$email_body .= "\n";
	$email_body .= "CANCELLATIONS: We have paired you one-on-one with a special needs child. These children require constant supervision by a caring and responsible adult. If you cancel last minute, this child will likely not be able to attend respite. If you need to cancel, please do so as early as possible, or provide a substitute. Last minute walk-in substitutes are welcome!\n";
	$email_body .= "\n";
	$email_body .= "DRESS/GROOMING: All volunteers should appear neat, clean and modest when volunteering at Kids On The Move and when representing Kids On The Move to the public. Clothing should be clean, in good repair, well-fitting and modest. Modesty is defined as clothing that is not revealing, such as cleavage, a person's chest, back, stomach or underwear. Sleeveless shirts, strapless, backless, tank tops, halter tops, cutoffs, are not permitted. Shirtless and shoeless is prohibited. Dresses, skirts, and shorts must be knee length or longer. Extremes in make-up, clothing, hairstyles, jewelry and body piercing are discouraged.\n";
	$email_body .= "\n";
	$email_body .= "*** Volunteers that do not meet the dress and grooming standards will be asked by Kids On The Move personnel to correct the problem. If not corrected, the volunteer may be turned away.\n";
	$email_body .= "\n";
	$email_body .= "For any questions, e-mail us at ".$volunteersAdminInfo['email']." or call at ".$volunteersAdminInfo['phone'].".\n";
	$email_body .= "\n";
	$email_body .= getSigInfo_plainText();
	
	return mail($to, $subject, $email_body, $from);
	
}

?>