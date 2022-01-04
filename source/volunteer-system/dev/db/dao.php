<?php

// *** NOTE ON TIMEZONES ***
// All dates, times, and timestamps are INPUT to the DAO in Mountain Time and OUTPUT from the DAO in Mountain Time.
//      - All timestamps are input to, and stored in, the DB as UTC — tables: account, volunteer, specialEventVolunteer
//      - All dates and times (that are not timestamps) are input w/o timezone and treated as Mountain Time in the DB — tables: volunteerDate, specialEventDate

$DB_CONN = mysqli_connect("localhost","kotm_vol_sys","otK91h#Ngf$3WtMa", "kotm_vol_sys");
if (mysqli_connect_errno()) {
	die("Failed to connect to database: ".mysqli_connect_error());
}
$DBT_PREFIX = "";
if($STAGE == "DEV") {
	$DBT_PREFIX = "dev_";
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// TRANSACTION MANAGEMENT
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

// DB ACCESSOR METHODS

// DB MUTATOR METHODS

function getNextTransactionId() {
	global $DBT_PREFIX, $DB_CONN;
	//
	// The UPDATE atomically increments lastIssuedId. Additionally it passes the result through last_insert_id(),
	// which saves the value in the connection. Subsequently mysqli_insert_id() reads the value from the connection
	// (i.e., out of last_insert_id). Since last_insert_id is specific to the current connection, we've effectively 
	// created an atomic operation (that touches the table only once) that both increments and returns lastIssuedId.
	// This is similar to incrementing a column (via UPDATE) in postgres using the RETURNING directive.
	//
	// The column is named lastIssuedId becasue each 'get' will always return a new value (i.e., lastIssuedId + 1).
	// Incidentally, the column's value also represents the total number of transaction IDs issued to date.
	//
	$sql = "UPDATE ".$DBT_PREFIX."transactionCounter SET lastIssuedId = LAST_INSERT_ID( lastIssuedId + 1 )";
	mysqli_query($DB_CONN, $sql);
	return mysqli_insert_id($DB_CONN);
}

// The function attempts to add transId to a list of used IDs in the database. If transId is not in the "used" 
// list, it is added and 'true' is returned. If transId is already in the list, it is not added (i.e., the list 
// is unchanged) and 'false' is returned. Thus, 'true' indicates the transaction was still valid, whereas 'false' 
// indicates it's already been processed and so is no longer valid. Calling this function more than once with the 
// same transId will result in 'true' for the first call and 'false' for all calls thereafter.
// 
// To keep the usedTransactions table from getting too large, this function also silently cleans up (i.e., 
// deletes) any entries older than 3 months. Hopefully no one is navigating/reloading browser history from more
// than 3 months ago. This timeout assumes transaction IDs will never be placed in a URL that's easily accessible
// to the user (i.e., so the user can copy the url and save for later), in which case the user might do that and
// reuse it after more than 3 months have passed (which would result in the transaction being interpreted as 
// valid and thus getting replayed).
function validateTransactionId($transId) {
	global $DBT_PREFIX, $DB_CONN;
	// start with silent table cleanup
	$sql = "DELETE FROM ".$DBT_PREFIX."usedTransactions WHERE dateUsed <= NOW() - INTERVAL 3 MONTH";;
	mysqli_query($DB_CONN, $sql);
	// now execute the query we came here for
	$sql = sprintf("INSERT INTO ".$DBT_PREFIX."usedTransactions (transId) VALUES ('%s')",
					mysqli_real_escape_string($DB_CONN, $transId));
	return mysqli_query($DB_CONN, $sql);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// INFORMATION
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

// DB ACCESSOR METHODS

function getVolunteersAdminInfo() {
	global $DBT_PREFIX, $DB_CONN;
	$sql = "SELECT * FROM ".$DBT_PREFIX."info WHERE type = 'volunteers_admin' LIMIT 1";
	return mysqli_query($DB_CONN, $sql);
}

function getWebsiteAdminInfo() {
	global $DBT_PREFIX, $DB_CONN;
	$sql = "SELECT * FROM ".$DBT_PREFIX."info WHERE type = 'website_admin' LIMIT 1";
	return mysqli_query($DB_CONN, $sql);
}

// DB MUTATOR METHODS

function setVolunteersAdminInfo($info) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForUpdate($info);
	$sql = sprintf("UPDATE ".$DBT_PREFIX."info SET $keysAndValues WHERE type = 'volunteers_admin'");
	return mysqli_query($DB_CONN, $sql);
}

function setWebsiteAdminInfo($info) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForUpdate($info);
	$sql = sprintf("UPDATE ".$DBT_PREFIX."info SET $keysAndValues WHERE type = 'website_admin'");
	return mysqli_query($DB_CONN, $sql);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ACCOUNT
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

// DB ACCESSOR METHODS

function getAllAccounts_limit500() {
	global $DBT_PREFIX, $DB_CONN;
	$sql = "SELECT *, CONVERT_TZ(createDate,'UTC','America/Denver') createDate, CONVERT_TZ(lastLoginDate,'UTC','America/Denver') lastLoginDate FROM ".$DBT_PREFIX."account ORDER BY uname LIMIT 500";
	return mysqli_query($DB_CONN, $sql);
}

function getCountAllAccounts() {
	global $DBT_PREFIX, $DB_CONN;
	$sql = "SELECT count(*) FROM ".$DBT_PREFIX."account";
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	return $result['count(*)'];
}

function getAccountByUname($uname, $filterAdmin=false) {
	global $DBT_PREFIX, $DB_CONN;
	$filterAdminQueryPart = "";
	if($filterAdmin) {
		$filterAdminQueryPart = " admin='1' AND";
	}
	$sql = sprintf("SELECT *, CONVERT_TZ(createDate,'UTC','America/Denver') createDate, CONVERT_TZ(lastLoginDate,'UTC','America/Denver') lastLoginDate FROM ".$DBT_PREFIX."account WHERE".$filterAdminQueryPart." uname = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname));
	return mysqli_query($DB_CONN, $sql);
}

function getAccountsByUname_limit500($uname, $filterAdmin=false) {
	global $DBT_PREFIX, $DB_CONN;
	$filterAdminQueryPart = "";
	if($filterAdmin) {
		$filterAdminQueryPart = " admin='1' AND";
	}
	$sql = sprintf("SELECT *, CONVERT_TZ(createDate,'UTC','America/Denver') createDate, CONVERT_TZ(lastLoginDate,'UTC','America/Denver') lastLoginDate FROM ".$DBT_PREFIX."account WHERE".$filterAdminQueryPart." uname like '%%%s%%' ORDER BY uname LIMIT 500",
					mysqli_real_escape_string($DB_CONN, $uname));
	return mysqli_query($DB_CONN, $sql);
}

function getCountAccountsByUname($uname, $filterAdmin=false) {
	global $DBT_PREFIX, $DB_CONN;
	$filterAdminQueryPart = "";
	if($filterAdmin) {
		$filterAdminQueryPart = " admin='1' AND";
	}
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."account WHERE".$filterAdminQueryPart." uname like '%%%s%%'",
					mysqli_real_escape_string($DB_CONN, $uname));
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	return $result['count(*)'];
}

function getAccountByName_limit500($first, $last, $filterAdmin=false) {
	global $DBT_PREFIX, $DB_CONN;
	$filterAdminQueryPart = "";
	if($filterAdmin) {
		$filterAdminQueryPart = " admin='1' AND";
	}
	$firstname_search = (isset($first) && $first != "") ? "= '%s'" : "like '%%%s%%'";
	$lastname_search = (isset($last) && $last != "") ? "= '%s'" : "like '%%%s%%'";
	$sql = sprintf("SELECT *, CONVERT_TZ(createDate,'UTC','America/Denver') createDate, CONVERT_TZ(lastLoginDate,'UTC','America/Denver') lastLoginDate FROM ".$DBT_PREFIX."account WHERE".$filterAdminQueryPart." firstname $firstname_search AND lastname $lastname_search ORDER BY uname LIMIT 500",
					mysqli_real_escape_string($DB_CONN, $first),
					mysqli_real_escape_string($DB_CONN, $last));
	return mysqli_query($DB_CONN, $sql);
}

function getCountAccountByName($first, $last, $filterAdmin=false) {
	global $DBT_PREFIX, $DB_CONN;
	$filterAdminQueryPart = "";
	if($filterAdmin) {
		$filterAdminQueryPart = " admin='1' AND";
	}
	$firstname_search = (isset($first) && $first != "") ? "= '%s'" : "like '%%%s%%'";
	$lastname_search = (isset($last) && $last != "") ? "= '%s'" : "like '%%%s%%'";
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."account WHERE".$filterAdminQueryPart." firstname $firstname_search AND lastname $lastname_search",
					mysqli_real_escape_string($DB_CONN, $first),
					mysqli_real_escape_string($DB_CONN, $last));
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	return $result['count(*)'];
}

function getAccountsByName_limit500($first, $last, $filterAdmin=false) {
	global $DBT_PREFIX, $DB_CONN;
	$filterAdminQueryPart = "";
	if($filterAdmin) {
		$filterAdminQueryPart = " admin='1' AND";
	}
	$sql = sprintf("SELECT *, CONVERT_TZ(createDate,'UTC','America/Denver') createDate, CONVERT_TZ(lastLoginDate,'UTC','America/Denver') lastLoginDate FROM ".$DBT_PREFIX."account WHERE".$filterAdminQueryPart." firstname like '%%%s%%' AND lastname like '%%%s%%' ORDER BY lastname LIMIT 500",
					mysqli_real_escape_string($DB_CONN, $first),
					mysqli_real_escape_string($DB_CONN, $last));
	return mysqli_query($DB_CONN, $sql);
}

function getCountAccountsByName($first, $last, $filterAdmin=false) {
	global $DBT_PREFIX, $DB_CONN;
	$filterAdminQueryPart = "";
	if($filterAdmin) {
		$filterAdminQueryPart = " admin='1' AND";
	}
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."account WHERE".$filterAdminQueryPart." firstname like '%%%s%%' AND lastname like '%%%s%%'",
					mysqli_real_escape_string($DB_CONN, $first),
					mysqli_real_escape_string($DB_CONN, $last));
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	return $result['count(*)'];
}

function unameExists($uname) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."account WHERE uname = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname));
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	if($result) {
		return true;
	}
	return false;
}

// DB MUTATOR METHODS

function updateLastLoginDate($uname) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("UPDATE ".$DBT_PREFIX."account SET lastLoginDate = UTC_TIMESTAMP() WHERE uname = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname));
	return mysqli_query($DB_CONN, $sql);
}

function createAccount($acct) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForInsert($acct);
	$sql = "INSERT INTO ".$DBT_PREFIX."account $keysAndValues";
	return mysqli_query($DB_CONN, $sql);
}

function deleteAccount($uname) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("DELETE FROM ".$DBT_PREFIX."account WHERE uname = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname));
	return mysqli_query($DB_CONN, $sql);
}

function updateUname($old, $new) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("UPDATE ".$DBT_PREFIX."account SET uname = '%s' WHERE uname = '%s'",
					mysqli_real_escape_string($DB_CONN, $new),
					mysqli_real_escape_string($DB_CONN, $old));
	return mysqli_query($DB_CONN, $sql);
}

function updatePassword($uname, $newPassword) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("UPDATE ".$DBT_PREFIX."account SET password = '%s' WHERE uname = '%s'",
					mysqli_real_escape_string($DB_CONN, $newPassword),
					mysqli_real_escape_string($DB_CONN, $uname));
	return mysqli_query($DB_CONN, $sql);
}

function updateAcctInfo($uname, $acctInfo) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForUpdate($acctInfo);
	$sql = sprintf("UPDATE ".$DBT_PREFIX."account SET $keysAndValues WHERE uname = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname));
	return mysqli_query($DB_CONN, $sql);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// VOLUNTEER DATE
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

// DB ACCESSOR METHODS

function getAllVolunteerDatesByTypeBefore($type, $date) { // WARNING: could return too much data causing server 500 error
	//$date: expected format yyyy-mm-dd
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."volunteerDate WHERE type = '%s' AND date < '%s' ORDER BY date, location",
					mysqli_real_escape_string($DB_CONN, $type),
					mysqli_real_escape_string($DB_CONN, $date));
	return mysqli_query($DB_CONN, $sql);
}

function getAllSpecialEventDatesBefore($date) { // WARNING: could return too much data causing server 500 error
	//$date: expected format yyyy-mm-dd
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."specialEventDate WHERE date < '%s' ORDER BY date, location",
					mysqli_real_escape_string($DB_CONN, $date));
	return mysqli_query($DB_CONN, $sql);
}

function getAllVolunteerDatesByTypeSince($type, $date) {
	//$date: expected format yyyy-mm-dd
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."volunteerDate WHERE type = '%s' AND date >= '%s' ORDER BY date, location",
					mysqli_real_escape_string($DB_CONN, $type),
					mysqli_real_escape_string($DB_CONN, $date));
	return mysqli_query($DB_CONN, $sql);
}

function getAllSpecialEventDatesSince($date, $primarySortBy="date") {
	//$date: expected format yyyy-mm-dd
	global $DBT_PREFIX, $DB_CONN;
	$sortBy = "date, location";
	if($primarySortBy == "location") {
		$sortBy = "location, date";
	}
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."specialEventDate WHERE date >= '%s' ORDER BY %s",
					mysqli_real_escape_string($DB_CONN, $date),
					$sortBy); // don't escape sortBy!
	return mysqli_query($DB_CONN, $sql);
}

function getAllNonSpecialEventDatesSince($date, $primarySortBy="date") {
	//$date: expected format yyyy-mm-dd
	global $DBT_PREFIX, $DB_CONN;
	$sortBy = "date, type, location";
	if($primarySortBy == "type") {
		$sortBy = "type, date, location";
	}
	else if($primarySortBy == "location") {
		$sortBy = "location, date, type";
	}
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."volunteerDate WHERE date >= '%s' ORDER BY %s",
					mysqli_real_escape_string($DB_CONN, $date),
					$sortBy); // don't escape sortBy!
	return mysqli_query($DB_CONN, $sql);
}

function getOldestVolunteerOrSpecialEventDateYear() {
	global $DBT_PREFIX, $DB_CONN;
	
	$currentDate = getdate();
	$oldestVDate = $currentDate;
	$oldestSpecialEventDate = $currentDate;
	
	$sql = "SELECT * FROM ".$DBT_PREFIX."volunteerDate ORDER BY date ASC LIMIT 1";
	$result = mysqli_query($DB_CONN, $sql);
	if( mysqli_num_rows($result) > 0 ) {
		$oldestVDate = mysqli_fetch_assoc($result);
		$oldestVDate = getdate( strtotime($oldestVDate['date']) );
	}
	
	$sql = "SELECT * FROM ".$DBT_PREFIX."specialEventDate ORDER BY date ASC LIMIT 1";
	$result = mysqli_query($DB_CONN, $sql);
	if( mysqli_num_rows($result) > 0 ) {
		$oldestSpecialEventDate = mysqli_fetch_assoc($result);
		$oldestSpecialEventDate = getdate( strtotime($oldestSpecialEventDate['date']) );
	}
	
	if($oldestVDate['year'] < $oldestSpecialEventDate['year']) {
		return $oldestVDate['year'];
	}
	return $oldestSpecialEventDate['year'];
}

function getVolunteerDateById($vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."volunteerDate WHERE id = '%s'",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function getSpecialEventDateById($specialEventDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."specialEventDate WHERE id = '%s'",
					mysqli_real_escape_string($DB_CONN, $specialEventDateId));
	return mysqli_query($DB_CONN, $sql);
}

function getVolunteerDateByDateStartTimeEndTimeLocationType($date, $startTime, $endTime, $location, $type) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."volunteerDate WHERE date = '%s' AND startTime = '%s' AND endTime = '%s' AND location = '%s' AND type = '%s'",
					mysqli_real_escape_string($DB_CONN, $date),
					mysqli_real_escape_string($DB_CONN, $startTime),
					mysqli_real_escape_string($DB_CONN, $endTime),
					mysqli_real_escape_string($DB_CONN, $location),
					mysqli_real_escape_string($DB_CONN, $type));
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	
	return $result;
}

function getSpecialEventDateByDateStartTimeEndTimeLocation($date, $specifiedTime, $startTime, $endTime, $location) {
	global $DBT_PREFIX, $DB_CONN;
	if($specifiedTime == "yes") {
		$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."specialEventDate WHERE date = '%s' AND hasStartEndTime = '1' AND startTime = '%s' AND endTime = '%s' AND location = '%s'",
						mysqli_real_escape_string($DB_CONN, $date),
						mysqli_real_escape_string($DB_CONN, $startTime),
						mysqli_real_escape_string($DB_CONN, $endTime),
						mysqli_real_escape_string($DB_CONN, $location));
	}
	else {
		$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."specialEventDate WHERE date = '%s' AND hasStartEndTime = '0' AND location = '%s'",
						mysqli_real_escape_string($DB_CONN, $date),
						mysqli_real_escape_string($DB_CONN, $location));
	}
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );

	return $result;
}

function getCountVolunteersForVolunteerDateById($vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."volunteer WHERE volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function getCountVolunteersForSpecialEventDateById($specialEventDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."specialEventVolunteer WHERE volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $specialEventDateId));
	return mysqli_query($DB_CONN, $sql);
}

function getCountVolunteersConfirmedForVolunteerDateById($vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."volunteer WHERE volunteerDateId = '%s' AND confirmed IS NOT NULL",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function getCountVolunteersConfirmedForSpecialEventDateById($specialEventDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."specialEventVolunteer WHERE volunteerDateId = '%s' AND confirmed IS NOT NULL",
					mysqli_real_escape_string($DB_CONN, $specialEventDateId));
	return mysqli_query($DB_CONN, $sql);
}

function getCountVolunteersShowedForVolunteerDateById($vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."volunteer WHERE volunteerDateId = '%s' AND showed = '1'",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	$countYes = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."volunteer WHERE volunteerDateId = '%s' AND showed = '0'",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	$countNo = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	
	$result['yes'] = $countYes['count(*)'];
	$result['no'] = $countNo['count(*)'];
	return $result;
}

function getCountVolunteersShowedForSpecialEventDateById($specialEventDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."specialEventVolunteer WHERE volunteerDateId = '%s' AND showed = '1'",
					mysqli_real_escape_string($DB_CONN, $specialEventDateId));
	$countYes = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	
	$sql = sprintf("SELECT count(*) FROM ".$DBT_PREFIX."specialEventVolunteer WHERE volunteerDateId = '%s' AND showed = '0'",
					mysqli_real_escape_string($DB_CONN, $specialEventDateId));
	$countNo = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	
	$result['yes'] = $countYes['count(*)'];
	$result['no'] = $countNo['count(*)'];
	return $result;
}

function volunteerDateTimeLocationAndTypeExists($date, $startTime, $endTime, $location, $type) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."volunteerDate WHERE date = '%s' AND startTime = '%s' AND endTime = '%s' AND location = '%s' AND type = '%s'",
					mysqli_real_escape_string($DB_CONN, $date),
					mysqli_real_escape_string($DB_CONN, $startTime),
					mysqli_real_escape_string($DB_CONN, $endTime),
					mysqli_real_escape_string($DB_CONN, $location),
					mysqli_real_escape_string($DB_CONN, $type));
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	if($result) {
		return true;
	}
	return false;
}

function specialEventDateTimeAndLocationExists($date, $specifiedTime, $startTime, $endTime, $location) {
	global $DBT_PREFIX, $DB_CONN;
	if($specifiedTime == "yes") {
		$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."specialEventDate WHERE date = '%s' AND hasStartEndTime = '1' AND startTime = '%s' AND endTime = '%s' AND location = '%s'",
						mysqli_real_escape_string($DB_CONN, $date),
						mysqli_real_escape_string($DB_CONN, $startTime),
						mysqli_real_escape_string($DB_CONN, $endTime),
						mysqli_real_escape_string($DB_CONN, $location));
	}
	else {
		$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."specialEventDate WHERE date = '%s' AND hasStartEndTime = '0' AND location = '%s'",
						mysqli_real_escape_string($DB_CONN, $date),
						mysqli_real_escape_string($DB_CONN, $location));
	}
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	if($result) {
		return true;
	}
	return false;
}

// DB MUTATOR METHODS

function createVolunteerDate($vDate) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForInsert($vDate);
	$sql = "INSERT INTO ".$DBT_PREFIX."volunteerDate $keysAndValues";
	return mysqli_query($DB_CONN, $sql);
}

function createSpecialEventDate($specialEventDate) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForInsert($specialEventDate);
	$sql = "INSERT INTO ".$DBT_PREFIX."specialEventDate $keysAndValues";
	return mysqli_query($DB_CONN, $sql);
}

function deleteVolunteerDate($vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("DELETE FROM ".$DBT_PREFIX."volunteerDate WHERE id = '%s'",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function deleteSpecialEventDate($vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("DELETE FROM ".$DBT_PREFIX."specialEventDate WHERE id = '%s'",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function updateVolunteerDate($vDateId, $vDateInfo) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForUpdate($vDateInfo);
	$sql = sprintf("UPDATE ".$DBT_PREFIX."volunteerDate SET $keysAndValues WHERE id = '%s'",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function updateSpecialEventDate($vDateId, $vDateInfo) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForUpdate($vDateInfo);
	$sql = sprintf("UPDATE ".$DBT_PREFIX."specialEventDate SET $keysAndValues WHERE id = '%s'",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// VOLUNTEER
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

// DB ACCESSOR METHODS

function getVolunteersByVolunteerDateId($vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT *, CONVERT_TZ(signupDate,'UTC','America/Denver') signupDate, CONVERT_TZ(confirmed,'UTC','America/Denver') confirmed FROM ".$DBT_PREFIX."volunteer WHERE volunteerDateId = '%s' ORDER BY uname",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function getSpecialEventVolunteersByVolunteerDateId($vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT *, CONVERT_TZ(signupDate,'UTC','America/Denver') signupDate, CONVERT_TZ(confirmed,'UTC','America/Denver') confirmed FROM ".$DBT_PREFIX."specialEventVolunteer WHERE volunteerDateId = '%s' ORDER BY uname",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function getVolunteersVDatesByUnameBefore($uname, $date) {
	//$date: expected format yyyy-mm-dd
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT *, CONVERT_TZ(signupDate,'UTC','America/Denver') signupDate, CONVERT_TZ(confirmed,'UTC','America/Denver') confirmed FROM ".$DBT_PREFIX."volunteer v, ".$DBT_PREFIX."volunteerDate vDate WHERE v.volunteerDateId = vDate.id AND v.uname = '%s' AND vDate.date < '%s' ORDER BY vDate.date desc, vDate.type asc, vDate.location asc, v.signupDate desc",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $date));
	return mysqli_query($DB_CONN, $sql);
}

function getVolunteersSpecialEventDatesByUnameBefore($uname, $date) {
	//$date: expected format yyyy-mm-dd
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT *, CONVERT_TZ(signupDate,'UTC','America/Denver') signupDate, CONVERT_TZ(confirmed,'UTC','America/Denver') confirmed FROM ".$DBT_PREFIX."specialEventVolunteer v, ".$DBT_PREFIX."specialEventDate vDate WHERE v.volunteerDateId = vDate.id AND v.uname = '%s' AND vDate.date < '%s' ORDER BY vDate.date desc, vDate.location asc, v.signupDate desc",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $date));
	return mysqli_query($DB_CONN, $sql);
}

function getVolunteersVDatesByUnameAfter($uname, $date) {
	//$date: expected format yyyy-mm-dd
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT *, CONVERT_TZ(signupDate,'UTC','America/Denver') signupDate, CONVERT_TZ(confirmed,'UTC','America/Denver') confirmed FROM ".$DBT_PREFIX."volunteer v, ".$DBT_PREFIX."volunteerDate vDate WHERE v.volunteerDateId = vDate.id AND v.uname = '%s' AND vDate.date >= '%s' ORDER BY vDate.date asc, vDate.type asc, vDate.location asc, v.signupDate asc",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $date));
	return mysqli_query($DB_CONN, $sql);
}

function getVolunteersSpecialEventDatesByUnameAfter($uname, $date) {
	//$date: expected format yyyy-mm-dd
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT *, CONVERT_TZ(signupDate,'UTC','America/Denver') signupDate, CONVERT_TZ(confirmed,'UTC','America/Denver') confirmed FROM ".$DBT_PREFIX."specialEventVolunteer v, ".$DBT_PREFIX."specialEventDate vDate WHERE v.volunteerDateId = vDate.id AND v.uname = '%s' AND vDate.date >= '%s' ORDER BY vDate.date asc, vDate.location asc, v.signupDate asc",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $date));
	return mysqli_query($DB_CONN, $sql);
}

function getAllVolunteerOrSpecialEventSignupsInMonth($month, $year) {
	global $DBT_PREFIX, $DB_CONN;
	
	$sql = sprintf("SELECT vd.id, vd.date, vd.location, a.uname, a.student, a.school, v.showed, vd.type "
				."FROM ".$DBT_PREFIX."volunteerDate as vd, ".$DBT_PREFIX."volunteer as v, ".$DBT_PREFIX."account as a "
				."WHERE vd.id = v.volunteerDateId AND v.uname = a.uname AND MONTH(vd.date) = '%s' AND YEAR(vd.date) = '%s' "
				."ORDER BY vd.type, vd.location, a.student, a.school",
				mysqli_real_escape_string($DB_CONN, $month),
				mysqli_real_escape_string($DB_CONN, $year));
	$vDateResult = mysqli_query($DB_CONN, $sql);
	$vDates = array();
	while( $vDate = mysqli_fetch_assoc($vDateResult) ) {
		$vDates[] = $vDate;
	}

	$sql = sprintf("SELECT vd.id, vd.date, a.uname, a.student, a.school, v.showed "
				."FROM ".$DBT_PREFIX."specialEventDate as vd, ".$DBT_PREFIX."specialEventVolunteer as v, ".$DBT_PREFIX."account as a "
				."WHERE vd.id = v.volunteerDateId AND v.uname = a.uname AND MONTH(vd.date) = '%s' AND YEAR(vd.date) = '%s' "
				."ORDER BY a.student, a.school",
				mysqli_real_escape_string($DB_CONN, $month),
				mysqli_real_escape_string($DB_CONN, $year));
	$specialEventDateResult = mysqli_query($DB_CONN, $sql);
	$specialEventDates = array();
	while( $specialEventDate = mysqli_fetch_assoc($specialEventDateResult) ) {
		$specialEventDate['location'] = "Any";
		$specialEventDate['type'] = "Special Events";
		$specialEventDates[] = $specialEventDate;
	}
	
	return array_merge($vDates, $specialEventDates);
}

function volunteerExists($uname, $vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."volunteer WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	if($result) {
		return true;
	}
	return false;
}

function specialEventVolunteerExists($uname, $vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT * FROM ".$DBT_PREFIX."specialEventVolunteer WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	$result = mysqli_fetch_assoc( mysqli_query($DB_CONN, $sql) );
	if($result) {
		return true;
	}
	return false;
}

function getVolunteerConfirmId($uname, $vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT confirmId FROM ".$DBT_PREFIX."volunteer WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function getSpecialEventVolunteerConfirmId($uname, $vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("SELECT confirmId FROM ".$DBT_PREFIX."specialEventVolunteer WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

// DB MUTATOR METHODS

function createVolunteer($volunteer) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForInsert($volunteer);
	$sql = "INSERT INTO ".$DBT_PREFIX."volunteer $keysAndValues";
	return mysqli_query($DB_CONN, $sql);
}

function createSpecialEventVolunteer($volunteer) {
	global $DBT_PREFIX, $DB_CONN;
	$keysAndValues = formatKeysAndValuesForInsert($volunteer);
	$sql = "INSERT INTO ".$DBT_PREFIX."specialEventVolunteer $keysAndValues";
	return mysqli_query($DB_CONN, $sql);
}

function deleteVolunteer($uname, $vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("DELETE FROM ".$DBT_PREFIX."volunteer WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function deleteSpecialEventVolunteer($uname, $vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("DELETE FROM ".$DBT_PREFIX."specialEventVolunteer WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function updateVolunteersSetShowed($vDateId, $volunteers, $showed) {
	global $DBT_PREFIX, $DB_CONN;
	if(strtoupper($showed) == "NULL") {
		$showed = mysqli_real_escape_string($DB_CONN, $showed);
	}
	else {
		$showed = "'".mysqli_real_escape_string($DB_CONN, $showed)."'";
	}
	$orStatement = formatOrStatement("uname", $volunteers);
	$sql = sprintf("UPDATE ".$DBT_PREFIX."volunteer SET showed = $showed WHERE volunteerDateId = '%s' AND ($orStatement)",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function updateSpecialEventVolunteersSetShowed($vDateId, $volunteers, $showed) {
	global $DBT_PREFIX, $DB_CONN;
	if(strtoupper($showed) == "NULL") {
		$showed = mysqli_real_escape_string($DB_CONN, $showed);
	}
	else {
		$showed = "'".mysqli_real_escape_string($DB_CONN, $showed)."'";
	}
	$orStatement = formatOrStatement("uname", $volunteers);
	$sql = sprintf("UPDATE ".$DBT_PREFIX."specialEventVolunteer SET showed = $showed WHERE volunteerDateId = '%s' AND ($orStatement)",
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}


function setVolunteerConfirmId($uname, $vDateId, $confirmId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("UPDATE ".$DBT_PREFIX."volunteer SET confirmId = '%s' WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $confirmId),
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function setSpecialEventVolunteerConfirmId($uname, $vDateId, $confirmId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("UPDATE ".$DBT_PREFIX."specialEventVolunteer SET confirmId = '%s' WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $confirmId),
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function setVolunteerConfirmed($uname, $vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("UPDATE ".$DBT_PREFIX."volunteer SET confirmed = UTC_TIMESTAMP() WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

function setSpecialEventVolunteerConfirmed($uname, $vDateId) {
	global $DBT_PREFIX, $DB_CONN;
	$sql = sprintf("UPDATE ".$DBT_PREFIX."specialEventVolunteer SET confirmed = UTC_TIMESTAMP() WHERE uname = '%s' AND volunteerDateId = '%s'",
					mysqli_real_escape_string($DB_CONN, $uname),
					mysqli_real_escape_string($DB_CONN, $vDateId));
	return mysqli_query($DB_CONN, $sql);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// HELPER FUNCTIONS
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function formatKeysAndValuesForInsert($hash) {
	global $DB_CONN;
	$keys = "";
	$values = "";
	$i = 0;
	foreach($hash as $key=>$value) {
		$keys .= mysqli_real_escape_string($DB_CONN, $key);
		$values .= "'".mysqli_real_escape_string($DB_CONN, $value)."'";
		$i++;
		if($i != count($hash)) {
			$keys .= ", ";
			$values .= ", ";
		}
	}
	return "($keys) VALUES ($values)";
}

function formatKeysAndValuesForUpdate($hash) {
	global $DB_CONN;
	$return = "";
	$i = 0;
	foreach($hash as $key=>$value) {
		$return .= mysqli_real_escape_string($DB_CONN, $key)." = '".mysqli_real_escape_string($DB_CONN, $value)."'";
		$i++;
		if($i != count($hash)) {
			$return .= ", ";
		}
	}
	return $return;
}

function formatOrStatement($key, $values) {
	global $DB_CONN;
	$return = "";
	$key = mysqli_real_escape_string($DB_CONN, $key);
	for($i=0; $i<count($values); $i++) {
		$return .= $key." = '".mysqli_real_escape_string($DB_CONN, $values[$i])."'";
		if($i != count($values)-1) {
			$return .= " OR ";
		}
	}
	return $return;
}

?>