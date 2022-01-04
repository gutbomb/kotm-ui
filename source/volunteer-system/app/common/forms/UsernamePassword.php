<table class="account account-top">
	<tr>
		<td class="accountLeft">Username*</td>
		<td>
			<input name="uname" id="uname" maxlength="100" style="width: 175px;" value="<?php echo htmlentities($_REQUEST['uname']); ?>">
			<span style="color:#999999; padding-left:2px;">(letters, numbers, underscore)</span>
		</td>
	</tr>
	<tr>
		<td class="accountLeft">Password*</td>
		<td>
			<input type="password" name="password1" id="password1" maxlength="50" style="width: 175px;" value="">
		</td>
	</tr>
	<tr>
		<td class="accountLeft">Retype Password*</td>
		<td>
			<input type="password" name="password2" id="password2" maxlength="50" style="width: 175px;" value="">
		</td>
	</tr>
</table>