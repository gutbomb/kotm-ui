<?php

function userNotLoggedIn() {
	$showLogin = isset($_REQUEST['showLogin']);
	?>
	<style>
		div.headerWrapper {
			margin-bottom: 35px;
		}
	</style>
	<div style="float:left;">
		<a href="?page=signup">Sign Up to Volunteer</a>
        <div class="spacer"></div>
        <a href="?page=faq">FAQ</a>
	</div>
	<div style="float:right;">
		<a href="javascript:toggleLoginForm();">Login<span id="login-link-arrow" class="fa <?php if($showLogin) { echo 'fa-angle-up'; } else { echo 'fa-angle-down'; } ?>" style="padding-left:4px; font-size:16px; position:relative; <?php if($showLogin) { echo 'top:0px;'; } else { echo 'top:1px;'; } ?>"></span></a>
		<div class="spacer" style="width:14px;"></div>
		<a href="?page=passwdReset">Forgot Password?</a>
		<div class="spacer"></div>
		<a href="?page=createAcct">Create Account</a>
	</div>
	<form style="position:absolute; right:0px; top:24px;<?php if(!$showLogin) { echo " display:none;"; } ?>" name="loginForm" id="loginForm" action="" method="post">
		<input type="hidden" name="preserveGetsPosts" id="preserveGetsPosts" value="<?php echo getEncodedGetsPostsString(); ?>"/>
		Username:<div class="spacer" style="width:3px;"></div><input type="text" name="login_uname" id="login_uname" size="13" maxlength="100" value="<?php echo $_REQUEST['login_uname']; ?>"/>
		<div class="spacer" style="width:5px;"></div>
		Password:<div class="spacer" style="width:3px;"></div><input type="password" name="login_password" id="login_password" size="13" maxlength="50"/>
		<div class="spacer" style="width:9px;"></div>
		<input type="submit" name="login" value="Login" />
	</form>
	<script type="text/javascript">
	function toggleLoginForm() {
		var showing = ( $('#loginForm').css('display') === 'none' );
		$('#loginForm').slideToggle(400, function(){
			if(showing) {
				$('#login_uname').focus();
			}
		});
		if(showing) {
			$('#login-link-arrow').removeClass('fa-angle-down');
			$('#login-link-arrow').addClass('fa-angle-up');
			$('#login-link-arrow').css('top', '0px');
		}
		else {
			$('#login-link-arrow').removeClass('fa-angle-up');
			$('#login-link-arrow').addClass('fa-angle-down');
			$('#login-link-arrow').css('top', '1px');
		}
	}
	</script>
	<?php
}

function userLoggedIn() {
	?>
	<div style="float:left;">
		<a href="?page=signup">Sign Up to Volunteer</a>
		<div class="spacer"></div>
		<a href="?page=myVolunteerSchedule">My Volunteer Schedule</a>
		<div class="spacer"></div>
		<a href="?page=manageAcct">Manage Account</a>
		<div class="spacer"></div>
        <a href="?page=faq">FAQ</a>
        <div class="spacer"></div>
	</div>
	<div style="float:right;">
		<?php echo "Welcome ".htmlentities($_SESSION['user']['firstname']) ?>&nbsp;<a href="javascript:logout();">(Logout)</a>
	</div>
	<form name="logoutForm" id="logoutForm" action="" method="get">
		<input type="hidden" name="page" id="page" value="signup"/>
		<input type="hidden" name="logout" id="logout" value="1"/>
	</form>
	<script type="text/javascript">
	function logout() {
		document.getElementById('logoutForm').submit();
	}
	</script>
	<br/>
	<div style="float:right; position:relative; width:100%; text-align:right;">
		<?php
		if($_SESSION['user']['admin']) {
			?><a href="?page=adminServices">Admin Services</a><?php
		}
		else {
			?>&nbsp;<?php
		}
		?>
	</div>
	<?php
}

if(isset($PAGE_LOCATION) && preg_match("/^download/", $PAGE)) {
	// this is a "download" page, so we don't want the full page template, just the content to download
	if( isset($_REQUEST['format']) && $_REQUEST['format'] != "html" ) {
		// This is a file to download, so don't send anything other than the file content, or we'll mess up the download 
		// which needs to be able to send raw HTML headers before anything else
		// DON'T SEND ANY CONTENT HERE (even whitespace)
	}
	else {
		// This is the default "download" format (html), so send a little preliminary javascript to make sure the page 
		// is not being loaded in the iframe
		?>
		<script type="text/javascript">
		if(window.top != window.self) {
			// User tried to access the download page from within the iframe. We don't want this. Probably happened
			// via redirection to login screen and back. Force html download pages to load outside the iframe.
			window.top.location.replace(window.self.location.href);
		}
		</script>
		<?php
	}
	require_once($PAGE_LOCATION);
}
else {
	// this is a standard page, so load the page template
	// the first <script> in the <body> forces all standard pages into the iframe if not already
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<script type="text/javascript">
			
			if(window.top == window.self) {
				// User tried to access the volunteer system directly (i.e., not via iframe in main wordpress site).
				// Redirect to main wordpress site, where 'page_id' is the volunteer portal page and 'vol_sys_page' is
				// the volunteer system url they were trying to access, which needs to be routed through the iframe.
				
				// GET method - *NOT USED*
				// (POST method is better because it keeps the url clean; see POST method implemented below in body)
				location.replace('//'+location.host+'/volunteer-portal/?vol_sys_page='+encodeURIComponent(location.href));
			}
			function jumpToElement(id) {
				// Element offset is relative to iframe, so add iframe offset in as well to get absolute top position relative to browser window.
				var offsetTop = $('#'+id).offset().top + parent.getIframeTop();
				parent.window.scrollTo(parent.window.scrollX, offsetTop);
			}
			</script>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
			<title>Kids On The Move - Volunteer System</title>
			<link href="styles/main.css?v=20200622" rel="stylesheet" type="text/css"/>
			<link href="styles/supplemental.css?v=20200214" rel="stylesheet" type="text/css"/>
			<link href="styles/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
			<link href="//fonts.googleapis.com/css?family=Droid+Sans:regular,bold" rel="stylesheet" type="text/css"/>
            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
			<script src="scripts/jquery-3.2.1.min.js"></script>
            <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		</head>
		<body>
			<div class="headerWrapper">
				<?php
				if($USER_STATUS == "logout") {
					userNotLoggedIn();
				}
				else {
					userLoggedIn();
				}
				if($STAGE == "DEV") {
					?>
					<div class="devNotice">DEVELOPMENT&nbsp;<a href="webdev_todo.txt" target="_blank">TODO List</a></div>
					<?php
				}
				?>
			</div>
			<div class="mainContent">
				<?php
				require_once($PAGE_LOCATION);
				?>
			</div>
			<script type="text/javascript">
			<?php
			if( isset($_REQUEST['jumpId']) ) {
				?>
				$(document).ready(function() {
					jumpToElement("<?php echo $_REQUEST['jumpId']; ?>");
				});
				<?php
			}
			?>
			</script>
		</body>
	</html>	
	<?php
}

?>