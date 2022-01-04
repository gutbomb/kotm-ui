<table class="account account-top">
	<tr>
		<td class="accountLeft">First Name*</td>
		<td>
			<input name="firstname" id="firstname" maxlength="50" style="width: 175px;" value="<?php echo htmlentities($_REQUEST['firstname']); ?>">
		</td>
	</tr>
	<tr>
		<td class="accountLeft">Last Name*</td>
		<td>
			<input name="lastname" id="lastname" maxlength="50" style="width: 175px;" value="<?php echo htmlentities($_REQUEST['lastname']); ?>">
		</td>
	</tr>
    <tr>
        <td class="accountLeft">Date of Birth*</td>
        <td>
            <input type="text" id="birthdate" name="birthdate" value="<?php echo htmlentities(date("m/d/Y", strtotime($_REQUEST['birthdate']))); ?>" placeholder="mm/dd/yyyy">
        </td>
    </tr>
	<tr>
		<td class="accountLeft">Age</td>
		<td>
			<?php echo htmlentities(calculateAgeFromBirthdate($acct['birthdate'])); ?>
		</td>
	</tr>
	<tr>
		<td class="accountLeft">E-mail Address*</td>
		<td>
			<input name="email" id="email" maxlength="50" style="width: 175px;" value="<?php echo htmlentities($_REQUEST['email']); ?>">
		</td>
	</tr>
	<tr>
		<td class="accountLeft">Admin*</td>
		<td>
			Yes: 
			<input type="radio" name="admin" id="adminYes" value="yes" <?php if($_REQUEST['admin'] == "yes") echo "checked"; ?>>
			&nbsp;No: 
			<input type="radio" name="admin" id="adminNo" value="no" <?php if($_REQUEST['admin'] == "no") echo "checked"; ?>>
		</td>
	</tr>
</table>