<?php

$vDateLoadError = false;

if(isset($_REQUEST['id']) && !isset($_REQUEST['manageVDateId'])) {
	$_REQUEST['manageVDateId'] = $_REQUEST['id'];
}

if(isset($_REQUEST['manageVDateId'])) {
	$_REQUEST['manageVDateId'] = truncate(trim($_REQUEST['manageVDateId']), 50);
	$vDate = mysqli_fetch_assoc( getVolunteerDateById($_REQUEST['manageVDateId']) );
	
	if($vDate) {
		// Load the initial data from the volunteer date into the form
			
		if(!isset($_REQUEST['volunteersNeeded'])) {
			$_REQUEST['volunteersNeeded'] = $vDate['volunteersNeeded'];
		}
		
		//*************Parsing times******************
		$startTime = strftime("%-l:%M %P", strtotime($vDate['startTime']));
		$endTime = strftime("%-l:%M %P", strtotime($vDate['endTime']));
		$startSuffix = strftime("%P", strtotime($vDate['startTime']));
		$endSuffix = strftime("%P", strtotime($vDate['endTime']));
		
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
	}
	else {
		//given volunteer date not found
		setErrorMsg("Event not found.", false);
		$vDateLoadError = true;
	}
}
else {
	//no volunteer date selected
	setErrorMsg("No event selected.", false);
	$vDateLoadError = true;
}

$currentVDate = $vDateLoadError || ($vDate['date'] >= getMySqlDate(time()));
$titleAddOn = " (archived)";
if($currentVDate) {
	$titleAddOn = "";
}

printPageMessagesAndTitle("Admin Services - Volunteer Event".$titleAddOn);

if(!$vDateLoadError) {
	?>
	<div class="text-indent">
	<?php
		if($currentVDate) {
			?>
			Back to <a href="?page=adminManageVolunteerDates">Current Events</a>
			<br/>
			<br/>
			<?php
		}
		else {
			?>
			Back to <a href="javascript:viewArchivedVolunteerDates('<?php echo addslashes($vDate['type']); ?>');">Archived Events</a>
			<form method="get" action="" name="adminViewArchivedVolunteerDatesForm" id="adminViewArchivedVolunteerDatesForm">
				<input type="hidden" name="page" id="page" value="adminManageVolunteerDates">
				<input type="hidden" name="adminViewArchivedVolunteerDates" id="adminViewArchivedVolunteerDates" value="true"/>
				<input type="hidden" name="adminViewArchivedVolunteerDates_eventType" id="adminViewArchivedVolunteerDates_eventType" value=""/>
			</form>
			<br/>
			<?php
		}
	?>
	</div>
	<?php
	$formattedDate = formatDateAsText_monthDayYear($vDate['date']);
	$scheduled = mysqli_fetch_assoc( getCountVolunteersForVolunteerDateById($vDate['id']) );
	$confirmed = mysqli_fetch_assoc( getCountVolunteersConfirmedForVolunteerDateById($vDate['id']) );
	$showed = getCountVolunteersShowedForVolunteerDateById($vDate['id']);
	$showed['NR'] = (int)$scheduled['count(*)'] - ((int)$showed['yes'] + (int)$showed['no']);
	?>
	<form class="table-indent" method="get" action="" name="adminUpdateVolunteersDateForm" id="adminUpdateVolunteersDateForm">
		<input type="hidden" name="page" id="page" value="adminManageVolunteerDate">
		<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
		<input type="hidden" name="manageVDateId" id="manageVDateId" value="<?php echo htmlentities($_REQUEST['manageVDateId']); ?>">
		<table class="adminManageVolunteerDate" style="margin-bottom:10px; table-layout:fixed; word-wrap:break-word;">
			<tr>
				<td class="adminManageVolunteerDateLeft">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">Type:</td>
				<td>
					<?php echo htmlentities($vDate['type']); ?>
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">Location:</td>
				<td>
					<?php echo htmlentities($vDate['location']); ?>
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">Date:</td>
				<td>
					<?php echo htmlentities($formattedDate); ?>
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">Start Time:</td>
				<td>
					<?php
						if($currentVDate) {
							?>
							<input name="startHour" id="startHour" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['startHour']); ?>">
							<?php
								echo ":";
							?>
							<input name="startMinutes" id="startMinutes" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['startMinutes']); ?>">&nbsp;<input type ="radio" name="startTimeAmPm" value="am" <?php echo $startAmChecked ?>>AM&nbsp;<input type ="radio" name="startTimeAmPm" value="pm" <?php echo $startPmChecked ?>>PM
							<?php
						}
						else {
							echo htmlentities($startTime);
						}
					?>
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">End Time:</td>
				<td>
					<?php
						if($currentVDate) {
							 ?>
					<input name="endHour" id="endHour" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['endHour']); ?>">
					<?php
						echo ":";
					?>
					<input name="endMinutes" id="endMinutes" maxlength="2" style="width: 18px;" value="<?php echo htmlentities($_REQUEST['endMinutes']); ?>">&nbsp;<input type ="radio" name="endTimeAmPm" value="am" <?php echo $endAmChecked ?>>AM&nbsp;<input type ="radio" name="endTimeAmPm" value="pm" <?php echo $endPmChecked ?>>PM
					<?php
						}
						else {
							echo htmlentities($endTime);
						}
					?>				
				</td>
			</tr>
			<tr>
				<td class="adminManageVolunteerDateLeft">Volunteers Needed:</td>
				<td <?php if($currentVDate) { echo "style='vertical-align:inherit;'"; } ?>>
					<?php
					if($currentVDate) {
						?>
						<input name="volunteersNeeded" id="volunteersNeeded" maxlength="3" style="width: 23px;" value="<?php echo htmlentities($_REQUEST['volunteersNeeded']); ?>">
						<?php
					}
					else {
						echo htmlentities($vDate['volunteersNeeded']);
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
					<?php 
						echo $showed['yes']." / ".$showed['no']." / ".$showed['NR'];
					?>
				</td>
			</tr>
			<tr>
				<?php
				if($currentVDate) {
					?>
					<td class="adminManageVolunteerDateLeft"><input type="submit" name="adminUpdateVolunteersDateSubmit" id="adminUpdateVolunteersDateSubmit" value="Save"/></td>
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
	$volunteers = getVolunteersByVolunteerDateId($vDate['id']);
	?>
	<table class="text-indent" style="border-collapse:collapse; margin-bottom:5px; width:618px;">
		<tr>
			<td><h2 style="margin:0px;">Scheduled Volunteers</h2></td>
			<td style="text-align:right;">Download Schedule: <a href="javascript:downloadSchedule('xls');">Excel</a>, <a href="javascript:downloadSchedule('html');">HTML</a></td>
		</tr>
	</table>
	<form class="table-indent" method="get" action="" name="adminManageVolunteersForm" id="adminManageVolunteersForm">
		<input type="hidden" name="page" id="page" value="adminManageVolunteerDate">
		<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
		<input type="hidden" name="manageVDateId" id="manageVDateId" value="<?php echo htmlentities($_REQUEST['manageVDateId']); ?>">
		<input type="hidden" name="isSpecialEventVolunteerDate" id="isSpecialEventVolunteerDate" value="0"/>
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
				if($currentVDate) {
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
					echo "	<td><center><a href=\"javascript:removeVolunteerSignup('".addslashes($volunteer['uname'])."', '".addslashes($volunteer['volunteerDateId'])."');\" class='deleteImgLink'><img src='images/pageElements/delete.svg'/></a></center></td>";
					echo "</tr>";
				}
			}
			?>
		</table>
		&nbsp;* Indicates reminder / confirm email has been sent.<br>
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
				if($currentVDate) {
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

	<table class="text-indent" style="border-collapse:collapse; margin-bottom:5px; width:618px;">
		<tr>
			<td>
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
							<input type="hidden" name="manageVDateId" id="manageVDateId" value="<?php echo htmlentities($_REQUEST['manageVDateId']); ?>">
							<input type="hidden" name="page" id="page" value="adminManageVolunteerDate">
							<button type="submit" id="adminCreateAcctSubmit" name="adminCreateAcctSubmit">Add Volunteer</button>
						</div>
					</form>
				</div>
			</td>
		</tr>
	</table>

	<form method="get" action="" name="adminManageAcctsEditAcctForm" id="adminManageAcctsEditAcctForm">
		<input type="hidden" name="page" id="page" value="adminManageAcct">
		<input type="hidden" name="editAcctUname" id="editAcctUname" value=""/>
	</form>
	<?php
	// if($currentVDate) {
		?>
		<form method="get" action="" name="adminRemoveSignupForm" id="adminRemoveSignupForm">
			<input type="hidden" name="page" id="page" value="adminManageVolunteerDate">
			<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
			<input type="hidden" name="manageVDateId" id="manageVDateId" value="<?php echo htmlentities($_REQUEST['manageVDateId']); ?>">
			<input type="hidden" name="removeSignup" id="removeSignup" value="false"/>
			<input type="hidden" name="removeSignupUname" id="removeSignupUname" value=""/>
			<input type="hidden" name="removeSignupVDateId" id="removeSignupVDateId" value=""/>
		</form>
		<?php
	// }
	?>
	<form method="get" action="" target="downloadSchedule<?php echo $vDate['id']; ?>" name="downloadScheduleForm" id="downloadScheduleForm">
		<input type="hidden" name="page" id="page" value="downloadVolunteerDateSchedule"/>
		<input type="hidden" name="downloadScheduleVDateId" id="downloadScheduleVDateId" value="<?php echo $vDate['id']; ?>"/>
		<input type="hidden" name="isSpecialEventVolunteerDate" id="isSpecialEventVolunteerDate" value="0"/>
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
		var checkboxes = document.adminManageVolunteersForm.unameSelect;
		if(typeof checkboxes.length == 'undefined') {
			checkboxes.checked = checkValue;
		}
		else {
			for(var i=0; i<checkboxes.length; i++) {
				checkboxes[i].checked = checkValue;
			}
		}
	}
	
	function viewArchivedVolunteerDates(type) {
		document.getElementById('adminViewArchivedVolunteerDates_eventType').value = type;
		document.getElementById('adminViewArchivedVolunteerDatesForm').submit();
	}
	
	function editAccount(editAcctUname) {
		document.getElementById('editAcctUname').value = editAcctUname;
		document.getElementById('adminManageAcctsEditAcctForm').submit();
	}

	<?php
	// if($currentVDate) {
		?>
		function removeVolunteerSignup(removeSignupUname, removeSignupVDateId) {
			parent.popupConfirm("<p>Delete sign-up?</p><p>"+removeSignupUname+"</p>").then(function(result){
				if( result === "OK" ) {
					document.getElementById('removeSignup').value = "true";
					document.getElementById('removeSignupUname').value = removeSignupUname;
					document.getElementById('removeSignupVDateId').value = removeSignupVDateId;
					document.getElementById('adminRemoveSignupForm').submit();
				}
			});
		}
		<?php
	// }
	?>
	
	function downloadSchedule(format) {
		if(format == 'html') {
			document.getElementById('downloadScheduleForm').submit();
		}
		else {
			window.location = "?page=downloadVolunteerDateSchedule&downloadScheduleVDateId=<?php echo $vDate['id']; ?>&isSpecialEventVolunteerDate=0&format=xls";
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
			var chkbox = document.adminManageVolunteersForm.unameSelect;
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
			document.getElementById('adminManageVolunteersForm').submit();
		}
		
		<?php
		if($currentVDate) {
			?>
			function sendReminderConfirmEmail() {
				parent.popupConfirm("Send reminder/confirm email to selected volunteers?").then(function(result){
					if( result === "OK" ) {
						setSelectedVolunteers();
						document.getElementById('volunteerAction').value = "sendReminderConfirmEmail";
						document.getElementById('adminManageVolunteersForm').submit();
					}
				});
			}
			
			function resendSignupEmail() {
				parent.popupConfirm("Resend initial sign-up email to selected volunteers?").then(function(result){
					if( result === "OK" ) {
						setSelectedVolunteers();
						document.getElementById('volunteerAction').value = "resendSignupEmail";
						document.getElementById('adminManageVolunteersForm').submit();
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
