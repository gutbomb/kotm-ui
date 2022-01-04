<?php

if(!isset($_REQUEST['firstname'])) {
	// Load the initial data from the user's session into the form
	$_REQUEST['firstname'] = $_SESSION['user']['firstname'];
	$_REQUEST['lastname'] = $_SESSION['user']['lastname'];
	$_REQUEST['birthdate'] = $_SESSION['user']['birthdate'];
	$_REQUEST['email'] = $_SESSION['user']['email'];
	$_REQUEST['address1'] = $_SESSION['user']['address1'];
	$_REQUEST['address2'] = $_SESSION['user']['address2'];
	$_REQUEST['city'] = $_SESSION['user']['city'];
	$_REQUEST['state'] = $_SESSION['user']['state'];
	$_REQUEST['zip'] = $_SESSION['user']['zip'];
	$_REQUEST['phone1'] = $_SESSION['user']['phone1'];
    $_REQUEST['phone2'] = $_SESSION['user']['phone2'];
    $_REQUEST['emergencyName'] = $_SESSION['user']['emergencyName'];
    $_REQUEST['emergencyRelationship'] = $_SESSION['user']['emergencyRelationship'];
    $_REQUEST['emergencyPhone'] = $_SESSION['user']['emergencyPhone'];
	$_REQUEST['student'] = ($_SESSION['user']['student'] == '1') ? 'yes' : 'no';
	$_REQUEST['school'] = $_SESSION['user']['school'];
	$_REQUEST['howHeardAbout'] = $_SESSION['user']['howHeardAbout'];
}

printPageMessagesAndTitle("Manage Account - Update Account Information");

?>
<p class="text-indent" style="margin-bottom:-2px;"><?php require_once('common/dataDisclaimer.php'); ?></p>
<form class="table-indent" method="get" action="" name="updateAcctInfoForm" id="updateAcctInfoForm">
<input type="hidden" name="page" id="page" value="updateAcctInfo">
<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">

<?php
require_once('common/forms/RequiredFieldsCell.php');
require_once('common/forms/StudentInfo.php');
require_once('common/forms/NameAddressEtc.php');
require_once('common/forms/HowHeardAbout.php');
?>

<table class="accountSubmit" style="padding-top:10px; text-align:right;">
	<tr>
		<td colspan="2">
			<input type="button" name="cancel" value="Cancel" onclick="javascript:cancelUpdateAcctInfo();"/>
			<div class="spacer"></div>
			<input type="submit" name="updateAcctInfoSubmit" id="updateAcctInfoSubmit" value="Save"/>
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

function cancelUpdateAcctInfo() {
	document.getElementById('firstname').value = "";
	document.getElementById('lastname').value = "";
	document.getElementById('birthdate').value = "";
	document.getElementById('email').value = "";
	document.getElementById('address1').value = "";
	document.getElementById('address2').value = "";
	document.getElementById('city').value = "";
	document.getElementById('state').value = "";
	document.getElementById('zip').value = "";
	document.getElementById('phone1').value = "";
	document.getElementById('phone2').value = "";
    document.getElementById('emergencyName').value = "";
    document.getElementById('emergencyRelationship').value = "";
    document.getElementById('emergencyPhone').value = "";
	document.getElementById('studentYes').checked = false;
	document.getElementById('studentNo').checked = false;
	document.getElementById('school').value = "";
	document.getElementById('howHeardAbout').value = "";
	document.location.href = "?page=manageAcct";
}
</script>