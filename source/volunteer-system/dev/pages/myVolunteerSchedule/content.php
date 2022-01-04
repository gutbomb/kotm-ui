<?php
printPageMessagesAndTitle("My Volunteer Schedule");
?>

<div class="text-indent" style="padding-bottom:14px;">
	<p style="font-weight:bold;">
		* Please avoid last minute cancellations. <u>Substitutes welcome</u>. Thanks!
	</p>
	<p style="margin-bottom:0px;">
		If you have any questions, please email us at <?php echo getVolunteersAdminEmailLink(); ?> or call <?php echo getVolunteersAdminPhone(); ?>.
	</p>
</div>

<?php
function printVDates($vDates, $title, $areCurrent) {
	?>
	<table class="text-indent" style="border-collapse:collapse; margin-top:5px; margin-bottom:10px; width:618px;">
		<tr>
			<td><h2 style="margin:0px;"><?php echo htmlentities($title); ?></h2></td>
			<?php
			if($areCurrent) {
				?>
				<td style="text-align:right;"><a id="show-history-anchor" name="show-history-anchor" href="javascript:toggleHistory()">Show History</a></td>
				<?php
			}
			?>
		</tr>
	</table>
	<table class="myVolunteerSchedule table-indent" style="table-layout:fixed; word-wrap:break-word;">
		<?php
		if(count($vDates) < 1) {
			$noneText = "No events scheduled <a href='?page=signup'>(sign up to volunteer)</a>";
			if(!$areCurrent) {
				$noneText = "None";
			}
			?>
			<tr>
				<td<?php if(!$areCurrent) echo " style='text-align:left;'"; ?>><?php echo $noneText; ?></td>
			</tr>
			<?php
		}
		else {
			?>
			<col style="width:156px;"/>
			<col style="width:104px;"/>
			<col style="width:140px;"/>
			<col style="width:117px;"/>
			<col style="width:100px;"/>
			<tr>
				<th>Date/Time<br/><span style="font-weight:normal;">(Mountain Time)</span></th>
				<th>Event</th>
				<th>Location<?php if($areCurrent){ echo "<br/><span style='font-weight:normal;'>(tap for directions)</span>"; } ?></th>
				<th>Sign-up Date</th>
				<th><?php if($areCurrent){echo "Cancel";}else{echo "Showed?";} ?></th>
			</tr>
			<?php
			foreach($vDates as $vDate) {
				$isSpecial = isset($vDate['hasStartEndTime']);
				$time = "Anytime";
				if( !$isSpecial || $vDate['hasStartEndTime'] != "0" ) {
					$startTime = strftime("%-l:%M %P", strtotime($vDate['startTime']));
					$endTime = strftime("%-l:%M %P", strtotime($vDate['endTime']));
					$time = htmlentities($startTime)." - ".htmlentities($endTime);
				}
			
				$formattedDateVDate = formatDateAsText_monthDayYear($vDate['date']);
				$formattedDateSignupDate = formatDateAsText_monthDayYear($vDate['signupDate']);
				
				if($areCurrent) {
					if($isSpecial) {
						$locationLink = "<a href='?page=signup&sedId=".$vDate['id']."'>".htmlentities($vDate['location'])."</a>";
					}
					else {
						$locationLink = getLocationDirectionsAnchorLink($vDate['location']);
					}
				}
				else {
					$locationLink = htmlentities($vDate['location']);
				}
				
				echo "<tr>";
				echo "	<td>".htmlentities($formattedDateVDate)."<br/>".$time."</td>";
				echo "	<td>".htmlentities($vDate['type'])."</td>";
				echo "	<td>".$locationLink."</td>";
				echo "	<td>".htmlentities($formattedDateSignupDate)."</td>";
				if($areCurrent) {
					if($isSpecial) {
						echo "	<td><a href=\"javascript:deleteSpecialEventSignup('".addslashes($vDate['volunteerDateId'])."', '".addslashes($formattedDateVDate)."', '".addslashes($time)."', '".addslashes($vDate['location'])."');\" class='deleteImgLink'><img src='images/pageElements/delete.svg'/></a></td>";
					}
					else {
						echo "	<td><a href=\"javascript:deleteVolunteerSignup('".addslashes($vDate['volunteerDateId'])."', '".addslashes($formattedDateVDate)."', '".addslashes($time)."', '".addslashes($vDate['location'])."', '".addslashes($vDate['type'])."');\" class='deleteImgLink'><img src='images/pageElements/delete.svg'/></a></td>";
					}
				}
				else {
					echo "	<td>";
					if($vDate['showed'] == '0') {
						echo "No";
					}
					else if($vDate['showed'] == '1') {
						echo "Yes";
					}
					else {
						echo "Not Recorded";
					}
					echo "	</td>";
				}
				echo "</tr>";
			}
		}
		?>
	</table>
	<?php
}

$vDatesDbResult = getVolunteersVDatesByUnameBefore($_SESSION['user']['uname'], getMySqlDate(time()));
$vDatesPast = array();
while($vDate = mysqli_fetch_assoc($vDatesDbResult)) {
	$vDatesPast[] = $vDate;
}

$vDatesDbResult = getVolunteersVDatesByUnameAfter($_SESSION['user']['uname'], getMySqlDate(time()));
$vDatesCurrent = array();
while($vDate = mysqli_fetch_assoc($vDatesDbResult)) {
	$vDatesCurrent[] = $vDate;
}

$specialEventDatesDbResult = getVolunteersSpecialEventDatesByUnameBefore($_SESSION['user']['uname'], getMySqlDate(time()));
$specialEventDatesPast = array();
while($specialEventDate = mysqli_fetch_assoc($specialEventDatesDbResult))
{
	$specialEventDate['type'] = "Special Event";
	$specialEventDatesPast[] = $specialEventDate;
}

$specialEventDatesDbResult = getVolunteersSpecialEventDatesByUnameAfter($_SESSION['user']['uname'], getMySqlDate(time()));
$specialEventDatesCurrent = array();
while($specialEventDate = mysqli_fetch_assoc($specialEventDatesDbResult))
{
	$specialEventDate['type'] = "Special Event";
	$specialEventDatesCurrent[] = $specialEventDate;
}

$vDatesCurrentAll = mergeDates($vDatesCurrent, $specialEventDatesCurrent, true);
$vDatesPastAll = mergeDates($vDatesPast, $specialEventDatesPast, false);

printVDates($vDatesCurrentAll, "Current Schedule", true);
?>

<div id="event-history" name="event-history" style="display:none;">
	<br/>
	<?php
	printVDates($vDatesPastAll, "History", false);
	?>
</div>

<form method="get" action="" name="volunteerDeleteSignupForm" id="volunteerDeleteSignupForm">
	<input type="hidden" name="page" id="page" value="myVolunteerSchedule">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<input type="hidden" name="deleteSignup" id="deleteSignup" value="false"/>
	<input type="hidden" name="deleteSignupVDateId" id="deleteSignupVDateId" value=""/>
</form>
<form method="get" action="" name="volunteerDeleteSpecialEventSignupForm" id="volunteerDeleteSpecialEventSignupForm">
	<input type="hidden" name="page" id="page" value="myVolunteerSchedule">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<input type="hidden" name="deleteSpecialEventSignup" id="deleteSpecialEventSignup" value="false"/>
	<input type="hidden" name="deleteSignupSpecialEventDateId" id="deleteSignupSpecialEventDateId" value=""/>
</form>
<script type="text/javascript">
function toggleHistory() {
	var isHiding = true;
	var frameHeight = parent.getIframeHeight();
	var elementHeight = $('#event-history').height();
	var history = document.getElementById('event-history');
	if(history.style.display == "none") {
		isHiding = false;
	}
	$('#event-history').slideToggle({
		duration: 400,
		progress: function(animation, progress, remainingMs){
			if(isHiding) {
				parent.setIframeHeight( frameHeight - (elementHeight*progress) );
			}
			else {
				parent.setIframeHeight( frameHeight + (elementHeight*progress) );
			}
		},
		complete: function(){
			parent.resizeIframe();
			var historyAnchor = document.getElementById('show-history-anchor');
			if(history.style.display == "none") {
				historyAnchor.innerHTML = "Show History";
			}
			else {
				historyAnchor.innerHTML = "Hide History";
			}
		}
	});
}
function deleteVolunteerSignup(deleteSignupVDateId, date, time, location, type) {
	parent.popupConfirm("<p>Delete sign-up?</p><p>"+type+" - "+location+"<br/>"+date+"<br/>"+time+"</p>").then(function(result){
		if( result === "OK" ) {
			document.getElementById('deleteSignup').value = "true";
			document.getElementById('deleteSignupVDateId').value = deleteSignupVDateId;
			document.getElementById('volunteerDeleteSignupForm').submit();
		}
	});
}
function deleteSpecialEventSignup(deleteSignupSpecialEventDateId, date, time, location) {
	parent.popupConfirm("<p>Delete sign-up?</p><p>Special Event - "+location+"<br/>"+date+"<br/>"+time).then(function(result){
		if( result === "OK" ) {
			document.getElementById('deleteSpecialEventSignup').value = "true";
			document.getElementById('deleteSignupSpecialEventDateId').value = deleteSignupSpecialEventDateId;
			document.getElementById('volunteerDeleteSpecialEventSignupForm').submit();
		}
	});
}
</script>