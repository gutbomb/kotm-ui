<?php

unset($acctsDbResult);

$filterAdmin = false;
if(isset($_REQUEST['filterAdmin']) && $_REQUEST['filterAdmin'] == 'true') {
	$filterAdmin = true;
}

$totalAccts = 0;
if(isset($_REQUEST['showAll']) && $_REQUEST['showAll'] == 'true') {
	setSkipScrollTop(true);
	$acctsDbResult = getAllAccounts_limit500();
	$totalAccts = getCountAllAccounts();
}
else if(isset($_REQUEST['searchBy']) && $_REQUEST['searchBy'] == "uname") {
	setSkipScrollTop(true);
	$_REQUEST['uname'] = truncate(trim($_REQUEST['uname']), 100);
	if($_REQUEST['searchType'] == "flexible") {
		$acctsDbResult = getAccountsByUname_limit500($_REQUEST['uname'], $filterAdmin);
		$totalAccts = getCountAccountsByUname($_REQUEST['uname'], $filterAdmin);
	}
	else {
		$acctsDbResult = getAccountByUname($_REQUEST['uname'], $filterAdmin);
	}
}
else if(isset($_REQUEST['searchBy']) && $_REQUEST['searchBy'] == "name") {
	setSkipScrollTop(true);
	$_REQUEST['firstname'] = truncate(trim($_REQUEST['firstname']), 50);
	$_REQUEST['lastname'] = truncate(trim($_REQUEST['lastname']), 50);
	if($_REQUEST['searchType'] == "flexible") {
		$acctsDbResult = getAccountsByName_limit500($_REQUEST['firstname'], $_REQUEST['lastname'], $filterAdmin);
		$totalAccts = getCountAccountsByName($_REQUEST['firstname'], $_REQUEST['lastname'], $filterAdmin);
	}
	else {
		$acctsDbResult = getAccountByName_limit500($_REQUEST['firstname'], $_REQUEST['lastname'], $filterAdmin);
		$totalAccts = getCountAccountByName($_REQUEST['firstname'], $_REQUEST['lastname'], $filterAdmin);
	}
}

$accts = array();

if(isset($acctsDbResult)) {
	while( $acct = mysqli_fetch_assoc($acctsDbResult) ) {
		$accts[] = $acct;
	}
}

if(isset($_REQUEST['searchBy'])) {
	$searchBy = $_REQUEST['searchBy'];
}
else {
	$searchBy = "uname";
}
if(isset($_REQUEST['searchType'])) {
	$searchType = $_REQUEST['searchType'];
}
else {
	$searchType = "flexible";
}

function getSearchBySelectedDesignation($option) {
	global $searchBy;
	if($searchBy == $option) {
		return " selected";
	}
	return "";
}

function getSearchTypeSelectedDesignation($option) {
	global $searchType;
	if($searchType == $option) {
		return " selected";
	}
	return "";
}

function getMessageVisibility($by, $type) {
	global $searchBy, $searchType;
	if($searchBy == $by && $searchType == $type) {
		return "block";
	}
	return "none";
}

function getSearchInputVisibility($by) {
	global $searchBy;
	if($searchBy == $by) {
		return "block";
	}
	return "none";
}

printPageMessagesAndTitle("Admin Services - User Accounts");

?>
<form method="get" style="margin-left:19px;" action="" name="adminManageAcctsForm" id="adminManageAcctsForm" style="padding-bottom:10px;">
	<input type="hidden" name="page" id="page" value="adminManageAccts">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<input type="hidden" name="showAll" id="showAll" value="false"/>
	<input type="hidden" name="deleteAcct" id="deleteAcct" value="false"/>
	<input type="hidden" name="deleteAcctUname" id="deleteAcctUname" value=""/>
	<table class="adminManageAcctsSearch">
		<tr>
			<td>
				Search By: <select name="searchBy" id="searchBy" onchange="javascript:showHideSearch();"><option value="uname"<?php echo getSearchBySelectedDesignation("uname"); ?>>Username</option><option value="name"<?php echo getSearchBySelectedDesignation("name"); ?>>Name</option></select><div class="spacer"></div>
				Search Type: <select name="searchType" id="searchType" onchange="javascript:showHideSearch();"><option value="flexible"<?php echo getSearchTypeSelectedDesignation("flexible"); ?>>Flexible</option><option value="strict"<?php echo getSearchTypeSelectedDesignation("strict"); ?>>Strict</option></select>
			</td>
			<td rowspan="3" style="vertical-align:top; width:300px;">
				<div id="unameFlexibleDef" style="display:<?php echo getMessageVisibility("uname", "flexible"); ?>;">
					Find accounts where <b>username contains</b> the given text. E.g., 'a' would match 'ashton', 'bashton', and 'ira'. The search is <b>case-insensitive</b>.
				</div>
				<div id="unameStrictDef" style="display:<?php echo getMessageVisibility("uname", "strict"); ?>;">
					Find accounts where <b>username exactly matches</b> the given text (at most one will be returned). The search is <b>case-insensitive</b>.
				</div>
				<div id="nameFlexibleDef" style="display:<?php echo getMessageVisibility("name", "flexible"); ?>;">
					Find accounts where the <b>user's name contains</b> the given text. E.g., 'a' would match 'ashton', 'bashton', and 'ira'. The search is <b>case-insensitive</b>.
				</div>
				<div id="nameStrictDef" style="display:<?php echo getMessageVisibility("name", "strict"); ?>;">
					Find accounts where the <b>user's name exactly matches</b> the given text (multiple may be returned). The search is <b>case-insensitive</b>.
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="searchByUsername" style="display:<?php echo getSearchInputVisibility("uname"); ?>; width:250px;">
					Username: <input name="uname" id="uname" maxlength="100" style="width:175px;" value="<?php echo htmlentities($_REQUEST['uname']); ?>">
				</div>
				<div id="searchByName" style="display:<?php echo getSearchInputVisibility("name"); ?>; width:250px;">
					<table class="invisible-table">
						<tr>
							<td style="padding:0px; padding-right:2px;">First Name:</td>
							<td style="padding:0px;"><input name="firstname" id="firstname" maxlength="50" style="display:inline-block; width:154px; margin-bottom:3px;" value="<?php echo htmlentities($_REQUEST['firstname']); ?>"></td>
						</tr>
						<tr>
							<td style="padding:0px;">Last Name:</td>
							<td style="padding:0px;"><input name="lastname" id="lastname" maxlength="50" style="display:inline-block; width:154px;" value="<?php echo htmlentities($_REQUEST['lastname']); ?>"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td>Search Admins Only: <input type="checkbox" name="filterAdmin" id="filterAdmin" value="true" <?php if($filterAdmin){echo 'checked';} ?>></td>
		</tr>
		<tr>
			<td style="vertical-align:top;">
				<input type="submit" name="adminManageAcctsSubmit" id="adminManageAcctsSubmit" value="Search"/><div class="spacer"></div>
				<input type="button" name="showAllButton" id="showAllButton" value="Show All Accounts" onclick="javascript:showAllAccounts();"/>
			</td>
		</tr>
	</table>
</form>
<?php
if(isset($_REQUEST['searchBy'])) {
	echo "<div class='text-indent' style='margin-top:20px;'><b>Results: ";
	if( $totalAccts > count($accts) ) {
		echo $totalAccts."</b>";
		echo "&nbsp; (Showing first ".count($accts).")";
	}
	else {
		echo count($accts)."</b>";
		if(isset($_REQUEST['showAll']) && $_REQUEST['showAll'] == 'true') {
			echo "&nbsp; (Showing all accounts)";
		}
	}
	echo "</div>";
}
if(count($accts) > 0) {
	?>
	<table class="adminManageAccts table-indent" style="table-layout:fixed; word-wrap:break-word;">
		<col style="width:191px;"/>
		<col style="width:212px;"/>
		<col style="width:107px;"/>
		<col style="width:107px;"/>
		<tr>
			<th>Username</th>
			<th>Name</th>
			<th>Admin</th>
			<th>Delete</th>
		</tr>
		<?php
		for($i=0; $i<count($accts); $i++) {
			$acct = $accts[$i];
			$admin = ($acct['admin'] == '1') ? 'Yes' : 'No';
			echo "<tr>";
			echo "	<td><a href=\"javascript:editAccount('".addslashes($acct['uname'])."');\">".htmlentities($acct['uname'])."</a></td>";
			echo "	<td>".htmlentities($acct['lastname']).", ".htmlentities($acct['firstname'])."</td>";
			echo "	<td><center>".htmlentities($admin)."</center></td>";
			echo "	<td>";
			if(strtolower($acct['uname']) != strtolower($_SESSION['user']['uname'])) {
				echo "<center><a href=\"javascript:deleteAccount('".addslashes($acct['uname'])."');\" class='deleteImgLink'><img src='images/pageElements/delete.svg'/></a></center>";
			}
			else {
				// Add the delete image anyway to the row height matches all the others, but make it invisible and unclickable
				echo "<center><div class='deleteImgLink'><img style='opacity:0;' src='images/pageElements/delete.svg'/></div></center>";
			}
			echo "</td>";
			echo "</tr>";
		}
		?>
	</table>
	<?php
}
?>
<form method="get" action="" name="adminManageAcctsEditAcctForm" id="adminManageAcctsEditAcctForm">
	<input type="hidden" name="page" id="page" value="adminManageAcct">
	<input type="hidden" name="editAcctUname" id="editAcctUname" value=""/>
</form>
<script type="text/javascript">
// No-op most of the time, but on browser back, depending on form state, 
// browser may change the toggle without calling this function, so we call
// it here to make sure things are in sync in that specific case.
$(document).ready(showHideSearch);

function showHideSearch() {
	var selectedOption_searchBy;
	var options_searchBy = document.getElementById('searchBy').getElementsByTagName('*');
	for(var i=0; i<options_searchBy.length; i++) {
		if( options_searchBy[i].selected ) {
			selectedOption_searchBy = options_searchBy[i];
			break;
		}
	}
	var selectedOption_searchType;
	var options_searchType = document.getElementById('searchType').getElementsByTagName('*');
	for(var i=0; i<options_searchType.length; i++) {
		if( options_searchType[i].selected ) {
			selectedOption_searchType = options_searchType[i];
			break;
		}
	}
	if(selectedOption_searchBy.value == "uname") {
		document.getElementById('searchByName').style.display = "none";
		document.getElementById('searchByUsername').style.display = "block";
		if(selectedOption_searchType.value == "flexible") {
			document.getElementById('unameFlexibleDef').style.display = "block";
			document.getElementById('unameStrictDef').style.display = "none";
			document.getElementById('nameFlexibleDef').style.display = "none";
			document.getElementById('nameStrictDef').style.display = "none";
		}
		else {
			document.getElementById('unameFlexibleDef').style.display = "none";
			document.getElementById('unameStrictDef').style.display = "block";
			document.getElementById('nameFlexibleDef').style.display = "none";
			document.getElementById('nameStrictDef').style.display = "none";
		}
	}
	else {
		document.getElementById('searchByUsername').style.display = "none";
		document.getElementById('searchByName').style.display = "block";
		if(selectedOption_searchType.value == "flexible") {
			document.getElementById('unameFlexibleDef').style.display = "none";
			document.getElementById('unameStrictDef').style.display = "none";
			document.getElementById('nameFlexibleDef').style.display = "block";
			document.getElementById('nameStrictDef').style.display = "none";
		}
		else {
			document.getElementById('unameFlexibleDef').style.display = "none";
			document.getElementById('unameStrictDef').style.display = "none";
			document.getElementById('nameFlexibleDef').style.display = "none";
			document.getElementById('nameStrictDef').style.display = "block";
		}
	}
}

function showAllAccounts() {
	document.getElementById('showAll').value = "true";
	document.getElementById('adminManageAcctsForm').submit();
}

function deleteAccount(deleteAcctUname) {
	parent.popupConfirm("<p>Delete account?</p><p>"+deleteAcctUname+"</p><p style='font-size:12px;'>Warning: This action will remove all account data (incl. volunteer history) and CANNOT be undone. Historical reports may be affected.</p>").then(function(result){
		if( result === "OK" ) {
			document.getElementById('showAll').value = "<?php echo addslashes($_REQUEST['showAll']); ?>";
			document.getElementById('deleteAcct').value = "true";
			document.getElementById('deleteAcctUname').value = deleteAcctUname;
			document.getElementById('adminManageAcctsForm').submit();
		}
	});
}

function editAccount(editAcctUname) {
	document.getElementById('editAcctUname').value = editAcctUname;
	document.getElementById('adminManageAcctsEditAcctForm').submit();
}
</script>