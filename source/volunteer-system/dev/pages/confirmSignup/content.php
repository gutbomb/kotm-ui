<?php

if(!isErrors()) {
	printPageMessagesAndTitle("Sign-up Confirmed!");
	$vDate = mysqli_fetch_assoc( getVolunteerDateById($_REQUEST['vDateId']) );
	$formattedDate = formatDateAsText_monthDayYear($vDate['date']);
	?>
	<div class="text-indent">
		<h2 style="color:#737373;"><?php echo htmlentities($vDate['type']); ?></h2>
		<table class="invisible-table text-indent" style="width:593px; table-layout:fixed; word-wrap:break-word;">
			<col style="width:75px;"/>
			<col style="width:518px;"/>
			<tr>
				<td style="vertical-align:top;">Date:</td>
				<td><?php echo htmlentities($formattedDate); ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Time:</td>
				<td><?php echo htmlentities(strftime("%-l:%M %P", strtotime($vDate['startTime'])))." - ".htmlentities(strftime("%-l:%M %P", strtotime($vDate['endTime']))); ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Location:</td>
				<td><?php echo htmlentities($vDate['location']); ?> (<?php echo getLocationDirectionsAnchorLink($vDate['location'], "directions"); ?>)</td>
			</tr>
			<tr>
				<td style="vertical-align:top; padding-top:6px;">Address:</td>
				<td style="padding-top:6px;"><?php echo getLocationAddress($vDate['location'], "<br/>"); ?></td>
			</tr>
		</table>
		<p style="margin-bottom:0px;">
			Thank you! :)
		</p>
	</div>
	<?php
}
else {
	//printPageMessagesAndTitle("Error Confirming Sign-up");
	printPageTitle("Error Confirming Sign-up");
	?>
	<div class="text-indent">
		<p style="margin-bottom:0px;">
			Unexpected error. Could not confirm sign-up. Please try again.
		</p>
	</div>
	<?php
}

?>