<table class="account account-mid">
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
			<input type="hidden" id="birthmonthLength">
			<input type="hidden" id="birthdayLength">
			<input name="birthmonth" id="birthmonth" maxlength="2" style="width:18px;" value="<?php echo htmlentities($_REQUEST['birthmonth']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'birthday');"> /
			<input name="birthday" id="birthday" maxlength="2" style="width:18px;" value="<?php echo htmlentities($_REQUEST['birthday']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'birthyear');"> /
			<input name="birthyear" id="birthyear" maxlength="4" style="width:30px;" value="<?php echo htmlentities($_REQUEST['birthyear']); ?>"> (mm/dd/yyyy)
		</td>
	</tr>
	<tr>
		<td class="accountLeft">E-mail Address*</td>
		<td>
			<input name="email" id="email" maxlength="50" style="width: 175px;" value="<?php echo htmlentities($_REQUEST['email']); ?>">
		</td>
	</tr>
</table>
<table class="account account-mid">
	<tr>
		<td class="accountLeft">Address 1*</td>
		<td>
			<input name="address1" id="address1" maxlength="100" style="width: 250px;" value="<?php echo htmlentities($_REQUEST['address1']); ?>">
		</td>
	</tr>
	<tr>
		<td class="accountLeft">Address 2</td>
		<td>
			<input name="address2" id="address2" maxlength="100" style="width: 250px;" value="<?php echo htmlentities($_REQUEST['address2']); ?>">
		</td>
	</tr>
	<tr>
		<td class="accountLeft">City, State, Zip*</td>
		<td>
			<input name="city" id="city" maxlength="50" style="width: 127px;" value="<?php echo htmlentities($_REQUEST['city']); ?>">
			<select name="state" id="state" style="width: 49px;"><option value=""></option><option value="AL">AL</option><option value="AK">AK</option><option value="AZ">AZ</option><option value="AR">AR</option><option value="CA">CA</option><option value="CO">CO</option><option value="CT">CT</option><option value="DE">DE</option><option value="DC">DC</option><option value="FL">FL</option><option value="GA">GA</option><option value="HI">HI</option><option value="ID">ID</option><option value="IL">IL</option><option value="IN">IN</option><option value="IA">IA</option><option value="KS">KS</option><option value="KY">KY</option><option value="LA">LA</option><option value="ME">ME</option><option value="MD">MD</option><option value="MA">MA</option><option value="MI">MI</option><option value="MN">MN</option><option value="MS">MS</option><option value="MO">MO</option><option value="MT">MT</option><option value="NE">NE</option><option value="NV">NV</option><option value="NH">NH</option><option value="NJ">NJ</option><option value="NM">NM</option><option value="NY">NY</option><option value="NC">NC</option><option value="ND">ND</option><option value="OH">OH</option><option value="OK">OK</option><option value="OR">OR</option><option value="PA">PA</option><option value="RI">RI</option><option value="SC">SC</option><option value="SD">SD</option><option value="TN">TN</option><option value="TX">TX</option><option value="UT">UT</option><option value="VT">VT</option><option value="VA">VA</option><option value="WA">WA</option><option value="WV">WV</option><option value="WI">WI</option><option value="WY">WY</option></select>
			<input name="zip" id="zip" maxlength="5" style="width: 64px;" value="<?php echo htmlentities($_REQUEST['zip']); ?>">
		</td>
	</tr>
	<tr>
		<td class="accountLeft">Primany Phone*</td>
		<td>
			<input type="hidden" id="phone1part1Length">
			<input type="hidden" id="phone1part2Length">
			<input name="phone1part1" id="phone1part1" maxlength="3" style="width: 23px;" value="<?php echo htmlentities($_REQUEST['phone1part1']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'phone1part2');"> -
			<input name="phone1part2" id="phone1part2" maxlength="3" style="width: 23px;" value="<?php echo htmlentities($_REQUEST['phone1part2']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'phone1part3');"> -
			<input name="phone1part3" id="phone1part3" maxlength="4" style="width: 30px;" value="<?php echo htmlentities($_REQUEST['phone1part3']); ?>">
		</td>
	</tr>
	<tr>
		<td class="accountLeft">Alternate Phone</td>
		<td>
			<input type="hidden" id="phone2part1Length">
			<input type="hidden" id="phone2part2Length">
			<input name="phone2part1" id="phone2part1" maxlength="3" style="width: 23px;" value="<?php echo htmlentities($_REQUEST['phone2part1']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'phone2part2');"> -
			<input name="phone2part2" id="phone2part2" maxlength="3" style="width: 23px;" value="<?php echo htmlentities($_REQUEST['phone2part2']); ?>" onkeydown="javascript:setFieldLength(this);" onkeyup="javascript:changeFocus(this, 'phone2part3');"> -
			<input name="phone2part3" id="phone2part3" maxlength="4" style="width: 30px;" value="<?php echo htmlentities($_REQUEST['phone2part3']); ?>">
		</td>
	</tr>
</table>