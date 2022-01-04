<?php

printPageMessagesAndTitle("Sign Up to Volunteer");

$sortby = "date";
if( isset($_REQUEST['sortby']) ) {
	if($_REQUEST['sortby'] == "type") {
		$sortby = "type";
	}
	else if($_REQUEST['sortby'] == "location") {
		$sortby = "location";
	}
}

$nonSpecialEventsDbResult = getAllNonSpecialEventDatesSince( getMySqlDate(time()), $sortby );
$nonSpecialEvents = array();
while($event = mysqli_fetch_assoc($nonSpecialEventsDbResult)) {
	$event['signupDate'] = ""; // this is just so mergeDates() below will work, ignored otherwise
	$nonSpecialEvents[] = $event;
}

$specialEventsDbResult = getAllSpecialEventDatesSince( getMySqlDate(time()), $sortby );
$specialEvents = array();
while($event = mysqli_fetch_assoc($specialEventsDbResult)) {
	$event['signupDate'] = ""; // this is just so mergeDates() below will work, ignored otherwise
	$event['type'] = "Special Event";
	$specialEvents[] = $event;
}

$allEvents = mergeDates($nonSpecialEvents, $specialEvents, true, $sortby);

function getNeeded($event, $isSpecialEvent=false) {
	if($isSpecialEvent) {
		$scheduled = mysqli_fetch_assoc( getCountVolunteersForSpecialEventDateById($event['id']) );
	}
	else {
		$scheduled = mysqli_fetch_assoc( getCountVolunteersForVolunteerDateById($event['id']) );
	}
	return $event['volunteersNeeded'] - $scheduled['count(*)'];
}
?>

<table class="text-indent" style="border-collapse:collapse; margin-top:5px; margin-bottom:10px; width:618px;">
	<tr>
		<td><h2 style="margin:0px;">What is Childcare? <span class="signup-min-age-note">(minimum age: <?php echo $CHILDCARE_MIN_AGE; ?>)</span></h2></td>
	</tr>
</table>
<div class="text-indent" style="padding-bottom:14px;">
	<p style="margin-bottom:0px;">
		One of the ways we empower families is by providing educational workshops for parents of children with special needs. When you volunteer for a childcare event, your job is simple &ndash; you get to play with and befriend one or more children while their parents attend training classes!
	</p>
</div>

<table class="text-indent" style="border-collapse:collapse; margin-top:5px; margin-bottom:10px; width:618px;">
	<tr>
		<td><h2 style="margin:0px;">What is Respite? <span class="signup-min-age-note">(minimum age: <?php echo $RESPITE_MIN_AGE; ?>)</span></h2></td>
	</tr>
</table>
<div class="text-indent" style="padding-bottom:14px;">
	<p style="margin-bottom:0px;">
		Respite provides a short period of rest or relief for the parents (or other caregivers) of a child with special needs. When you volunteer for a respite event, you play with and befriend a single child, one-on-one. Should you need anything, a nurse is always on duty to assist.
	</p>
</div>

<table class="text-indent" style="border-collapse:collapse; margin-top:5px; margin-bottom:10px; width:618px;">
	<tr>
		<td><h2 style="margin:0px;">Other Ways to Volunteer</h2></td>
	</tr>
</table>
<div class="text-indent" style="padding-bottom:14px;">
	<p style="margin-bottom:0px;">
		Our Early Head Start Preschool provides quality childcare for low-income families every weekday from 8 AM to 4 PM. We need classroom volunteers to assist teachers and play with the students. These volunteers are required to have a background check, TB test, and some training, and must commit to at least 15 hours in the classroom. If you are interested in helping in the classroom, contact the volunteer coordinator at <a href="mailto:volunteer@kotm.org">volunteer@kotm.org</a>. Volunteers can also email the volunteer coordinator to arrange a time to clean, do yard work, or discuss group volunteering.
	</p>
</div>

<table class="text-indent" style="border-collapse:collapse; margin-top:5px; margin-bottom:10px; width:618px;">
	<tr>
		<td><h2 style="margin:0px;">COVID-19 Precautions</h2></td>
	</tr>
</table>
<div class="text-indent" style="padding-bottom:14px;">
	<p style="margin-bottom:0px;">
		Due to the COVID-19 virus outbreak and in support of the guidelines by government health organizations at multiple levels, Kids On The Move is adopting several practices to prevent the possible spread of COVID-19 within our community. Volunteers will be required to:
	</p>
	<ul>
		<li>Agree to a COVID-19 consent form, accepting the risk of exposure while choosing to volunteer (part of online sign-up)
		<li>Complete an electronic health questionnaire concerning symptoms and possible exposure</li>
		<li>Wear a mask at all times (please bring your own if possible; we do have masks available)</li>
		<li>Have their temperature taken upon entrance to KOTM</li>
		<li>Sanitize their hands every 30 minutes (sanitizer provided)</li>
		<li>Help sanitize the location and toys after the event (let staff know if you can’t for any reason)</li>
		<li>Social distance as much as possible</li>
	</ul>
	<p style="margin-bottom:0px;">
		Lastly, if you are experiencing any type of illness or COVID-19 symptoms, please do not volunteer. With your help, Kids On The Move can continue to safely serve high-risk families and children. Thank you!
	</p>
</div>

<table class="text-indent" style="border-collapse:collapse; margin-top:5px; margin-bottom:10px; width:618px;">
	<tr>
		<td><h2 style="margin:0px;">Volunteer Opportunities</h2></td>
	</tr>
</table>
<table class="myVolunteerSchedule table-indent" style="table-layout:fixed; word-wrap:break-word;">
	<?php
	if(count($allEvents) < 1) {
		?>
		<tr>
			<td style="text-align:left;">None at the moment. Check back later!</td>
		</tr>
		<?php
	}
	else {
		$addtlAttributes_date = ($sortby == 'date') ? "" : " signup-table-sort-header-clickable\" onclick=\"sortSignupTable('date')";
		$addtlAttributes_event = ($sortby == 'type') ? "" : " signup-table-sort-header-clickable\" onclick=\"sortSignupTable('type')";
		$addtlAttributes_location = ($sortby == 'location') ? "" : " signup-table-sort-header-clickable\" onclick=\"sortSignupTable('location')";
		?>
		<col style="width:151px;"/>
		<col style="width:105px;"/>
		<col style="width:150px;"/>
		<col style="width:101px;"/>
		<col style="width:110px;"/>
		<tr>
			<th><span class="signup-table-sort-header">Date/Time<span class="fa fa-sort-down signup-table-sort-header-icon<?php echo $addtlAttributes_date; ?>"></span></span><br/><span style="font-weight:normal; position:relative; top:-5px;">(Mountain Time)</span></th>
			<th><span class="signup-table-sort-header">Event<span class="fa fa-sort-down signup-table-sort-header-icon<?php echo $addtlAttributes_event; ?>"></span></span></th>
			<th><span class="signup-table-sort-header">Location<span class="fa fa-sort-down signup-table-sort-header-icon<?php echo $addtlAttributes_location; ?>"></span></span><br/><span style="font-weight:normal; position:relative; top:-5px;">(tap for directions)</span></th>
			<th>Volunteers Needed</th>
			<th></th>
		</tr>
		<?php
		foreach($allEvents as $event) {
			$isSpecial = isset($event['hasStartEndTime']);
			$alreadySignedUp = false;
			if( isset($_SESSION['user']['uname']) ) {
				// user is logged in, so check if they've already signed up
				if($isSpecial) {
					$alreadySignedUp = specialEventVolunteerExists($_SESSION['user']['uname'], $event['id']);
				}
				else {
					$alreadySignedUp = volunteerExists($_SESSION['user']['uname'], $event['id']);
				}
			}
			
			// compose date, time
			$date = formatDateAsText_monthDayYear($event['date']);
			$time = "Anytime";
			if( !$isSpecial || $event['hasStartEndTime'] != "0" ) {
				$startTime = strftime("%-l:%M %P", strtotime($event['startTime']));
				$endTime = strftime("%-l:%M %P", strtotime($event['endTime']));
				$time = htmlentities($startTime)." - ".htmlentities($endTime);
			}
			
			// ids for managing show/hide details (only used for special events)
			$detailsAnchorId = "details-anchor-".$event['id'];
			$detailsId = "details-".$event['id'];
			
			// compose location link
			if($isSpecial) {
				$locationLink = "<a href='javascript:toggleDetails(\"".$detailsId."\", \"".$detailsAnchorId."\")'>".htmlentities($event['location'])."</a>";
			}
			else {
				$locationLink = getLocationDirectionsAnchorLink($event['location']);
			}
			
			// get count of volunteers still needed for this event
			$needed = getNeeded($event, $isSpecial);
			
			// button and messaging defaults
			$message = $needed;
			$buttonLabel = "Sign Up";
			$buttonAttributes = "class='signup-button'";
			// see if the event is full
			if($needed < 1) {
				$message = "Filled,<br/>Thank You!";
				$buttonAttributes = "class='signup-button signup-button-disabled' disabled=true";
			}
			// user sign-ups take precedence over full event messaging
			if($alreadySignedUp) {
				$message = "<a href='?page=myVolunteerSchedule'>Already<br/>signed up!</a>";
				$buttonAttributes = "class='signup-button signup-button-disabled' disabled=true";
			}
			
			echo "<tr id='signup-table-row-".( ($isSpecial) ? "se" : "nse" )."-".$event['id']."' class='".( ($isSpecial) ? "signup-special-event-row" : "signup-event-row" )."'>";
			echo "	<td class='signup-left-col'>".htmlentities($date)."<br/>".$time."</td>";
			echo "	<td>".htmlentities($event['type']).( ($isSpecial) ? "<br/><a id='".$detailsAnchorId."' name='".$detailsAnchorId."' href='javascript:toggleDetails(\"".$detailsId."\", \"".$detailsAnchorId."\")'>show details</a>" : "" )."</td>";
			echo "	<td>".$locationLink."</td>";
			echo "	<td>".$message."</td>";
			echo "	<td class='signup-right-col'><input type='submit' ".$buttonAttributes." value='".$buttonLabel."' onclick='signupForDate(".$event['id'].", ".(($isSpecial)?'true':'false').")'/></td>";
			echo "</tr>";
			if($isSpecial) {
				?>
				<tr class="special-event-details-row">
					<td colspan=5>
						<div id="<?php echo $detailsId; ?>" style="display:none;"><!--
							--><div style="width:82px;">Description:</div><!-- fixed-width div required to get Firefox to respect the "word-wrap" property
							--><div><div style="width:108px; word-wrap:break-word;"><?php echo htmlentities($event['shortDescription']); ?></div></div><!--
							--><div style="width:55px;">Details:</div><!-- fixed-width div required to get Firefox to respect the "word-wrap" property
							--><div><div style="width:245px; word-wrap:break-word;"><?php echo processMarkupForHtmlDisplay($event['longDescription']); ?></div></div><!--
						--></div>
					</td>
				</tr>
				<?php
			}
		}
	}	
	?>
</table>

<form method="get" action="" name="sortbyForm" id="sortbyForm">
	<input type="hidden" name="page" id="page" value="signup"/>
	<input type="hidden" name="sortby" id="sortby" value=""/>
	<input type="hidden" name="noScrollTop" id="noScrollTop" value="true"/>
</form>

<form method="get" action="" name="signupForm" id="signupForm">
	<input type="hidden" name="page" id="page" value="signupConfirm"/>
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<input type="hidden" name="signupVDateId" id="signupVDateId" value=""/>
</form>

<form method="get" action="" name="signupFormSpecialEvent" id="signupFormSpecialEvent">
	<input type="hidden" name="page" id="page" value="specialEventSignupConfirm"/>
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<input type="hidden" name="signupSpecialEventDateId" id="signupSpecialEventDateId" value=""/>
</form>

<script type="text/javascript">
<?php
if( isset($_REQUEST['noScrollTop']) ) {
	?>
	parent.skipNextScrollTop();
	<?php
}

if( isset($_REQUEST['sedId']) ) {
	// sedId = special event directions ID, meaning a special event ID to which we want to link for directions.
	// When other pages link to directions for special events, they link to this page via the sedId,
	// which causes the browser to jump up/down to the respective special event and expand it's details.
	// Directions for special events, if provided, are included in the details.
	?>
	parent.skipNextScrollTop();
	$(document).ready(function() {
		var sedId = "<?php echo $_REQUEST['sedId']; ?>";
		// Element offset is relative to iframe, so add iframe offset in as well to get absolute top position relative to browser 
		// window. Then subtract a bit just to leave a small padding above the element at top of browser.
		var offsetTop = $("#signup-table-row-se-"+sedId).offset().top + parent.getIframeTop() - 20;
		var scrollDelay = 100;
		setTimeout(function() {
			// scroll the browser
			$('html, body', window.parent.document).animate({scrollTop: offsetTop}, 1000, function() {
				// on scroll complete, expand details to reveal any directions
				if(this.tagName.toLowerCase() === "body") { // don't do for both html and body or it'll open and then close again
					toggleDetails("details-"+sedId, "details-anchor-"+sedId);
				}
			});
		}, scrollDelay);
	});
	<?php
}
?>

function sortSignupTable(sortby) {
	document.getElementById('sortby').value = sortby;
	document.getElementById('sortbyForm').submit();
}

function signupForDate(dateId, isSpecial) {
	if(isSpecial) {
		document.getElementById('signupSpecialEventDateId').value = dateId;
		document.getElementById('signupFormSpecialEvent').submit();
	}
	else {
		document.getElementById('signupVDateId').value = dateId;
		document.getElementById('signupForm').submit();
	}
}

function toggleDetails(detailsId, detailsAnchorId) {
	var isHiding = true;
	var frameHeight = parent.getIframeHeight();
	var elementHeight = $("#"+detailsId).height();
	var details = document.getElementById(detailsId);
	if(details.style.display == "none") {
		isHiding = false;
	}
	$("#"+detailsId).slideToggle({
		duration: 300,
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
			var detailsAnchor = document.getElementById(detailsAnchorId);
			if(details.style.display == "none") {
				detailsAnchor.innerHTML = "show details";
			}
			else {
				detailsAnchor.innerHTML = "hide details";
			}
		}
	});
}
</script>