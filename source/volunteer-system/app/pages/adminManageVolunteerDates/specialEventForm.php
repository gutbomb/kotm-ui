<?php
	
	if(!isset($_REQUEST['specialEventStartHour'])) {
		$_REQUEST['specialEventStartHour'] = 5;
	}
	
	if(!isset($_REQUEST['specialEventStartMinutes'])) {
		$_REQUEST['specialEventStartMinutes'] = 45;
	}
	
	if(!isset($_REQUEST['specialEventEndHour'])) {
		$_REQUEST['specialEventEndHour'] = 9;
	}
	
	if(!isset($_REQUEST['specialEventEndMinutes'])) {
		$_REQUEST['specialEventEndMinutes'] = 30;
	}

	$startAmChecked = "";
	$startPmChecked = "";
	
	if(isset($_REQUEST['specialEventStartTimeAmPm'])) {
		if(strcmp($_REQUEST['specialEventStartTimeAmPm'], "am") == 0) {
			$startAmChecked = "checked";
		}
		else {
			$startPmChecked = "checked";
		}
	}
	else
	{
		$startPmChecked = "checked";
	}
	
	$endAmChecked = "";
	$endPmChecked = "";
	
	if(isset($_REQUEST['specialEventEndTimeAmPm'])) {
		if(strcmp($_REQUEST['specialEventEndTimeAmPm'], "am") == 0) {
			$endAmChecked = "checked";
		}
		else {
			$endPmChecked = "checked";
		}
	}
	else
	{
		$endPmChecked = "checked";
	}
	
?>
<h2>Special Events</h2>
<form method="get" action="" name="adminAddSpecialEventDateForm" id="adminAddSpecialEventDateForm">
	<input type="hidden" name="page" id="page" value="adminManageVolunteerDates">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<table class="adminAddVolunteerDate" style="padding-bottom:10px;">
		<tr>
			<td style="padding-right:0px;">
				Location:&nbsp;
				<input name="specialEventLocation" id="specialEventLocation" maxlength="300" style="width:282px;" value="<?php echo htmlentities($_REQUEST['specialEventLocation']); ?>">
			</td>
		</tr>
		<tr>
			<td>
				Date:&nbsp;
				<input type="hidden" id="specialEventVolunteerMonthLength">
				<input type="hidden" id="specialEventVolunteerDayLength">
				<input name="specialEventVolunteerMonth" id="specialEventVolunteerMonth" maxlength="2" style="width:18px;" value="<?php echo htmlentities($_REQUEST['specialEventVolunteerMonth']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'specialEventVolunteerDay');"> /
				<input name="specialEventVolunteerDay" id="specialEventVolunteerDay" maxlength="2" style="width:18px;" value="<?php echo htmlentities($_REQUEST['specialEventVolunteerDay']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'specialEventVolunteerYear');"> /
				<input name="specialEventVolunteerYear" id="specialEventVolunteerYear" maxlength="4" style="width:30px;" value="<?php echo htmlentities($_REQUEST['specialEventVolunteerYear']); ?>">&nbsp;
				(mm/dd/yyyy)
			</td>
		</tr>
		<tr>
			<td>
				Anytime:
				<input type="radio" name="specialEventSpecifiedTime" id="specialEventSpecifiedTimeNo" value="no" <?php if($_REQUEST['specialEventSpecifiedTime'] == "no" || $_REQUEST['specialEventSpecifiedTime'] == "") echo "checked"; ?> onclick="javascript:showHideTimeInput();">
				<div class="spacer"></div>
				Specified Time:
				<input type="radio" name="specialEventSpecifiedTime" id="specialEventSpecifiedTimeYes" value="yes" <?php if($_REQUEST['specialEventSpecifiedTime'] == "yes") echo "checked"; ?> onclick="javascript:showHideTimeInput();">
			</td>
		</tr>
		<tr id="timeInputRow" style="<?php if(!isset($_REQUEST['specialEventSpecifiedTime']) || $_REQUEST['specialEventSpecifiedTime'] != 'yes') echo 'display:none;'; ?>">
			<td>
				<div id="timeInput" style="<?php if(!isset($_REQUEST['specialEventSpecifiedTime']) || $_REQUEST['specialEventSpecifiedTime'] != 'yes') echo 'display:none;'; ?>">
					<div style="padding-bottom:8px;">
						Start time:&nbsp;
						<input name="specialEventStartHour" id="specialEventStartHour" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['specialEventStartHour']); ?>">
						: <input name="specialEventStartMinutes" id="specialEventStartMinutes" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['specialEventStartMinutes']); ?>">&nbsp;
						<input type ="radio" name="specialEventStartTimeAmPm" value="am" <?php echo htmlentities($startAmChecked); ?>>AM
						<input type ="radio" name="specialEventStartTimeAmPm" value="pm" <?php echo htmlentities($startPmChecked); ?>>PM
					</div>
					<div>
						End time:&nbsp;
						<input name="specialEventEndHour" id="specialEventEndHour" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['specialEventEndHour']); ?>">
						: <input name="specialEventEndMinutes" id="specialEventEndMinutes" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['specialEventEndMinutes']); ?>">&nbsp;
						<input type ="radio" name="specialEventEndTimeAmPm" value="am" <?php echo htmlentities($endAmChecked); ?>>AM
						<input type ="radio" name="specialEventEndTimeAmPm" value="pm" <?php echo htmlentities($endPmChecked); ?>>PM
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				Volunteers Needed (number):&nbsp;
				<input name="specialEventVolunteersNeeded" id="specialEventVolunteersNeeded" maxlength="3" style="width:25px;" value="<?php echo htmlentities($_REQUEST['specialEventVolunteersNeeded']); ?>">
			</td>
		</tr>
		<tr>
			<td style="padding-right:0px;">
				Description:<br/>
				<input name="specialEventShortDescription" id="specialEventShortDescription" maxlength="300" style="width:342px;" value="<?php echo htmlentities($_REQUEST['specialEventShortDescription']); ?>">
			</td>
		</tr>
		<tr>
			<td style="padding-right:0px;">
				<!-- span wrapping Details label prevents Safari from shifting the text when Options are toggled -->
				<div>
					<span>Details <i>(incl. directions)</i><div class="spacer" style="width:1px;"></div>:</span>
					<div style="display:inline-block; width:201px; text-align:right;">
						<a href="javascript:toggleSEDetailsOptions();" style="">Options<span id="se-details-options-arrow" class="fa fa-angle-down" style="padding-left:4px; font-size:16px; position:relative; top:1px;"></span></a>
					</div>
				</div>
				<div id="seDetailsOptions" style="display:none; padding:8px 30px; margin:2px 0px 7px; background-color:#f4f4f4;">
					<table style="width:100%;" class="se-details-options">
						<tr>
							<td>*bold*</td>
							<td><span class="fa fa-long-arrow-right" style="padding-left:4px; font-size:16px; position:relative; top:1px;"></span></td>
							<td style="font-weight:bold;">bold</td>
						</tr>
						<tr>
							<td>~italic~</td>
							<td><span class="fa fa-long-arrow-right" style="padding-left:4px; font-size:16px; position:relative; top:1px;"></span></td>
							<td style="font-style:italic;">italic</td>
						</tr>
						<tr>
							<td>_underline_</td>
							<td><span class="fa fa-long-arrow-right" style="padding-left:4px; font-size:16px; position:relative; top:1px;"></span></td>
							<td style="text-decoration:underline;">underline</td>
						</tr>
						<tr>
							<td>[[http://google.com]]</td>
							<td><span class="fa fa-long-arrow-right" style="padding-left:4px; font-size:16px; position:relative; top:1px;"></span></td>
							<td><a href="http://google.com" target="_blank">http://google.com</a></td>
						</tr>
					</table>
				</div>
				<div style="margin-bottom:-16px;">
					<textarea name="specialEventLongDescription" id="specialEventLongDescription" maxlength="1000" style="width:338px;" rows="8" cols="19" oninput="textareaResize();textCounter();"><?php echo htmlentities($_REQUEST['specialEventLongDescription']); ?></textarea>
					<div style="color:#999999; text-align:right; padding-right:2px;"><span id="charsLeftCount">1000</span> left</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" name="adminAddSpecialEventDateSubmit" id="adminAddSpecialEventDateSubmit" value="Add Event"/>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
// No-op most of the time, but on browser back, depending on form state, 
// browser may change the toggle without calling this function, so we call
// it here to make sure things are in sync in that specific case.
$(document).ready(showHideTimeInput);

function showHideTimeInput() {
	var isHiding = true;
	if(document.getElementById('specialEventSpecifiedTimeYes').checked) {
		isHiding = false;
		$('#timeInputRow').show();
		parent.resizeIframe();
	}
	var frameHeight = parent.getIframeHeight();
	var elementHeight = $('#timeInput').height();
	var slideOptions = {
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
			if(isHiding) {
				$('#timeInputRow').hide();
			}
			parent.resizeIframe();
		}
	}
	if(document.getElementById('specialEventSpecifiedTimeYes').checked) {
		$('#timeInput').slideDown(slideOptions);
	}
	else {
		$('#timeInput').slideUp(slideOptions);
	}
}

function toggleSEDetailsOptions() {
	var showing = ( $('#seDetailsOptions').css('display') === 'none' );
	var frameHeight = parent.getIframeHeight();
	var elementHeight = $("#seDetailsOptions").height();
	var highestProgress = 0;
	var slideOptions = {
		duration: 400,
		progress: function(animation, progress, remainingMs){
			if(highestProgress < progress) {
				// Since there are multiple elements being slid, and the progress
				// function is called for each, only update the frame height if the
				// current element's progress is more than the last highest progress.
				// Prevents jitter if the elements don't progress at the same rate.
				// Also avoids wasting time/cycles, which could slow the animation.
				highestProgress = progress;
				var boost = 1.35; // helps smooth things out a bit
				if(showing) {
					parent.setIframeHeight( frameHeight + (elementHeight*progress*boost) );
				}
				else {
					parent.setIframeHeight( frameHeight - (elementHeight*progress*boost) );
				}
			}
		},
		complete: function(){
			parent.resizeIframe();
		}
	}
	$('#seDetailsOptions').slideToggle(slideOptions);
	if(showing) {
		$('#se-details-options-arrow').removeClass('fa-angle-down');
		$('#se-details-options-arrow').addClass('fa-angle-up');
		$('#se-details-options-arrow').css('top', '0px');
	}
	else {
		$('#se-details-options-arrow').removeClass('fa-angle-up');
		$('#se-details-options-arrow').addClass('fa-angle-down');
		$('#se-details-options-arrow').css('top', '1px');
	}
}

// make sure details textarea is resized to fit content
var textareaMinHeight = $('#specialEventLongDescription').height(); // initial page load height (set in html/css above)
window.setTimeout( function(){
	textareaResize();
}, 1); // slight delay is for older IE browsers

function textareaResize() {
	$('#specialEventLongDescription').height(1); // to get accurage measurement from scroll height
	var scrollHeight = $('#specialEventLongDescription')[0].scrollHeight + 10; // buffer to prevent scrollbar from briefly appearing
	if(scrollHeight > textareaMinHeight) {
		$('#specialEventLongDescription').height(scrollHeight);
	}
	else {
		$('#specialEventLongDescription').height(textareaMinHeight);
	}
	parent.resizeIframe();
}

// initialize characters left counter
textCounter();

function textCounter() {
	var field = document.getElementById('specialEventLongDescription');
	document.getElementById('charsLeftCount').innerHTML = field.maxLength - field.value.length;
}
</script>