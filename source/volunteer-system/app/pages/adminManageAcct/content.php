<?php

$accountLoadError = false;

if(isset($_REQUEST['id']) && !isset($_REQUEST['editAcctUname'])) {
	$_REQUEST['editAcctUname'] = $_REQUEST['id'];
}

if(isset($_REQUEST['editAcctUname'])) {
	$_REQUEST['editAcctUname'] = truncate(trim($_REQUEST['editAcctUname']), 100);
	$acct = mysqli_fetch_assoc( getAccountByUname($_REQUEST['editAcctUname']) );
	
	if(!isset($_REQUEST['firstname'])) {
		// Load the initial data from the account into the form
		if($acct) {
			$_REQUEST['admin'] = ($acct['admin'] == '1') ? "yes" : "no";
			$_REQUEST['firstname'] = $acct['firstname'];
			$_REQUEST['lastname'] = $acct['lastname'];
			$_REQUEST['birthdate'] = $acct['birthdate'];
			$_REQUEST['email'] = $acct['email'];
			$_REQUEST['address1'] = $acct['address1'];
			$_REQUEST['address2'] = $acct['address2'];
			$_REQUEST['city'] = $acct['city'];
			$_REQUEST['state'] = $acct['state'];
			$_REQUEST['zip'] = $acct['zip'];
            $_REQUEST['phone1'] = $acct['phone1'];
            $_REQUEST['phone2'] = $acct['phone2'];
			$_REQUEST['student'] = ($acct['student'] == '1') ? 'yes' : 'no';
			$_REQUEST['school'] = $acct['school'];
            $_REQUEST['emergencyName'] = $acct['emergencyName'];
            $_REQUEST['emergencyRelationship'] = $acct['emergencyRelationship'];
            $_REQUEST['emergencyPhone'] = $acct['emergencyPhone'];
			$_REQUEST['howHeardAbout'] = $acct['howHeardAbout'];
		}
		else {
			//given account not found
			setErrorMsg("Account not found.", false);
			$accountLoadError = true;
		}
	}
	
}
else {
	//no account selected
	setErrorMsg("No account selected.", false);
	$accountLoadError = true;
}

printPageMessagesAndTitle("Admin Services - Manage Account");

if(!$accountLoadError) {
	require_once('pages/adminManageAcct/AdminManageUsernamePassword.php');
	?>
	<form class="table-indent" style="margin-top:10px; margin-bottom:27px;" method="get" action="" name="adminEditAcctForm" id="adminEditAcctForm">
		<input type="hidden" name="page" id="page" value="adminManageAcct">
		<input type="hidden" name="transId" id="transId" value="<?php echo getNextTransactionIdEncoded(); ?>">
		<input type="hidden" name="editAcctUname" id="editAcctUname2" value="<?php echo htmlentities($_REQUEST['editAcctUname']); ?>">
		<table style="text-align:right; width:618px;" cellspacing="5px">
			<tr>
				<td>*Required Fields</td>
			</tr>
		</table>
		<?php
		require_once('pages/adminManageAcct/AdminManageNameEmail.php');
		require_once('pages/adminManageAcct/AdminManageAddressPhone.php');
		require_once('pages/adminManageAcct/AdminManageStudent.php');
        require_once('pages/adminManageAcct/AdminManageEmergency.php');
        ?>
	</form>
	<?php
	function printVDates($vDates, $title, $areCurrent) {
		?>
		<table class="text-indent" style="border-collapse:collapse; margin-bottom:10px; width:618px;<?php if(!$areCurrent) { echo " margin-top:22px;"; } ?>">
			<tr>
				<td><h2 style="margin:0px;"><?php echo htmlentities($title); ?></h2></td>
				<?php
				if($areCurrent) {
					?>
					<td style="text-align:right;"><a id="show-history-anchor" name="show-history-anchor" href="javascript:toggleHistory()">Show History</a></td>
					<?php
				}
				?>
			</tr>
		</table>
		<table class="adminManageVolunteers table-indent" style="table-layout:fixed; word-wrap:break-word;">
			<?php
			if(count($vDates) < 1) {
				$noneText = "No events scheduled";
				if(!$areCurrent) {
					$noneText = "None";
				}
				?>
				<tr>
					<td style="text-align:left;"><?php echo $noneText; ?></td>
				</tr>
				<?php
			}
			else {
				?>
				<col style="width:135px;"/>
				<col style="width:75px;"/>
				<col style="width:120px;"/>
				<col style="width:105px;"/>
				<col style="width:105px;"/>
				<col style="width:77px;"/>
				<tr>
					<th>Date/Time<br/><span style="font-weight:normal;">(Mountain Time)</span></th>
					<th>Event</th>
					<th>Location</th>
					<th>Sign-up Date</th>
					<th>Date Confirmed</th>
					<th>Showed?</th>
				</tr>
				<?php
				foreach($vDates as $vDate) {
					$isSpecial = isset($vDate['hasStartEndTime']);
					$isSpecialJavaScriptSetting = "false";
					if($isSpecial) {
						$isSpecialJavaScriptSetting = "true";
					}
					
					$time = "Anytime";
					if( !$isSpecial || $vDate['hasStartEndTime'] != "0" ) {
						$startTime = strftime("%-l:%M %P", strtotime($vDate['startTime']));
						$endTime = strftime("%-l:%M %P", strtotime($vDate['endTime']));
						$time = htmlentities($startTime)." - ".htmlentities($endTime);
					}
				
					$formattedDateVDate = formatDateAsText_monthDayYear($vDate['date']);
					$formattedDateSignupDate = formatDateAsText_monthDayYear($vDate['signupDate'], true);
					
					echo "<tr>";
					echo "	<td><a href=\"javascript:manageVolunteerDate('".addslashes($vDate['id'])."', '".$isSpecialJavaScriptSetting."');\">".htmlentities($formattedDateVDate)."</a><br/>".$time."</td>";
					echo "	<td>".htmlentities($vDate['type'])."</td>";
					echo "	<td>".htmlentities($vDate['location'])."</td>";
					echo "	<td>".htmlentities($formattedDateSignupDate)."</td>";
					echo "	<td>";
					if($vDate['confirmed'] == "") {
						echo "None";
						if($vDate['confirmId'] != null) {
							echo "*";
						}
					}
					else {
						echo htmlentities( formatDateAsText_monthDayYear($vDate['confirmed'], true) );
					}
					echo "	</td>";
					echo "	<td>";
					if($vDate['showed'] == '0') {
						echo "No";
					}
					else if($vDate['showed'] == '1') {
						echo "Yes";
					}
					else {
						echo "Not Recorded";
					}
					echo "	</td>";
					echo "</tr>";
				}
			}
			?>
		</table>
		<div class="text-indent">
			&nbsp;* Indicates reminder / confirm email has been sent.
		</div>
		<?php
	}
	
	$vDatesDbResult = getVolunteersVDatesByUnameBefore($acct['uname'], getMySqlDate(time()));
	$vDatesPast = array();
	while($vDate = mysqli_fetch_assoc($vDatesDbResult)) {
		$vDatesPast[] = $vDate;
	}
	
	$vDatesDbResult = getVolunteersVDatesByUnameAfter($acct['uname'], getMySqlDate(time()));
	$vDatesCurrent = array();
	while($vDate = mysqli_fetch_assoc($vDatesDbResult)) {
		$vDatesCurrent[] = $vDate;
	}
	
	$specialEventDatesDbResult = getVolunteersSpecialEventDatesByUnameBefore($acct['uname'], getMySqlDate(time()));
	$specialEventDatesPast = array();
	while($specialEventDate = mysqli_fetch_assoc($specialEventDatesDbResult)) {
		$specialEventDate['type'] = "Special Event";
		$specialEventDatesPast[] = $specialEventDate;
	}
	
	$specialEventDatesDbResult = getVolunteersSpecialEventDatesByUnameAfter($acct['uname'], getMySqlDate(time()));
	$specialEventDatesCurrent = array();
	while($specialEventDate = mysqli_fetch_assoc($specialEventDatesDbResult)) {
		$specialEventDate['type'] = "Special Event";
		$specialEventDatesCurrent[] = $specialEventDate;
	}
	
	$vDatesCurrentAll = mergeDates($vDatesCurrent, $specialEventDatesCurrent, true);
	$vDatesPastAll = mergeDates($vDatesPast, $specialEventDatesPast, false);
	
	printVDates($vDatesCurrentAll, "Current Schedule", true);
	?>
	
	<div id="event-history" name="event-history" style="display:none;">
		<?php
		printVDates($vDatesPastAll, "History", false);
		?>
	</div>
	
	<form method="get" action="" name="adminEditAcctManageVDateForm" id="adminEditAcctManageVDateForm">
		<input type="hidden" name="page" id="page" value="adminManageVolunteerDate">
		<input type="hidden" name="manageVDateId" id="manageVDateId" value=""/>
	</form>
	<form method="get" action="" name="adminEditAcctManageSpecialEventDateForm" id="adminEditAcctManageSpecialEventDateForm">
		<input type="hidden" name="page" id="page" value="adminManageSpecialEventDate">
		<input type="hidden" name="manageSpecialEventDateId" id="manageSpecialEventDateId" value=""/>
	</form>
	<script type="text/javascript">
        var availableSchools = [
            "Brigham Young Univerity",
            "Dixie State University",
            "Rocky Mountain University",
            "Roseman University",
            "Salt Lake Community College",
            "Utah Valley University"
        ];
        $( "#school" ).autocomplete({
            source: availableSchools
        });

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

        function toggleHistory() {
            var isHiding = true;
            var frameHeight = parent.getIframeHeight();
            var elementHeight = $('#event-history').height();
            var history = document.getElementById('event-history');
            if(history.style.display == "none") {
                isHiding = false;
            }
            $('#event-history').slideToggle({
                duration: 400,
                progress: function(animation, progress, remainingMs){
                    if(isHiding) {
                        parent.setIframeHeight( frameHeight - (elementHeight*progress) );
                    }
                    else {
                        parent.setIframeHeight( frameHeight + (elementHeight*progress) );
                    }
                },
                complete: function(){
                    parent.resizeIframe();
                    var historyAnchor = document.getElementById('show-history-anchor');
                    if(history.style.display == "none") {
                        historyAnchor.innerHTML = "Show History";
                    }
                    else {
                        historyAnchor.innerHTML = "Hide History";
                    }
                }
            });
        }

        function manageVolunteerDate(manageDateId, isSpecialEventDate) {
            if(isSpecialEventDate == "true") {
                document.getElementById('manageSpecialEventDateId').value = manageDateId;
                document.getElementById('adminEditAcctManageSpecialEventDateForm').submit();
            }
            else{
                document.getElementById('manageVDateId').value = manageDateId;
                document.getElementById('adminEditAcctManageVDateForm').submit();
            }
        }
	</script>
	<?php
}
