<?php

$vDateLoadError = false;

if(isset($_REQUEST['signupVDateId'])) {
	$_REQUEST['signupVDateId'] = truncate(trim($_REQUEST['signupVDateId']), 50);
	$vDate = mysqli_fetch_assoc( getVolunteerDateById($_REQUEST['signupVDateId']) );
	if($vDate == false) {
		setErrorMsg("Event not found.", false);
		$vDateLoadError = true;
	}
}
else {
	//no volunteer date selected
	setErrorMsg("No event selected.", false);
	$vDateLoadError = true;
}

if(!isset($_REQUEST['firstname'])) {
	// Load the initial data from the user's session into the form
	$_REQUEST['firstname'] = $_SESSION['user']['firstname'];
	$_REQUEST['lastname'] = $_SESSION['user']['lastname'];
	$_REQUEST['birthdate'] = $_SESSION['user']['birthdate'];
	$_REQUEST['email'] = $_SESSION['user']['email'];
	$_REQUEST['address1'] = $_SESSION['user']['address1'];
	$_REQUEST['address2'] = $_SESSION['user']['address2'];
	$_REQUEST['city'] = $_SESSION['user']['city'];
	$_REQUEST['state'] = $_SESSION['user']['state'];
	$_REQUEST['zip'] = $_SESSION['user']['zip'];
    $_REQUEST['phone1'] = $_SESSION['user']['phone1'];
    $_REQUEST['phone2'] = $_SESSION['user']['phone2'];
    $_REQUEST['emergencyName'] = $_SESSION['user']['emergencyName'];
    $_REQUEST['emergencyRelationship'] = $_SESSION['user']['emergencyRelationship'];
    $_REQUEST['emergencyPhone'] = $_SESSION['user']['emergencyPhone'];
	$_REQUEST['student'] = ($_SESSION['user']['student'] == '1') ? 'yes' : 'no';
	$_REQUEST['school'] = $_SESSION['user']['school'];
}

printPageMessagesAndTitle("Confirm Your Sign-up<div class='spacer'></div><a href='?page=signup'>(cancel)</a>");

if(!$vDateLoadError) {
	?>
	<div class="text-indent">
		<h2 style="color:#737373;"><?php echo htmlentities($vDate['type']); ?></h2>
		<table class="invisible-table text-indent" style="width:593px; table-layout:fixed; word-wrap:break-word;">
			<col style="width:75px;"/>
			<col style="width:518px;"/>
			<tr>
				<td style="vertical-align:top;">Date:</td>
				<td><?php echo htmlentities(formatDateAsText_monthDayYear($vDate['date'])); ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Time:</td>
				<td><?php echo htmlentities(strftime("%-l:%M %P", strtotime($vDate['startTime'])))." - ".htmlentities(strftime("%-l:%M %P", strtotime($vDate['endTime']))); ?></td>
			</tr>
			<tr>
				<td  style="vertical-align:top;">Location:</td>
				<td><?php echo htmlentities($vDate['location']); ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top; padding-top:6px;">Address:</td>
				<td style="padding-top:6px;"><?php echo getLocationAddress($vDate['location'], "<br/>"); ?></td>
			</tr>
		</table>
	</div>
	<p class="text-indent" style="margin-top:20px; padding-bottom:0px; margin-bottom:-10px;">
		Please <u><b>update your account info</b></u> and <u><b>acknowledge the statements below</b></u>.
	</p>
	<form class="table-indent" method="get" action="" name="updateAcctInfoForm" id="updateAcctInfoForm">
	<input type="hidden" name="page" id="page" value="signupConfirm">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<input type='hidden' name='signupVDateId' id='signupVDateId' value='<?php echo $vDate['id']; ?>'/>

	<?php
	require_once('common/forms/RequiredFieldsCell.php');
	require_once('common/forms/StudentInfo.php');
	require_once('common/forms/NameAddressEtc.php');
	$USE_BULLETS_FOR_AGREEMENTS = true;
	require_once('common/forms/Agreements.php');
	?>
	
	<table class="accountSubmit" style="padding-top:10px; text-align:right;">
		<tr>
			<td style="text-align:left;">
				<input type="checkbox" style="cursor:pointer;" name="agreeAll" id="agreeAll" value="yes"<?php echo getCheckedStatus("agreeAll"); ?>>
				<div style="display:inline-block; cursor:pointer;" onclick="javascript:toggleCheckbox();">I have read and affirm/agree with all of the above statements.*</div>
			</td>
			<td>
				<input type="button" name="cancel" value="Cancel" onclick="javascript:cancelUpdateAcctInfo();"/>
				<div class="spacer"></div>
				<input type="submit" name="updateAcctInfoSubmit" id="updateAcctInfoSubmit" value="Confirm Sign-Up"/>
			</td>
		</tr>
	</table>
	
	</form>
	
	<script type="text/javascript">
	var options = document.getElementById('state').getElementsByTagName('*');
	for(var i=0; i<options.length; i++) {
		var option = options[i];
		if( option.value == "<?php echo addslashes($_REQUEST['state']); ?>" ) {
			option.selected = true;
			break;
		}
	}
	
	function setFieldLength(field) {
		document.getElementById(field.id+'Length').value = field.value.length;
	}
	
	function changeFocus(currElem, nextElemId) {
		var nextElem = document.getElementById(nextElemId);
		if( currElem.value.length == currElem.maxLength && 
			document.getElementById(currElem.id+'Length').value < currElem.maxLength &&
			nextElem.value.length == 0 ) {
			
			nextElem.focus();
		}
	}
	
	function toggleCheckbox() {
		$('#agreeAll').prop("checked", !$('#agreeAll').prop("checked"));
	}
	
	function cancelUpdateAcctInfo() {
		document.getElementById('firstname').value = "";
		document.getElementById('lastname').value = "";
		document.getElementById('birthdate').value = "";
		document.getElementById('email').value = "";
		document.getElementById('address1').value = "";
		document.getElementById('address2').value = "";
		document.getElementById('city').value = "";
		document.getElementById('state').value = "";
		document.getElementById('zip').value = "";
        document.getElementById('phone1').value = "";
        document.getElementById('phone2').value = "";
        document.getElementById('emergencyName').value = "";
        document.getElementById('emergencyRelationship').value = "";
        document.getElementById('emergencyPhone').value = "";
		document.getElementById('studentYes').checked = false;
		document.getElementById('studentNo').checked = false;
		document.getElementById('school').value = "";
	
		document.getElementById('agreeAll').checked = false;
		
		document.location.href = "?page=signup";
	}
	</script>
	
<?php
}
?>