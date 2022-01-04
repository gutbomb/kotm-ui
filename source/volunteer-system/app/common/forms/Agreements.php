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
        <td class ="narrow" align="right" valign="top">
            <?php
            if($USE_BULLETS_FOR_AGREEMENTS) {
                ?> <span class="fa <?php echo $AGREEMENT_BULLET_STYLE; ?> agreements-table-bullet-icon"></span> <?php
            }
            else {
                ?> <input type="checkbox" name="readRules" id="readRules" value="yes"<?php echo getCheckedStatus("readRules"); ?>> <?php
            }
            ?>
        </td>
        <td class ="wide">I have read and understand and will promise to abide by the <a href="/volunteer-system/app/?page=volunteerRules">Volunteer Rules and Policies</a>.<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
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
		<td class ="wide">I agree not to take photos or videos of anyone while I am volunteering at Kids On The Move. (Special permission for school projects may be obtained by asking permission from the staff on duty.)<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
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
		<td class ="wide">I agree to keep confidential ALL information obtained while volunteering for Kids On The Move, which includes children and family names, and any personal information about the families and the children. (Special permission for school projects may be obtained by asking permission from the staff on duty.)<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?></td>
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
		<td class ="wide" style="padding-bottom:0px;">
			I agree not to hold Kids On The Move liable for any acts, omissions, injuries, or damages, and I agree to accept any and all risks associated with volunteering for Kids On The Move.<br><br>I also agree to read and abide by the <a href="#notResponsibleAgree" id="covid-policy-link">Kids On The Move COVID-19 Volunteer Consent</a> (click to read)<?php if(!$USE_BULLETS_FOR_AGREEMENTS){ echo "*"; } ?>
			<div id="covid-policy">
				<h3>Kids On The Move Volunteer Consent</h3>
				<p>As a volunteer at Kids On The Move (KOTM), I understand that my volunteer position may require close contact interaction with someone who may or may not be aware that they have COVID-19. I also understand that:</p>
				<ol>
					<li>KOTM is following CDC and Utah Department of Health guidelines for preventing the spread of COVID-19 and has established protocols for ensuring my safety when I come into contact with someone who has contracted COVID-19, when that information is made known to KOTM.</li>
					<li>KOTM will preserve my COVID-19 HIPAA-related information to the extent possible, and I acknowledge that KOTM’s need to share my COVID-19 information with others (on a need-toknow basis) will happen as a protection to the broader community.</li>
					<li>While KOTM requires staff, clients, volunteers and the general public to respond to a health screening prior to interaction, anyone could choose to respond falsely and thus expose others to an increased risk for contracting COVID-19. I will not hold KOTM liable due to somebody who falsely represents their conditions on the health screening.</li>
					<li>COVID-19 has a long incubation period during which carriers of the virus may not show symptoms but are still contagious.</li>
					<li>Due to the COVID-19 incubation period, I may unknowingly pass on the COVID-19 virus to others. I also acknowledge that others may unknowingly pass on the COVID-19 virus to me.</li>
					<li>Due to the close contact nature of services provided by KOTM and characteristics of the virus, there is an elevated risk of transmission for COVID-19 (and other infectious diseases) despite the additional measures being taken by KOTM to prevent such transmission.</li>
				</ol>
				<p><strong>INFORMED CONSENT</strong>: By reading and agreeing to this document, I declare that I understand the inherent risks of my volunteer position with KOTM as it relates to COVID-19, and I bring no claim whatsoever upon KOTM as a result of my volunteering with KOTM.</p>
				<?php
					if(!$USE_BULLETS_FOR_AGREEMENTS) {
				?>
				<p><strong>Please scroll up and check the box for this item confirming you have read it before submitting the form.</strong></p>
				<?php
					}
				?>
			</div>
		</td>
	</tr>
</table>
<script src="scripts/agreements.js"></script>