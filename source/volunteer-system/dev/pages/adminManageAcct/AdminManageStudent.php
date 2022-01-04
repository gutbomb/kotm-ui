<table class="account account-bottom" style="border-top:none;" style="margin-bottom:-5px;">
	<tr>
		<td class="accountLeft">Are you a student?*</td>
		<td>
			Yes: 
			<input type="radio" name="student" id="studentYes" value="yes" <?php if($_REQUEST['student'] == "yes") echo "checked"; ?> onclick="javascript:showHideSchoolInput();">
			&nbsp;No: 
			<input type="radio" name="student" id="studentNo" value="no" <?php if($_REQUEST['student'] == "no") echo "checked"; ?> onclick="javascript:showHideSchoolInput();">
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<div id="schoolInput" style="<?php if(!isset($_REQUEST['student']) || $_REQUEST['student'] != 'yes') echo 'display:none; '; ?>padding-bottom:5px;">
				<div style="display:inline-block; width:123px; padding-right:21px;">School Name*</div><input name="school" id="school" maxlength="100" style="width:175px;" value="<?php echo htmlentities($_REQUEST['school']); ?>">
			</div>
		</td>
	</tr>
	<tr>
		<td class="accountLeft">How did you hear about Kids On The Move?</td>
		<td>
			<div style="display:inline-block; margin-bottom:-20px;">
				<textarea name="howHeardAbout" id="howHeardAbout" maxlength="500" style="width:406px;" rows="5" cols="19" oninput="textCounter();"><?php echo htmlentities($_REQUEST['howHeardAbout']); ?></textarea>
				<div style="color:#999999; text-align:right; padding-right:2px;"><span id="charsLeftCount">500</span> left</div>
			</div>
		</td>
	</tr>
	<tr>
		<td class="accountLeft" style="padding-top:18px;">
			<input type="submit" name="adminEditAcctSubmit" id="adminEditAcctSubmit" value="Save"/>
		</td>
	</tr>
</table>
<script type="text/javascript">
// No-op most of the time, but on browser back, depending on form state, 
// browser may change the toggle without calling this function, so we call
// it here to make sure things are in sync in that specific case.
$(document).ready(showHideSchoolInput);

function showHideSchoolInput() {
	var isHiding = !document.getElementById('studentYes').checked;
	var frameHeight = parent.getIframeHeight();
	var elementHeight = $("#schoolInput").height();
	var highestProgress = 0;
	var slideOptions = {
		duration: 300,
		progress: function(animation, progress, remainingMs){
			if(highestProgress < progress) {
				// Since there are multiple elements being slid, and the progress
				// function is called for each, only update the frame height if the
				// current element's progress is more than the last highest progress.
				// Prevents jitter if the elements don't progress at the same rate.
				// Also avoids wasting time/cycles, which could slow the animation.
				highestProgress = progress;
				if(isHiding) {
					parent.setIframeHeight( frameHeight - (elementHeight*progress) );
				}
				else {
					parent.setIframeHeight( frameHeight + (elementHeight*progress) );
				}
			}
		},
		complete: function(){
			parent.resizeIframe();
		}
	}
	if(document.getElementById('studentYes').checked) {
		$("#schoolInput").slideDown(slideOptions);
	}
	else {
		$("#schoolInput").slideUp(slideOptions);
	}
}

// initialize characters left counter
textCounter();

function textCounter() {
	var field = document.getElementById('howHeardAbout');
	document.getElementById('charsLeftCount').innerHTML = field.maxLength - field.value.length;
}
</script>