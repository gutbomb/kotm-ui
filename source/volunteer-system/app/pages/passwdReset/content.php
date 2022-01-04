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
                <input type="text" id="birthdate" name="birthdate" value="<?php echo htmlentities(date("m/d/Y", strtotime($_REQUEST['birthdate']))); ?>" placeholder="mm/dd/yyyy">
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
    $( function() {
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
	document.getElementById('birthdate').value = "";
	document.location.href = "?page=signup";	
}
</script>