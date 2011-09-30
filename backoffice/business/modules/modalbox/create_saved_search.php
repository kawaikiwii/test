<?php
/**
 * Project:     WCM
 * File:        modules/modalbox/create_saved_search.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../../initWebApp.php';

wcmGUI::openForm('savedSearch');
wcmGUI::openFieldset('');
wcmGUI::renderTextField('saved_name', '', _BIZ_TITLE . ' *', array('class' => 'type-req'));
wcmGUI::renderTextArea('saved_description', '', _BIZ_DESCRIPTION);
?>
<li>
<label><?php echoH8(_BIZ_DASHBOARD); ?></label>
<input type="checkbox" id="dashboard" name="dashboard" class="modField">
</li>
<li>
<label><?php echoH8(_BIZ_SHARED); ?></label>
<input type="checkbox" id="shared" name="shared" class="modField">
</li>
<?php
wcmGUI::closeFieldset();        
wcmGUI::closeForm();
?>

<!-- <ul class="toolbar">
	<li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
	<li><a href= "#" onclick="if (manageSaveSearch('create',$('saved_name').value, $('saved_description').value, $('search_baseQuery').value+' '+$('search_query').value, getSearchForm().serialize(), '', 'searches', $('dashboard').checked)) { closemodal(); } return false;" class="save"><?php echo _BIZ_SAVE;?></a></li>

</ul> -->
