<form class="table-indent" style="margin-top:30px;" method="post" action="?page=adminManageAcct" name="adminPasswdResetForm" id="adminPasswdResetForm">
	<input type="hidden" name="editAcctUname" id="editAcctUname1" value="<?php echo htmlentities($_REQUEST['editAcctUname']); ?>">
	<table class="account account-bottom" style="border-width:1px; border-spacing:5px; border-style:solid; border-color:#d1d1d1; table-layout:fixed; word-wrap:break-word;">
		<tr>
			<td class="accountLeft" style="vertical-align:top;">Username</td>
			<td>
				<?php echo htmlentities($acct['uname']); ?>
			</td>
		</tr>
		<tr>
			<td class="accountLeft">Password</td>
			<td>
				<input type="password" name="password1" id="password1" maxlength="50" style="width: 175px;" value="">
			</td>
		</tr>
		<tr>
			<td class="accountLeft">Retype Password</td>
			<td>
				<input type="password" name="password2" id="password2" maxlength="50" style="width: 175px;" value="">
			</td>
		</tr>
		<tr>
			<td class="accountLeft"></td>
			<td>
				<input type="submit" name="adminPasswdResetSubmit" id="adminPasswdResetSubmit" value="Change Password"/>
			</td>
		</tr>
		<tr>
			<td class="accountLeft">Account Created</td>
			<td>
				<?php echo htmlentities( formatDateAsText_monthDayYear($acct['createDate'], true) ); ?> (MT)
			</td>
		</tr>
		<tr>
			<td class="accountLeft">Last Login</td>
			<td>
				<?php 
				if($acct['lastLoginDate'] == "") { echo "Never"; } else { echo htmlentities( formatDateAsText_monthDayYear($acct['lastLoginDate'], true) ); } ?> (MT)
			</td>
		</tr>
	</table>
</form>