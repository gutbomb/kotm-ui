<?php

printPageMessagesAndTitle("Create Account<div class='spacer'></div><a href='?page=signup'>(cancel)</a>");

?>
<p class="text-indent" style="margin-bottom:-2px;"><?php require_once('common/dataDisclaimer.php'); ?></p>

<form method="post" class="table-indent" action="?page=createAcct" name="createAcctForm" id="createAcctForm">

<style>
table.accountAgreements {
	padding-bottom: 0px;
	border-bottom: none;
}
table.studentInfo {
	border-top: none;
}
</style>

<?php
require_once('common/forms/RequiredFieldsCell.php');
require_once('common/forms/UsernamePassword.php');
require_once('common/forms/StudentInfo.php');
require_once('common/forms/NameAddressEtc.php');
require_once('common/forms/Agreements.php');
require_once('common/forms/HowHeardAbout.php');
?>

<table class="accountSubmit" style="padding-top:10px; text-align:right;">
	<tr>
		<td colspan="2">
			<input type="button" name="cancel" value="Cancel" onclick="javascript:cancelCreateAcct();"/>
			<div class="spacer"></div>
			<input type="submit" name="createAcctSubmit" id="createAcctSubmit" value="Submit"/>
		</td>
	</tr>
</table>

</form>

<script type="text/javascript">
var options = document.getElementById('state').getElementsByTagName('*');
for(var i=0; i<options.length; i++) {
	var option = options[i];
	if( option.value == "<?php echo addslashes($_REQUEST['state']); ?>" ) {
		option.selected = true;
		break;
	}
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

function cancelCreateAcct() {
	document.getElementById('uname').value = "";
	document.getElementById('password1').value = "";
	document.getElementById('password2').value = "";
	document.getElementById('firstname').value = "";
	document.getElementById('lastname').value = "";
	document.getElementById('birthyear').value = "";
	document.getElementById('birthmonth').value = "";
	document.getElementById('birthday').value = "";
	document.getElementById('email').value = "";
	document.getElementById('address1').value = "";
	document.getElementById('address2').value = "";
	document.getElementById('city').value = "";
	document.getElementById('state').value = "";
	document.getElementById('zip').value = "";
	document.getElementById('phone1part1').value = "";
	document.getElementById('phone1part2').value = "";
	document.getElementById('phone1part3').value = "";
	document.getElementById('phone2part1').value = "";
	document.getElementById('phone2part2').value = "";
	document.getElementById('phone2part3').value = "";
	document.getElementById('studentYes').checked = false;
	document.getElementById('studentNo').checked = false;
	document.getElementById('school').value = "";
	document.getElementById('howHeardAbout').value = "";
	
	document.getElementById('arrestedAffirm').checked = false;
	document.getElementById('sexOffenderAgree').checked = false;
	document.getElementById('drugAffirm').checked = false;
	document.getElementById('soundMindAgree').checked = false;
	document.getElementById('noPhotoAgree').checked = false;
	document.getElementById('noInfoAgree').checked = false;
	document.getElementById('usePhotoAgree').checked = false;
	document.getElementById('kotmAgree').checked = false;
	document.getElementById('notResponsibleAgree').checked = false;
	
	document.location.href = "?page=signup";
}
</script>