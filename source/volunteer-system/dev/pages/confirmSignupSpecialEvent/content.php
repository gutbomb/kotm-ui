<?php

if(!isErrors()) {
	printPageMessagesAndTitle("Sign-up Confirmed!");
	$vDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['vDateId']) );
	$formattedDate = formatDateAsText_monthDayYear($vDate['date']);
	if($vDate['hasStartEndTime'] == '0') {
		$formattedTime = "Anytime";
	}
	else {
		$formattedTime = htmlentities(strftime("%-l:%M %P", strtotime($vDate['startTime'])))." - ".htmlentities(strftime("%-l:%M %P", strtotime($vDate['endTime'])));
	}
	?>
	<div class="text-indent">
		<h2 style="color:#737373;">Special Event</h2>
		<table class="invisible-table text-indent" style="width:593px; table-layout:fixed; word-wrap:break-word;">
			<col style="width:90px;"/>
			<col style="width:503px;"/>
			<tr>
				<td style="vertical-align:top;">Date:</td>
				<td><?php echo htmlentities($formattedDate); ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Time:</td>
				<td><?php echo $formattedTime; ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Location:</td>
				<td><?php echo htmlentities($vDate['location']); ?> (<a href="?page=signup&sedId=<?php echo $vDate['id']; ?>">directions</a>)</td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Description:</td>
				<td><?php echo htmlentities($vDate['shortDescription']); ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Details:</td>
				<td><?php echo processMarkupForHtmlDisplay($vDate['longDescription']); ?></td>
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