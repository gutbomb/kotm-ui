<?php

$specialEventDateLoadError = false;

if(isset($_REQUEST['signupSpecialEventDateId'])) {
	$_REQUEST['signupSpecialEventDateId'] = truncate(trim($_REQUEST['signupSpecialEventDateId']), 50);
	$specialEventDate = mysqli_fetch_assoc( getSpecialEventDateById($_REQUEST['signupSpecialEventDateId']) );
	if($specialEventDate == false) {
		setErrorMsg("Event not found.", false);
		$specialEventDateLoadError = true;
	}
}
else {
	//no volunteer date selected
	setErrorMsg("No event selected.", false);
	$specialEventDateLoadError = true;
}

if(!isset($_REQUEST['firstname'])) {
	// Load the initial data from the user's session into the form
	$_REQUEST['firstname'] = $_SESSION['user']['firstname'];
	$_REQUEST['lastname'] = $_SESSION['user']['lastname'];
	$_REQUEST['birthyear'] = substr($_SESSION['user']['birthdate'], 0, 4);
	$_REQUEST['birthmonth'] = substr($_SESSION['user']['birthdate'], 5, 2);
	$_REQUEST['birthday'] = substr($_SESSION['user']['birthdate'], 8, 2);
	$_REQUEST['email'] = $_SESSION['user']['email'];
	$_REQUEST['address1'] = $_SESSION['user']['address1'];
	$_REQUEST['address2'] = $_SESSION['user']['address2'];
	$_REQUEST['city'] = $_SESSION['user']['city'];
	$_REQUEST['state'] = $_SESSION['user']['state'];
	$_REQUEST['zip'] = $_SESSION['user']['zip'];
	$_REQUEST['phone1part1'] = substr($_SESSION['user']['phone1'], 0, 3);
	$_REQUEST['phone1part2'] = substr($_SESSION['user']['phone1'], 4, 3);
	$_REQUEST['phone1part3'] = substr($_SESSION['user']['phone1'], 8, 4);
	$_REQUEST['phone2part1'] = substr($_SESSION['user']['phone2'], 0, 3);
	$_REQUEST['phone2part2'] = substr($_SESSION['user']['phone2'], 4, 3);
	$_REQUEST['phone2part3'] = substr($_SESSION['user']['phone2'], 8, 4);
	$_REQUEST['student'] = ($_SESSION['user']['student'] == '1') ? 'yes' : 'no';
	$_REQUEST['school'] = $_SESSION['user']['school'];
}

printPageMessagesAndTitle("Confirm Your Sign-up<div class='spacer'></div><a href='?page=signup'>(cancel)</a>");

if(!$specialEventDateLoadError) {

	if($specialEventDate['hasStartEndTime'] == '0') {
		$formattedTime = "Anytime";
	}
	else {
		$formattedTime = htmlentities(strftime("%-l:%M %P", strtotime($specialEventDate['startTime'])))." - ".htmlentities(strftime("%-l:%M %P", strtotime($specialEventDate['endTime'])));
	}

	?>
	<div class="text-indent">
		<h2 style="color:#737373;">Special Event</h2>
		<table class="invisible-table text-indent" style="width:593px; table-layout:fixed; word-wrap:break-word;">
			<col style="width:90px;"/>
			<col style="width:503px;"/>
			<tr>
				<td style="vertical-align:top;">Date:</td>
				<td><?php echo htmlentities(formatDateAsText_monthDayYear($specialEventDate['date'])); ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Time:</td>
				<td><?php echo $formattedTime; ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Location:</td>
				<td><?php echo htmlentities($specialEventDate['location']); ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Description:</td>
				<td><?php echo htmlentities($specialEventDate['shortDescription']); ?></td>
			</tr>
			<tr>
				<td style="vertical-align:top;">Details:</td>
				<td><?php echo processMarkupForHtmlDisplay($specialEventDate['longDescription']); ?></td>
			</tr>
		</table>
	</div>
	<p class="text-indent" style="margin-top:20px; padding-bottom:0px; margin-bottom:-10px;">
		Please <u><b>update your account info</b></u> and <u><b>acknowledge the statements below</b></u>.
	</p>
	<form class="table-indent" method="get" action="" name="updateAcctInfoForm" id="updateAcctInfoForm">
	<input type="hidden" name="page" id="page" value="specialEventSignupConfirm">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<input type='hidden' name='signupSpecialEventDateId' id='signupSpecialEventDateId' value='<?php echo $specialEventDate['id']; ?>'/>

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
		document.getElementById('birthyear').value = "";
		document.getElementById('birthmonth').value = "";
		document.getElementById('birthday').value = "";
		document.getElementById('email').value = "";
		document.getElementById('address1').value = "";
		document.getElementById('address2').value = "";
		document.getElementById('city').value = "";
		document.getElementById('state').value = "";
		document.getElementById('zip').value = "";
		document.getElementById('phone1part1').value = "";
		document.getElementById('phone1part2').value = "";
		document.getElementById('phone1part3').value = "";
		document.getElementById('phone2part1').value = "";
		document.getElementById('phone2part2').value = "";
		document.getElementById('phone2part3').value = "";
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