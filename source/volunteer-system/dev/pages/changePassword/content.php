<?php

printPageMessagesAndTitle("Manage Account - Change Password");

?>
<form style="margin-left:19px;" method="post" action="?page=changePassword" name="changePasswordForm" id="changePasswordForm">
	<table class="changePassword">
		<tr>
			<td>Current Password:</td>
			<td>
				<input type="password" name="password" id="password" maxlength="50" style="width: 175px;" value="">
			</td>
		</tr>
		<tr>
			<td>New Password:</td>
			<td>
				<input type="password" name="password1" id="password1" maxlength="50" style="width: 175px;" value="">
			</td>
		</tr>
		<tr>
			<td>Retype New Password:</td>
			<td>
				<input type="password" name="password2" id="password2" maxlength="50" style="width: 175px;" value="">
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type="button" name="cancel" value="Cancel" onclick="javascript:cancelChangePassword();"/>
				<div class="spacer"></div>
				<input type="submit" name="changePasswordSubmit" id="changePasswordSubmit" value="Submit"/>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
function cancelChangePassword() {
	document.getElementById('password').value = "";
	document.getElementById('password1').value = "";
	document.getElementById('password2').value = "";
	document.location.href = "?page=manageAcct";
}
</script>