<?php

$specialEventDateLoadError = false;

if(isset($_REQUEST['id']) && !isset($_REQUEST['manageSpecialEventDateId'])) {
	$_REQUEST['manageSpecialEventDateId'] = $_REQUEST['id'];
}

if(isset($_REQUEST['manageSpecialEventDateId'])) {
	$_REQUEST['manageSpecialEventDateId'] = truncate(trim($_REQUEST['manageSpecialEventDateId']), 50);
	$specialEventDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['manageSpecialEventDateId']) );
	
	if($specialEventDate) {
		// Load the initial data from the volunteer date into the form
			
		if(!isset($_REQUEST['volunteersNeeded'])) {
			$_REQUEST['volunteersNeeded'] = $specialEventDate['volunteersNeeded'];
		}
		
		//*************Parsing times******************
		if($specialEventDate['hasStartEndTime'] == 0) {
			// load defaults
			$startHour = "5";
			$startMinutes = "45";
			$endHour = "9";
			$endMinutes = "30";
			$startAmChecked = "";
			$startPmChecked = "checked";
			$endAmChecked = "";
			$endPmChecked = "checked";
		}
		else {
			$startTime = strftime("%-l:%M %P", strtotime($specialEventDate['startTime']));
			$endTime = strftime("%-l:%M %P", strtotime($specialEventDate['endTime']));
			$startSuffix = strftime("%P", strtotime($specialEventDate['startTime']));
			$endSuffix = strftime("%P", strtotime($specialEventDate['endTime']));
			
			$startAmChecked = "";
			$startPmChecked = "";
			$endAmChecked = "";
			$endPmChecked = "";
			
			if(strcmp($startSuffix,"am") == 0) {
				$startAmChecked = "checked";
			}
			else {
				$startPmChecked = "checked";
			}
			
			if(strcmp($endSuffix,"am") == 0) {
				$endAmChecked = "checked";
			}
			else {
				$endPmChecked = "checked";
			}
			
			$startHour = strtok($startTime, " :");
			$startMinutes = strtok(" :");
			
			$endHour = strtok($endTime, " :");
			$endMinutes = strtok(" :");
		}
		//**************End parsing*************
		
		if(!isset($_REQUEST['startHour'])) {
			$_REQUEST['startHour'] = $startHour;
		}
		
		if(!isset($_REQUEST['startMinutes'])) {
			$_REQUEST['startMinutes'] = $startMinutes;
		}
		
		if(!isset($_REQUEST['endHour'])) {
			$_REQUEST['endHour'] = $endHour;
		}
		
		if(!isset($_REQUEST['endMinutes'])) {
			$_REQUEST['endMinutes'] = $endMinutes;
		}
		
		if(isset($_REQUEST['startTimeAmPm'])) {
			$startAmChecked = "";
			$startPmChecked = "";
		
			if(strcmp($_REQUEST['startTimeAmPm'], "am") == 0) {
				$startAmChecked = "checked";
			}
			else {
				$startPmChecked = "checked";
			}
		}
		
		if(isset($_REQUEST['endTimeAmPm'])) {
			$endAmChecked = "";
			$endPmChecked = "";
		
			if(strcmp($_REQUEST['endTimeAmPm'], "am") == 0) {
				$endAmChecked = "checked";
			}
			else {
				$endPmChecked = "checked";
			}
		}
		
		if(!isset($_REQUEST['location'])) {
			$_REQUEST['location'] = $specialEventDate['location'];
		}
		if(!isset($_REQUEST['shortDescription'])) {
			$_REQUEST['shortDescription'] = $specialEventDate['shortDescription'];
		}
		if(!isset($_REQUEST['longDescription'])) {
			$_REQUEST['longDescription'] = $specialEventDate['longDescription'];
		}
		if(!isset($_REQUEST['specifiedTime'])) {
			$_REQUEST['specifiedTime'] = ($specialEventDate['hasStartEndTime'] == '1') ? "yes" : "no";
		}
	}
	else {
		//given volunteer date not found
		setErrorMsg("Event not found.", false);
		$specialEventDateLoadError = true;
	}
}
else {
	//no volunteer date selected
	$specialEventDateLoadError = true;
}

$currentSpecialEventDate = $specialEventDateLoadError || ($specialEventDate['date'] >= getMySqlDate(time()));
$titleAddOn = " (archived)";
if($currentSpecialEventDate) {
	$titleAddOn = "";
}

printPageMessagesAndTitle("Admin Services - Volunteer Event".$titleAddOn);

if(!$specialEventDateLoadError) {
	?>
	<div class="text-indent">
	<?php
		if($currentSpecialEventDate) {
			?>
			Back to <a href="?page=adminManageVolunteerDates">Current Events</a>
			<br/>
			<br/>
			<?php
		}
		else {
			?>
			Back to <a href="javascript:viewArchivedSpecialEventDates();">Archived Events</a>	
			<form method="get" action="" name="adminViewArchivedSpecialEventDatesForm" id="adminViewArchivedSpecialEventDatesForm">
				<input type="hidden" name="page" id="page" value="adminManageVolunteerDates">
				<input type="hidden" name="adminViewArchivedSpecialEventDates" id="adminViewArchivedSpecialEventDates" value="true"/>
			</form>
			<br/>
			<?php
		}
	?>
	</div>
	<?php
	$formattedDate = formatDateAsText_monthDayYear($specialEventDate['date']);
	$scheduled = mysqli_fetch_assoc( getCountVolunteersForSpecialEventDateById($specialEventDate['id']) );
	$confirmed = mysqli_fetch_assoc( getCountVolunteersConfirmedForSpecialEventDateById($specialEventDate['id']) );
	$showed = getCountVolunteersShowedForSpecialEventDateById($specialEventDate['id']);
	$showed['NR'] = (int)$scheduled['count(*)'] - ((int)$showed['yes'] + (int)$showed['no']);
	?>
	<form class="table-indent" method="get" action="" name="adminUpdateSpecialEventDateForm" id="adminUpdateSpecialEventDateForm">
		<input type="hidden" name="page" id="page" value="adminManageSpecialEventDate">
		<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
		<input type="hidden" name="manageSpecialEventDateId" id="manageSpecialEventDateId" value="<?php echo htmlentities($_REQUEST['manageSpecialEventDateId']); ?>">
		<table class="adminManageVolunteerDate" style="margin-bottom:10px; table-layout:fixed; word-wrap:break-word;">
			<tr>
				<td class="adminManageVolunteerDateLeft">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft" style="vertical-align:top;">Type:</td>
				<td>
					<?php
						echo htmlentities("Special Event");
					?>
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft" style="vertical-align:top;">Location:</td>
				<td <?php if($currentSpecialEventDate) { echo "style='vertical-align:inherit;'"; } ?>>
					<?php
					if($currentSpecialEventDate) {
						?>
						<input name="location" id="location" maxlength="300" style="width:396px;" value="<?php echo htmlentities($_REQUEST['location']); ?>">
						<?php
					}
					else {
						echo htmlentities($specialEventDate['location']);
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">Date:</td>
				<td>
					<?php echo htmlentities($formattedDate); ?>
				</td>
			</tr>
			<?php
			if($currentSpecialEventDate) {
				?>
				<tr>
					<td class="adminManageVolunteerDateLeft">
						<div style="margin-bottom:-4px;">
							Time:
						</div>
					</td>
					<td>
						<div style="margin-bottom:-4px;">
							Anytime:
							<input type="radio" name="specifiedTime" id="specifiedTimeNo" value="no" <?php if($_REQUEST['specifiedTime'] == "no" || $_REQUEST['specifiedTime'] == "") echo "checked"; ?> onclick="javascript:showHideTimeInput();">
							<div class="spacer"></div>
							Specified Time:
							<input type="radio" name="specifiedTime" id="specifiedTimeYes" value="yes" <?php if($_REQUEST['specifiedTime'] == "yes") echo "checked"; ?> onclick="javascript:showHideTimeInput();">
						</div>
					</td>
				</tr>
				<tr id="timeInputRow" style="<?php if(!isset($_REQUEST['specifiedTime']) || $_REQUEST['specifiedTime'] != 'yes') echo 'display:none;'; ?>">
					<td colspan="2">
						<div id="timeInput" style="margin-bottom:-2px; <?php if(!isset($_REQUEST['specifiedTime']) || $_REQUEST['specifiedTime'] != 'yes') echo 'display:none;'; ?>">
							<div style="padding-left:24px; display:inline-block; width:150px;">
								<div style="padding-bottom:3px;">
									Start time:
								</div>
								<div>
									End Time:
								</div>
							</div>
							<div style="display:inline-block; padding-left:4px;">
								<div style="padding-bottom:3px;">
									<input name="startHour" id="startHour" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['startHour']); ?>">
									: <input name="startMinutes" id="startMinutes" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['startMinutes']); ?>">&nbsp;<input type ="radio" name="startTimeAmPm" value="am" <?php echo htmlentities($startAmChecked); ?>>AM&nbsp;<input type ="radio" name="startTimeAmPm" value="pm" <?php echo htmlentities($startPmChecked); ?>>PM
								</div>
								<div>
									<input name="endHour" id="endHour" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['endHour']); ?>">
									: <input name="endMinutes" id="endMinutes" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['endMinutes']); ?>">&nbsp;<input type ="radio" name="endTimeAmPm" value="am" <?php echo htmlentities($endAmChecked); ?>>AM&nbsp;<input type ="radio" name="endTimeAmPm" value="pm" <?php echo htmlentities($endPmChecked); ?>>PM
								</div>
							</div>
						</div>
					</td>
				</tr>
				<?php
			}
			else if($specialEventDate['hasStartEndTime'] == '0') {
				?>
				<tr>
					<td class="adminManageVolunteerDateLeft">Time:</td>
					<td>Anytime</td>
				</tr>
				<?php
			}
			else {
				?>
				<tr>
					<td class="adminManageVolunteerDateLeft">Start time:</td>
					<td><?php echo htmlentities($startTime); ?></td>
				</tr>
				<tr>
					<td class="adminManageVolunteerDateLeft">End time:</td>
					<td><?php echo htmlentities($endTime); ?></td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td class="adminManageVolunteerDateLeft" style="vertical-align:top;">
					<?php if($currentSpecialEventDate) { echo "<div style='margin-bottom:4px;'>"; } ?>
						Description:
					<?php if($currentSpecialEventDate) { echo "<div>"; } ?>
				</td>
				<td <?php if($currentSpecialEventDate) { echo "style='vertical-align:inherit;'"; } ?>>
					<?php
					if($currentSpecialEventDate) {
						 ?>
						 <div style="margin-top:4px;">
							 <input name="shortDescription" id="shortDescription" maxlength="300" style="width:396px;" value="<?php echo htmlentities($_REQUEST['shortDescription']); ?>">
						 </div>
						<?php
					}
					else {
						echo htmlentities($specialEventDate['shortDescription']);
					}
					?>				
				</td>
			</tr>
			<?php
			if($currentSpecialEventDate) {
				?>
				<tr>
					<td></td>
					<td style="text-align:right;">
						<div style="margin-top:2px; margin-bottom:-7px;">
							<a href="javascript:toggleSEDetailsOptions();" style="">Options<span id="se-details-options-arrow" class="fa fa-angle-down" style="padding-left:4px; font-size:16px; position:relative; top:1px;"></span></a>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td class="adminManageVolunteerDateLeft" style="vertical-align:top;">Details:<?php if($currentSpecialEventDate) { echo "<br/><i>(incl. directions)</i>"; } ?></td>
				<td>
					<?php
					if($currentSpecialEventDate) {
						?>
						<div id="seDetailsOptions" style="display:none; padding:8px 30px; margin:3px 0px 8px; background-color:#f4f4f4;">
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
						<div style="display:inline-block; margin-bottom:-13px;">
							<textarea name="longDescription" id="longDescription" maxlength="1000" style="width:392px;" rows="10" cols="19" oninput="textareaResize();textCounter();"><?php echo htmlentities($_REQUEST['longDescription']); ?></textarea> 
							<div style="color:#999999; text-align:right; padding-right:2px;"><span id="charsLeftCount">1000</span> left</div>
						</div>
						<?php
					}
					else {
						echo processMarkupForHtmlDisplay($specialEventDate['longDescription']);
					}
					?>				
				</td>
			</tr>
			<?php
			if($currentSpecialEventDate) {
				?>
				<tr>
					<td class="adminManageVolunteerDateLeft"></td>
					<td>
						<div style="margin-bottom:12px;">
							Preview &ndash; Portal/HTML display <i>(width <b>will</b> vary)</i>
							<div style="background-color:#f4f4f4; padding:10px; width:379px; margin-bottom:15px;">
								<?php echo processMarkupForHtmlDisplay($_REQUEST['longDescription']); ?>
							</div>
							Preview &ndash; Email/text display <i>(width <b>will</b> vary)</i>
							<div style="background-color:#f4f4f4; padding:10px; width:379px;">
								<?php
									// little extra processing needed here because we're simulating plain text in an html environment
									echo "Details: <span style='white-space:pre-wrap;'>".htmlentities(processMarkupForTextDisplay($_REQUEST['longDescription']))."</span>";
								?>
							</div>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td class="adminManageVolunteerDateLeft">Volunteers Needed:</td>
				<td <?php if($currentSpecialEventDate) { echo "style='vertical-align:inherit;'"; } ?>>
					<?php
					if($currentSpecialEventDate) {
						?>
						<input name="volunteersNeeded" id="volunteersNeeded" maxlength="3" style="width: 23px;" value="<?php echo htmlentities($_REQUEST['volunteersNeeded']); ?>">
						<?php
					}
					else {
						echo htmlentities($specialEventDate['volunteersNeeded']);
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">Scheduled:</td>
				<td>
					<?php echo $scheduled['count(*)']; ?>
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">Confirmed:</td>
				<td>
					<?php echo $confirmed['count(*)']; ?>
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">Showed (Yes/No/NR):</td>
				<td>
					<?php echo $showed['yes']." / ".$showed['no']." / ".$showed['NR']; ?>
				</td>
			</tr>
			<tr>
				<?php
				if($currentSpecialEventDate) {
					?>
					<td class="adminManageVolunteerDateLeft"><input style="margin-top:5px;" type="submit" name="adminUpdateSpecialEventDateSubmit" id="adminUpdateSpecialEventDateSubmit" value="Save"/></td>
					<td>&nbsp;</td>
					<?php
				}
				?>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>
	</form>
	<br/>
	<?php
	$volunteers = getSpecialEventVolunteersByVolunteerDateId($specialEventDate['id']);
	?>
	<table class="text-indent" style="border-collapse:collapse; margin-bottom:5px; width:618px;">
		<tr>
			<td><h2 style="margin:0px;">Scheduled Volunteers</h2></td>
			<td style="text-align:right;">Download Schedule: <a href="javascript:downloadSchedule('xls');">Excel</a>, <a href="javascript:downloadSchedule('html');">HTML</a></td>
		</tr>
	</table>
	<form class="table-indent" method="get" action="" name="adminManageSpecialEventVolunteersForm" id="adminManageSpecialEventVolunteersForm">
		<input type="hidden" name="page" id="page" value="adminManageSpecialEventDate">
		<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
		<input type="hidden" name="manageVDateId" id="manageVDateId" value="<?php echo htmlentities($_REQUEST['manageSpecialEventDateId']); ?>">
		<input type="hidden" name="isSpecialEventVolunteerDate" id="isSpecialEventVolunteerDate" value="1"/>
		<style>
			table.adminManageVolunteers th, table.adminManageVolunteers td {
				padding: 8px 8px 8px 8px;
			}
		</style>
		<table class="adminManageVolunteers" style="table-layout:fixed; word-wrap:break-word;">
			<?php
			if($scheduled['count(*)'] < 1) {
				?>
				<tr>
					<td style="text-align:left;">None</td>
				</tr>
				<?php
			}
			else {
				if($currentSpecialEventDate) {
					?>
					<col style="width:35px;"/>
					<col style="width:112px;"/>
					<col style="width:108px;"/>
					<col style="width:101px;"/>
					<col style="width:101px;"/>
					<col style="width:82px;"/>
					<col style="width:78px;"/>
					<?php
				}
				else {
					?>
					<col style="width:39px;"/>
					<col style="width:127px;"/>
					<col style="width:122px;"/>
					<col style="width:74px;"/>
					<col style="width:83px;"/>
					<col style="width:94px;"/>
					<col style="width:78px;"/>
					<?php
				}
				?>
				<tr>
					<th style="padding-left:0px; padding-right:0px; text-align:center;"><input type='checkbox' name='selectAll' value='' onclick='javascript:toggleAll(this);'></th>
					<th>Username</th>
					<th>Name</th>
					<th>Sign-up Date</th>
					<th>Date Confirmed</th>
					<th>Showed?</th>
					<?php
						echo "<th>Remove</th>";
					?>
				</tr>
				<?php
				while($volunteer = mysqli_fetch_assoc($volunteers)) {
					$formattedDateSignupDate = formatDateAsText_monthDayYear($volunteer['signupDate'], true);
					$acct = mysqli_fetch_assoc( getAccountByUname($volunteer['uname']) );
					echo "<tr>";
					echo "	<td style=\"padding-left:0px; padding-right:0px; text-align:center;\"><input type='checkbox' name='unameSelect' value='".htmlentities($acct['uname'])."'></td>";
					echo "	<td><a href=\"javascript:editAccount('".addslashes($acct['uname'])."');\">".htmlentities($acct['uname'])."</a></td>";
					echo "	<td>".htmlentities($acct['lastname']).", ".htmlentities($acct['firstname'])."</td>";
					echo "	<td><center>".htmlentities($formattedDateSignupDate)."</center></td>";
					echo "	<td><center>";
					if($volunteer['confirmed'] == "") {
						echo "None";
						if($volunteer['confirmId'] != null) {
							echo "*";
						}
					}
					else {
						echo htmlentities( formatDateAsText_monthDayYear($volunteer['confirmed'], true) );
					}
					echo "</center></td>";
					echo "	<td><center>";
					if($volunteer['showed'] == '0') {
						echo "No";
					}
					else if($volunteer['showed'] == '1') {
						echo "Yes";
					}
					else {
						echo "Not Recorded";
					}
					echo "</center></td>";
					echo "	<td><center><a href=\"javascript:removeSpecialEventSignup('".addslashes($volunteer['uname'])."', '".addslashes($volunteer['volunteerDateId'])."');\" class='deleteImgLink'><img src='images/pageElements/delete.svg'/></a></center></td>";
					echo "</tr>";
				}
			}
			?>
		</table>
		&nbsp;* Indicates reminder / confirm email has been sent.<br>

		<button type="button" id="volunteer-add-button">Manually add volunteer</button>
		<div id="manual-volunteer-add">
			<form method="get" action="" name="adminCreateAcctForm" id="adminCreateAcctForm">
				<h2>Manually Add Volunteer</h2>
				<div class="input-block">
					<label for="firstname">First Name</label>
					<input type="text" id="firstname" name="firstname">
				</div>
				<div class="input-block">
					<label for="lastname">Last Name</label>
					<input type="text" id="lastname" name="lastname">
				</div>
				<div class="input-block full">
					<label for="email">Email Address</label>
					<input type="email" id="email" name="email">
				</div>
				<div class="button-block">
					<input type="hidden" name="page" id="page" value="adminManageSpecialEventDate">
					<button type="submit" id="adminCreateAcctSubmit" name="adminCreateAcctSubmit">Add Volunteer</button>
					<button type="button" id="cancel-manual-volunteer">Cancel</button>
				</div>
			</form>
		</div>
		<?php
		if($scheduled['count(*)'] > 0) {
			?>
			<br/>
			<br/>
			<table class="adminUpdateVolunteers invisible-table" style="margin-left:-3px; margin-top:2px;">
				<tr>
					<th>For the selected volunteers:</th>
				</tr>
				<tr>
					<td>
						<input type="button" name="updateShowedStatusButton" id="updateShowedStatusButton" value="Set" onclick="javascript:updateShowedStatus();"/><div style="display:inline-block; width:7px;"></div>showed: <select name="showedStatus" id="showedStatus"><option value="Yes">Yes</option><option value="No">No</option><option value="Not Recorded">Not Recorded</option></select>
					</td>
				</tr>
				<?php
				if($currentSpecialEventDate) {
					?>
					<tr>
						<td>
							<input type="button" name="sendReminderConfirmEmailButton" id="sendReminderConfirmEmailButton" value="Send" onclick="javascript:sendReminderConfirmEmail();"/><div style="display:inline-block; width:7px;"></div>reminder / confirm email
						</td>
					</tr>
					<tr>
						<td>
							<input type="button" name="resendSignupEmailButton" id="resendSignupEmailButton" value="Resend" onclick="javascript:resendSignupEmail();"/><div style="display:inline-block; width:7px;"></div>initial sign-up email
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<input type="hidden" name="selectedVolunteers" id="selectedVolunteers" value=""/>
			<input type="hidden" name="volunteerAction" id="volunteerAction" value=""/>
			<?php
		}
		?>
	</form>
	<form method="get" action="" name="adminManageAcctsEditAcctForm" id="adminManageAcctsEditAcctForm">
		<input type="hidden" name="page" id="page" value="adminManageAcct">
		<input type="hidden" name="editAcctUname" id="editAcctUname" value=""/>
	</form>

		<form method="get" action="" name="adminRemoveSignupForm" id="adminRemoveSignupForm">
			<input type="hidden" name="page" id="page" value="adminManageSpecialEventDate">
			<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
			<input type="hidden" name="manageSpecialEventDateId" id="manageSpecialEventDateId" value="<?php echo htmlentities($_REQUEST['manageSpecialEventDateId']); ?>">
			<input type="hidden" name="removeSignup" id="removeSignup" value="false"/>
			<input type="hidden" name="removeSignupUname" id="removeSignupUname" value=""/>
			<input type="hidden" name="removeSignupSpecialEventDateId" id="removeSignupSpecialEventDateId" value=""/>
		</form>

	<form method="get" action="" target="downloadSchedule<?php echo $specialEventDate['id']; ?>" name="downloadScheduleForm" id="downloadScheduleForm">
		<input type="hidden" name="page" id="page" value="downloadVolunteerDateSchedule"/>
		<input type="hidden" name="downloadScheduleVDateId" id="downloadScheduleVDateId" value="<?php echo $specialEventDate['id']; ?>"/>
		<input type="hidden" name="isSpecialEventVolunteerDate" id="isSpecialEventVolunteerDate" value="1"/>
	</form>
	<script type="text/javascript">
	jQuery(function($) {
		$('#volunteer-add-button').click(function() {
			$('#volunteer-add-button').slideUp();
			$('#manual-volunteer-add').slideDown();
    	});
		$('#cancel-manual-volunteer').click(function() {
			$('#volunteer-add-button').slideDown();
			$('#manual-volunteer-add').slideUp();
			$('#manual-first-name').val('');
			$('#manual-last-name').val('');
			$('#manual-email').val('');
		});
	});
	
	function toggleAll(selectAll_checkbox) {
		var checkValue = false; // uncheck all checkboxes
		if(selectAll_checkbox.checked) {
			checkValue = true; // check all checkboxes
		}
		var checkboxes = document.adminManageSpecialEventVolunteersForm.unameSelect;
		if(typeof checkboxes.length == 'undefined') {
			checkboxes.checked = checkValue;
		}
		else {
			for(var i=0; i<checkboxes.length; i++) {
				checkboxes[i].checked = checkValue;
			}
		}
	}
	
	function viewArchivedSpecialEventDates() {
		document.getElementById('adminViewArchivedSpecialEventDatesForm').submit();
	}
	
	function editAccount(editAcctUname) {
		document.getElementById('editAcctUname').value = editAcctUname;
		document.getElementById('adminManageAcctsEditAcctForm').submit();
	}
	
		// No-op most of the time, but on browser back, depending on form state, 
		// browser may change the toggle without calling this function, so we call
		// it here to make sure things are in sync in that specific case.
		$(document).ready(showHideTimeInput);
		
		function showHideTimeInput() {
			var isHiding = true;
			if(document.getElementById('specifiedTimeYes').checked) {
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
			if(document.getElementById('specifiedTimeYes').checked) {
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
		var textareaMinHeight = $('#longDescription').height(); // initial page load height (set in html/css above)
		window.setTimeout( function(){
			textareaResize();
		}, 1); // slight delay is for older IE browsers

		function textareaResize() {
			$('#longDescription').height(1); // to get accurage measurement from scroll height
			var scrollHeight = $('#longDescription')[0].scrollHeight + 10; // buffer to prevent scrollbar from briefly appearing
			if(scrollHeight > textareaMinHeight) {
				$('#longDescription').height(scrollHeight);
			}
			else {
				$('#longDescription').height(textareaMinHeight);
			}
			parent.resizeIframe();
		}
		
		// initialize characters left counter
		textCounter();
	
		function textCounter() {
			var field = document.getElementById('longDescription');
			document.getElementById('charsLeftCount').innerHTML = field.maxLength - field.value.length;
		}
		
		function removeSpecialEventSignup(removeSignupUname, removeSignupSpecialEventDateId) {
			parent.popupConfirm("<p>Delete sign-up?</p><p>"+removeSignupUname+"</p>").then(function(result){
				if( result === "OK" ) {
					document.getElementById('removeSignup').value = "true";
					document.getElementById('removeSignupUname').value = removeSignupUname;
					document.getElementById('removeSignupSpecialEventDateId').value = removeSignupSpecialEventDateId;
					document.getElementById('adminRemoveSignupForm').submit();
				}
			});
		}
	
	function downloadSchedule(format) {
		if(format == 'html') {
			document.getElementById('downloadScheduleForm').submit();
		}
		else {
			window.location = "?page=downloadVolunteerDateSchedule&downloadScheduleVDateId=<?php echo $specialEventDate['id']; ?>&isSpecialEventVolunteerDate=1&format=xls";
		}
	}
	
	<?php
	if($scheduled['count(*)'] > 0) {
		?>
		var options = document.getElementById('showedStatus').getElementsByTagName('*');
		for(var i=0; i<options.length; i++) {
			var option = options[i];
			if( option.value == "<?php echo addslashes($_REQUEST['showedStatus']); ?>" ) {
				option.selected = true;
				break;
			}
		}

		function setSelectedVolunteers() {
			var selectedVolunteers = "";
			var chkbox = document.adminManageSpecialEventVolunteersForm.unameSelect;
			if(typeof chkbox.length == 'undefined') {
				if(chkbox.checked) {
					selectedVolunteers = chkbox.value;
				}
			}
			else {
				for(var i=0; i<chkbox.length; i++) {
					if(chkbox[i].checked) {
						selectedVolunteers += chkbox[i].value+"\n";
					}
				}
			}
			document.getElementById('selectedVolunteers').value = selectedVolunteers;
		}
		
		function updateShowedStatus() {
			setSelectedVolunteers();
			document.getElementById('volunteerAction').value = "updateShowedStatus";
			document.getElementById('adminManageSpecialEventVolunteersForm').submit();
		}
		
		<?php
		if($currentSpecialEventDate) {
			?>
			function sendReminderConfirmEmail() {
				parent.popupConfirm("Send reminder/confirm email to selected volunteers?").then(function(result){
					if( result === "OK" ) {
						setSelectedVolunteers();
						document.getElementById('volunteerAction').value = "sendReminderConfirmEmail";
						document.getElementById('adminManageSpecialEventVolunteersForm').submit();
					}
				});
			}
			
			function resendSignupEmail() {
				parent.popupConfirm("Resend initial sign-up email to selected volunteers?").then(function(result){
					if( result === "OK" ) {
						setSelectedVolunteers();
						document.getElementById('volunteerAction').value = "resendSignupEmail";
						document.getElementById('adminManageSpecialEventVolunteersForm').submit();
					}
				});
			}
			<?php
		}
	}
	?>
	</script>
	<?php
}
