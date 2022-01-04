<table class="account account-bottom">
	<tr>
		<td class="accountLeft">How did you hear about Kids On The Move?</td>
		<td>
			<div style="display:inline-block; margin-bottom:-20px;">
				<textarea name="howHeardAbout" id="howHeardAbout" maxlength="500" style="width:406px;" rows="5" cols="19" oninput="textCounter();"><?php echo htmlentities($_REQUEST['howHeardAbout']); ?></textarea>
				<div style="color:#999999; text-align:right; padding-right:2px;"><span id="charsLeftCount">500</span> left</div>
			</div>
		</td>
	</tr>
	<tr><td><div></div></td></tr>
</table>

<script type="text/javascript">
// initialize characters left counter
textCounter();

function textCounter() {
	var field = document.getElementById('howHeardAbout');
	document.getElementById('charsLeftCount').innerHTML = field.maxLength - field.value.length;
}
</script>