<?php

function getSigInfo_plainText() {
	$info = mysqli_fetch_assoc( getVolunteersAdminInfo() );

	$info_plainText = $info['name']."\n";
	if(isset($info['title']) && $info['title'] != "") {
		$info_plainText .= $info['title']."\n";
	}
	$info_plainText .= "Kids On The Move\n";
	if(isset($info['phone']) && $info['phone'] != "") {
		$info_plainText .= $info['phone']."\n";
	}
	$info_plainText .= "www.kotm.org\n";
	
	return $info_plainText;
}

?>