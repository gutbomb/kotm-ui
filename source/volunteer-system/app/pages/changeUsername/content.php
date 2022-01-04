<?php

printPageMessagesAndTitle("Manage Account - Change Username");

?>
<form style="margin-left:19px;" method="get" action="" name="changeUsernameForm" id="changeUsernameForm">
	<input type="hidden" name="page" id="page" value="changeUsername">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<table class="changeUsername">
		<tr>
			<td>Current Username:</td>
			<td>
				<?php echo htmlentities($_SESSION['user']['uname']); ?>
			</td>
		</tr>
		<tr>
			<td>New Username:</td>
			<td>
				<input name="uname" id="uname" maxlength="100" style="width: 175px;" value="<?php echo htmlentities($_REQUEST['uname']); ?>"/>
				<span style="color:#999999; padding-left:2px;">(letters, numbers, underscore)</span>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type="button" name="cancel" value="Cancel" onclick="javascript:cancelChangeUsername();"/>
				<div class="spacer"></div>
				<input type="submit" name="changeUsernameSubmit" id="changeUsernameSubmit" value="Submit"/>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
function cancelChangeUsername() {
	document.getElementById('uname').value = "";
	document.location.href = "?page=manageAcct";
}
</script>