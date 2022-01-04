<?php
if($_REQUEST['birthdate']) {
    $birthdateField = date("m/d/Y", strtotime($_REQUEST['birthdate']));
} else {
    $birthdateField = "";
}

?>
<script>
    var highestProgress = 0;
    var slideOptions = {
        duration: 300,
        progress: function(animation, progress, remainingMs){
            if(highestProgress < progress) {
                // Since there are multiple elements being slid, and the progress
                // function is called for each, only update the frame height if the
                // current element's progress is more than the last highest progress.
                // Prevents jitter if the elements don't progress at the same rate.
                // Also avoids wasting time/cycles, which could slow the animation.
                highestProgress = progress;
                // if(isHiding) {
                // 	parent.setIframeHeight( frameHeight - (elementHeight*progress) );
                // }
                // else {
                // 	parent.setIframeHeight( frameHeight + (elementHeight*progress) );
                // }
            }
        },
        complete: function(){
            // parent.resizeIframe();
        }
    };


    function checkAges(birthDate) {
        $("#createAcctSubmit").attr("disabled", false);
        $("#createAcctSubmit").removeClass('disabled');
        $("#under12").slideUp(slideOptions);
        $("#under18").slideUp(slideOptions);
        var years = new Date(new Date() - new Date(birthdate.value)).getFullYear() - 1970;
        if (years < 12) {
           $("#under12").slideDown(slideOptions);
           $("#createAcctSubmit").attr("disabled", true);
           $("#createAcctSubmit").addClass('disabled');
        } else if (years < 18) {
           $("#under18").slideDown(slideOptions);
        }
    }
    $( function() {
        $('#phone1, #phone2, #emergencyPhone').keydown(function (e) {
            var key = e.charCode || e.keyCode || 0;
            $text = $(this);
            if (key !== 8 && key !== 9) {
                if ($text.val().length === 3) {
                    $text.val($text.val() + '-');
                }
                if ($text.val().length === 7) {
                    $text.val($text.val() + '-');
                }

            }

            return (key == 8 || key == 9 || key == 46 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105));
        });
        var currentYear = (new Date).getFullYear();
        $( "#birthdate" ).datepicker({
            changeYear: true,
            yearRange: "1900:" + currentYear,
            dateFormat: "mm/dd/yy"
        });
    } );
    $.datepicker._generateMonthYearHeader_original = $.datepicker._generateMonthYearHeader;

    $.datepicker._generateMonthYearHeader = function(inst, dm, dy, mnd, mxd, s, mn, mns) {
        var header = $($.datepicker._generateMonthYearHeader_original(inst, dm, dy, mnd, mxd, s, mn, mns)),
            years = header.find('.ui-datepicker-year');

        // reverse the years
        years.html(Array.prototype.reverse.apply(years.children()));

        // return our new html
        return $('<div />').append(header).html();
    }
</script>
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
           <input type="text" id="birthdate" name="birthdate" value="<?php echo htmlentities($birthdateField); ?>" onchange="javascript:checkAges(this)" placeholder="mm/dd/yyyy">
        </td>
    </tr>
    <tr id="under12" style="display:none">
        <td></td>
        <td class="error">
            Volunteers must be at least 12 years old to create an account.
        </td>
    </tr>
    <tr id="under18" style="display:none">
        <td></td>
        <td class="error">
            All volunteers under 18 years old are required to print, sign, scan, and email <a href="/volunteer-system/app/images/pageElements/KOTM-Volunteer-sign-in-under-18-Agreement.pdf" target="_blank">this form</a> to the volunteer coordinator at <a href="mailto:volunteer@kotm.org">volunteer@kotm.org</a> or bring the printed form with you.
        </td>
    </tr>
	<tr>
		<td class="accountLeft">E-mail Address*</td>
		<td>
			<input type="email" name="email" id="email" maxlength="50" style="width: 175px;" value="<?php echo htmlentities($_REQUEST['email']); ?>">
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
		<td class="accountLeft">Mobile Phone*</td>
		<td>
            <input type="tel" id="phone1" name="phone1" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="xxx-xxx-xxxx" value="<?php echo htmlentities($_REQUEST['phone1']); ?>">
		</td>
	</tr>
	<tr>
		<td class="accountLeft">Alternate Phone</td>
		<td>
            <input type="tel" id="phone2" name="phone2" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="xxx-xxx-xxxx" value="<?php echo htmlentities($_REQUEST['phone2']); ?>">
		</td>
	</tr>
</table>


<!-- Emergency Contact -->
<table class="account account-mid">
    <tr>
        <td class="accountLeft" colspan="2"><strong>Emergency Contact</strong></td>
    </tr>
    <tr>
        <td class="accountLeft">Full Name*</td>
        <td>
            <input name="emergencyName" id="emergencyName" maxlength="50" style="width: 175px;" value="<?php echo htmlentities($_REQUEST['emergencyName']); ?>">
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
</table>