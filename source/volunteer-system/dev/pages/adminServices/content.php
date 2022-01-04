<?php

if(!isset($_REQUEST['adminSetVolunteersAdminSubmit'])) {
	$volunteersAdminInfo = mysqli_fetch_assoc( getVolunteersAdminInfo() );
	if($volunteersAdminInfo) {
		// Load volunteer coordinator information into the form
		$_REQUEST['volunteersAdminName'] = $volunteersAdminInfo['name'];
		$_REQUEST['volunteersAdminTitle'] = $volunteersAdminInfo['title'];
		$_REQUEST['volunteersAdminPhone'] = $volunteersAdminInfo['phone'];
		$_REQUEST['volunteersAdminEmail'] = $volunteersAdminInfo['email'];
	}
	else {
		// volunteer coordinator information not found
		setErrorMsg("Schema for volunteer coordinator info not found in database. This should not happen. Please notify app developer.", false);
	}
}

if(!isset($_REQUEST['adminSetWebsiteAdminSubmit'])) {
	$websiteAdminInfo = mysqli_fetch_assoc( getWebsiteAdminInfo() );
	if($websiteAdminInfo) {
		// Load website admin information into the form
		$_REQUEST['websiteAdminName'] = $websiteAdminInfo['name'];
		$_REQUEST['websiteAdminTitle'] = $websiteAdminInfo['title'];
		$_REQUEST['websiteAdminPhone'] = $websiteAdminInfo['phone'];
		$_REQUEST['websiteAdminEmail'] = $websiteAdminInfo['email'];
	}
	else {
		// website admin information not found
		setErrorMsg("Schema for app developer info not found in database. This should not happen. Please notify app developer.", false);
	}
}

printPageMessagesAndTitle("Admin Services");

?>
<div class="text-indent">
	<a href="https://docs.google.com/a/fridayskids.org/forms/d/1FfMw02a3Rr_QeDWznmXe-tnX8jL7xuegtBiieHeCXHU/viewform" target="_blank">Respite Night Report Form</a><br/><br/>
	
	<a href="?page=adminManageVolunteerDates">Volunteer Events</a><br/>
	<div class="spacer"></div><br/>
	<a href="?page=adminManageAccts">User Accounts</a><br/>
	<div class="spacer"></div><br/>
	<a href="?page=adminOrgParticipationReports">Participation Reports</a><br/><br/>
	
	<a href="javascript:jumpToElement('adminSetVolunteersAdminForm');">Volunteer Coordinator Info</a><br/>
	<div class="spacer"></div><br/>
	<a href="javascript:jumpToElement('adminSetWebsiteAdminForm');">App Developer Info</a><br/>
</div>
<br/>
<form class="table-indent" style="padding-top:10px;" method="get" action="" name="adminSetVolunteersAdminForm" id="adminSetVolunteersAdminForm">
	<input type="hidden" name="page" id="page" value="adminServices">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<table class="adminServicesInformation">
		<tr>
			<th style="text-align:center;" colspan="2">Volunteer Coordinator Info</th>
		</tr>
		<tr>
			<td style="width:48px;">Name:*</td>
			<td><input name="volunteersAdminName" id="volunteersAdminName" maxlength="100" style="width: 260px;" value="<?php echo htmlentities($_REQUEST['volunteersAdminName']); ?>"></td>
		</tr>
		<tr>
			<td>Title:*</td>
			<td><input name="volunteersAdminTitle" id="volunteersAdminTitle" maxlength="100" style="width: 260px;" value="<?php echo htmlentities($_REQUEST['volunteersAdminTitle']); ?>"></td>
		</tr>
		<tr>
			<td>Phone:*</td>
			<td><input name="volunteersAdminPhone" id="volunteersAdminPhone" maxlength="50" style="width: 260px;" value="<?php echo htmlentities($_REQUEST['volunteersAdminPhone']); ?>"></td>
		</tr>
		<tr>
			<td>Email:*</td>
			<td><input name="volunteersAdminEmail" id="volunteersAdminEmail" maxlength="50" style="width: 260px;" value="<?php echo htmlentities($_REQUEST['volunteersAdminEmail']); ?>"></td>
		</tr>
		<tr>
			<td style="text-align:right;" colspan="2"><span style="font-weight:bold;">(*required fields)</span>
			<div style="width:3px;" class="spacer"></div>
			<input style="margin-right:12px;" type="submit" name="adminSetVolunteersAdminSubmit" id="adminSetVolunteersAdminSubmit" value="Save"/></td>
		</tr>
		<tr>
			<td style="font-style:italic;" colspan="2">
				This information is used: 			
				<ol style="margin-top:5px; margin-bottom:0px; padding-left:25px; padding-right:4px;">
					<li>When a volunteer cancels his/her sign-up within 7 days of the event, an email is sent to notify the volunteer coordinator of a schedule change (fields used: email).</li>
					<li>As the from address on emails to volunteers (fields used: title, email).</li>
					<li>For the signature line on emails to volunteers (fields used: name, title, phone).</li>
					<li>To provide additional contact info in some emails to volunteers (fields used: email, phone).</li>
					<li>To provide contact info within the volunteer portal on the following pages (fields used: email, phone):</li>
						<ul style="padding-left:31px;">
							<li>Create Account, Update Account Info (for questions regarding data collected).</li>
							<li>Create Account, Signup Confirm (for questions regarding the volunteer screening statements, including in error messages).</li>
							<li>My Volunteer Schedule (for general contact info, including cancellation questions).</li>
							<li>All pages with forms (to display contact info in error messages when an "unknown" error occurs)</li>
						<ul>
				<ol>
			</td>
		</tr>
	</table>
</form>
<br/>
<form class="table-indent" style="padding-top:10px;" method="get" action="" name="adminSetWebsiteAdminForm" id="adminSetWebsiteAdminForm">
	<input type="hidden" name="page" id="page" value="adminServices">
	<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
	<table class="adminServicesInformation">
		<tr>
			<th style="text-align:center;" colspan="2">App Developer Info</th>
		</tr>
		<tr>
			<td style="width:48px;">Name:</td>
			<td><input name="websiteAdminName" id="websiteAdminName" maxlength="100" style="width: 260px;" value="<?php echo htmlentities($_REQUEST['websiteAdminName']); ?>"></td>
		</tr>
		<tr>
			<td>Title:</td>
			<td><input name="websiteAdminTitle" id="websiteAdminTitle" maxlength="100" style="width: 260px;" value="<?php echo htmlentities($_REQUEST['websiteAdminTitle']); ?>"></td>
		</tr>
		<tr>
			<td>Phone:</td>
			<td><input name="websiteAdminPhone" id="websiteAdminPhone" maxlength="50" style="width: 260px;" value="<?php echo htmlentities($_REQUEST['websiteAdminPhone']); ?>"></td>
		</tr>
		<tr>
			<td>Email:*</td>
			<td><input name="websiteAdminEmail" id="websiteAdminEmail" maxlength="50" style="width: 260px;" value="<?php echo htmlentities($_REQUEST['websiteAdminEmail']); ?>"></td>
		</tr>
		<tr>
			<td style="text-align:right;" colspan="2"><span style="font-weight:bold;">(*required fields)</span>
			<div style="width:3px;" class="spacer"></div>
			<input style="margin-right:12px;" type="submit" name="adminSetWebsiteAdminSubmit" id="adminSetWebsiteAdminSubmit" value="Save"/></td>
		</tr>
		<tr>
			<td style="font-style:italic;" colspan="2">
				Emails that would normally be sent to the volunteer coordinator (i.e., signup cancellation emails) are sent to the app developer when testing the system in "dev" mode.
			</td>
		</tr>
	</table>
</form>