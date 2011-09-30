<?php
/**
 * Project:     WCM
 * File:        modules/modalbox/create_empty_bin.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

wcmGUI::openForm('emptyBin');
wcmGUI::openFieldset('');
wcmGUI::renderTextField('bin_name', '', _BIZ_TITLE . ' *', array('class' => 'type-req'));
wcmGUI::renderTextArea('bin_description', '', _BIZ_DESCRIPTION);
wcmGUI::closeFieldset();        
wcmGUI::closeForm();

?>
<ul class="toolbar">
	<li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
	<li><a href= "#" onclick="if (manageBin('createEmpty',document.getElementById('bin_name').value, document.getElementById('bin_description').value, '', '', 'bins', false)) closemodal(); return false;" class="save"><?php echo _BIZ_SAVE;?></a></li>
</ul>