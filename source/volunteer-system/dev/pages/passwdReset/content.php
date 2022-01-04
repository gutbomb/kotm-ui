<?php

printPageMessagesAndTitle("Reset Password");

?>
<form style="margin-left:19px;" method="get" action="" name="passwdResetForm" id="passwdResetForm">
	<input type="hidden" name="page" id="page" value="passwdReset">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<table class="passwdReset">
		<tr>
			<td colspan='2'>Enter the account username and date of birth.</td>
		</tr>
		<tr>
			<td>Username:</td>
			<td>
				<input name="uname" id="uname" maxlength="100" style="width: 175px;" value="<?php echo htmlentities($_REQUEST['uname']); ?>"/>
			</td>
		</tr>
		<tr>
			<td>Date of Birth:</td>
			<td>
				<input type="hidden" id="birthmonthLength">
				<input type="hidden" id="birthdayLength">
				<input name="birthmonth" id="birthmonth" maxlength="2" style="width:18px;" value="<?php echo htmlentities($_REQUEST['birthmonth']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'birthday');"> /
				<input name="birthday" id="birthday" maxlength="2" style="width:18px;" value="<?php echo htmlentities($_REQUEST['birthday']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'birthyear');"> /
				<input name="birthyear" id="birthyear" maxlength="4" style="width:30px;" value="<?php echo htmlentities($_REQUEST['birthyear']); ?>"> (mm/dd/yyyy)
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type="button" name="cancel" value="Cancel" onclick="javascript:cancelPasswdReset();"/>
				<div class="spacer"></div>
				<input type="submit" name="passwdResetSubmit" id="passwdResetSubmit" value="Reset Password"/>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
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

function cancelPasswdReset() {
	document.getElementById('uname').value = "";
	document.getElementById('birthmonth').value = "";
	document.getElementById('birthday').value = "";
	document.getElementById('birthyear').value = "";
	document.location.href = "?page=signup";	
}
</script>