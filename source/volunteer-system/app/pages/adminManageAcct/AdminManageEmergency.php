    <tr>
        <td colspan="2" class="accountLeft">
            <h2>Emergency Contact</h2>
        </td>
    </tr>
    <tr>
        <td class="accountLeft">Full Name*</td>
        <td>
            <input name="emergencyName" id="emergencyName" maxlength="100" style="width: 250px;" value="<?php echo htmlentities($_REQUEST['emergencyName']); ?>">
        </td>
    </tr>
    <tr>
        <td class="accountLeft">Relationship*</td>
        <td>
            <input name="emergencyRelationship" id="emergencyRelationship" maxlength="100" style="width: 250px;" value="<?php echo htmlentities($_REQUEST['emergencyRelationship']); ?>">
        </td>
    </tr>
	<tr>
		<td class="accountLeft">Phone*</td>
		<td>
            <input type="tel" id="emergencyPhone" name="emergencyPhone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="xxx-xxx-xxxx" value="<?php echo htmlentities($_REQUEST['emergencyPhone']); ?>">
		</td>
	</tr>
    <tr>
        <td class="accountLeft" style="padding-top:18px;">
            <input type="submit" name="adminEditAcctSubmit" id="adminEditAcctSubmit" value="Save"/>
        </td>
    </tr>
</table>