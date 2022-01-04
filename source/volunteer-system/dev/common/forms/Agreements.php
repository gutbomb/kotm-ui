<?php

if(!isset($USE_BULLETS_FOR_AGREEMENTS)) {
	$USE_BULLETS_FOR_AGREEMENTS = false; // default
}
$AGREEMENT_BULLET_STYLE = "fa-angle-right";

function getCheckedStatus($fieldname) {
	if(isset($_REQUEST[$fieldname]) && $_REQUEST[$fieldname] == "yes" ) {
		return " checked";
	}
	else {
		return "";
	}
}

?>
<table class="accountAgreements account-bottom">
	<tr>
		<td class="accountLeft" colspan="2" style="padding-left:23px;">
			<span style="font-weight:bold;"><u>To ensure the safety of the children</u>,</span> Kids On The Move requires all volunteers to affirm/agree with the following statements. If you have any questions, please email us (<?php echo getVolunteersAdminEmailLink(); ?>).
		</td>
	</tr>
	<tr>
		<td class="accountLeft" colspan="2"></td>
	</tr>
	<tr>
		<td class ="narrow" align="right" valign="top">
			<?php
			if($USE_BULLETS_FOR_AGREEMENTS) {
				?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
			}
			else {
				?> <input type="checkbox" name="kotmAgree" id="kotmAgree" value="yes"<?php echo getCheckedStatus("kotmAgree"); ?>> <?php
			}
			?>
		</td>
		<td class ="wide">I agree to be a Kids On The Move volunteer.<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
	</tr>
	<tr>
		<td class="narrow" align="right" valign="top">
			<?php
			if($USE_BULLETS_FOR_AGREEMENTS) {
				?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
			}
			else {
				?> <input type="checkbox" name="arrestedAffirm" id="arrestedAffirm" value="yes"<?php echo getCheckedStatus("arrestedAffirm"); ?>> <?php
			}
			?>
		</td>
		<td class="wide">I affirm that I have never been involved in, arrested, charged with, or convicted of: child abuse, sexual abuse, domestic violence, assault, or any illegal sexual conduct.<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
	</tr>
	<tr>
		<td class="narrow" align="right" valign="top">
			<?php
			if($USE_BULLETS_FOR_AGREEMENTS) {
				?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
			}
			else {
				?> <input type="checkbox" name="sexOffenderAgree" id="sexOffenderAgree" value="yes"<?php echo getCheckedStatus("sexOffenderAgree"); ?>> <?php
			}
			?>
		</td>
		<td class="wide">I understand my name, age, and date of birth, will be looked up on the National Sex Offender Public Website.<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
	</tr>
	<tr>
		<td class="narrow" align="right" valign="top">
			<?php
			if($USE_BULLETS_FOR_AGREEMENTS) {
				?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
			}
			else {
				?> <input type="checkbox" name="drugAffirm" id="drugAffirm" value="yes"<?php echo getCheckedStatus("drugAffirm"); ?>> <?php
			}
			?>
		</td>
		<td class="wide">I affirm that in the past year I have not experimented with any illegal drugs, or abused alcohol or prescription medications.<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
	</tr>
	<tr>
		<td class ="narrow" align="right" valign="top">
			<?php
			if($USE_BULLETS_FOR_AGREEMENTS) {
				?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
			}
			else {
				?> <input type="checkbox" name="soundMindAgree" id="soundMindAgree" value="yes"<?php echo getCheckedStatus("soundMindAgree"); ?>> <?php
			}
			?>
		</td>
		<td class ="wide">I agree that while volunteering I will be of sound mind; and my thinking will not be inhibited or impaired by the use or abuse of: alcohol, drugs, or prescription medications.<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
	</tr>
	<tr>
		<td class ="narrow" align="right" valign="top">
			<?php
			if($USE_BULLETS_FOR_AGREEMENTS) {
				?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
			}
			else {
				?> <input type="checkbox" name="noPhotoAgree" id="noPhotoAgree" value="yes"<?php echo getCheckedStatus("noPhotoAgree"); ?>> <?php
			}
			?>
		</td>
		<td class ="wide">I agree not to take photos or videos of anyone while I am volunteering at Kids On The Move. (Special permission for school projects may be obtained by asking permission from the nurse on duty.)<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
	</tr>
	<tr>
		<td class ="narrow" align="right" valign="top">
			<?php
			if($USE_BULLETS_FOR_AGREEMENTS) {
				?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
			}
			else {
				?> <input type="checkbox" name="noInfoAgree" id="noInfoAgree" value="yes"<?php echo getCheckedStatus("noInfoAgree"); ?>> <?php
			}
			?>
		</td>
		<td class ="wide">I agree to keep confidential ALL information obtained while volunteering for Kids On The Move, which includes children and family names, and any personal information about the families and the children. (Special permission for school projects may be obtained by asking permission from the nurse on duty.)<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
	</tr>
	<tr>
		<td class ="narrow" align="right" valign="top">
			<?php
			if($USE_BULLETS_FOR_AGREEMENTS) {
				?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
			}
			else {
				?> <input type="checkbox" name="usePhotoAgree" id="usePhotoAgree" value="yes"<?php echo getCheckedStatus("usePhotoAgree"); ?>> <?php
			}
			?>
		</td>
		<td class ="wide">I agree that Kids On The Move may use my image and/or words in print, video, photographic, internet, and other electronic mediums to advance the mission of Kids On The Move.<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
	</tr>
	<tr>
		<td class ="narrow" align="right" valign="top">
			<?php
			if($USE_BULLETS_FOR_AGREEMENTS) {
				?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
			}
			else {
				?> <input type="checkbox" name="notResponsibleAgree" id="notResponsibleAgree" value="yes"<?php echo getCheckedStatus("notResponsibleAgree"); ?>> <?php
			}
			?>
		</td>
		<td class ="wide" style="padding-bottom:0px;">I agree not to hold Kids On The Move liable for any acts, omissions, injuries, or damages, and I agree to accept any and all risks associated with volunteering for Kids On The Move.<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
	</tr>
	
</table>