<?php
/**
 * Project:     WCM
 * File:        modules/modalbox/create_bin_session.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
?>
<form onsubmit="return false;">
	<fieldset>
		<ul>
			<li>
				<label><?php echo _BIZ_TITLE;?></label>
				<input type="text" id="bin_name" />
			</li>
			<li>
				<label><?php echo _BIZ_DESCRIPTION;?></label>
				<textarea id="bin_description" rows="" cols=""></textarea>
			</li>
	
		</ul>
	</fieldset>
</form>
	
<ul class="toolbar">
	<li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
	<li><a href= "#" onclick="manageBin('createBinFromSession',document.getElementById('bin_name').value, document.getElementById('bin_description').value, '', '', 'bins', false); closemodal(); return false;" class="save"><?php echo _BIZ_SAVE;?></a></li>
</ul>