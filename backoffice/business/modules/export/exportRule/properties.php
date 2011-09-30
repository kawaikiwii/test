<?php 
/**
 * Project:     M
 * File:        modules/export/exportRule/properties.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */
 
$bizobject = wcmMVC_Action::getContext();
$config = wcmConfig::getInstance();

echo '<div class="zone">';

wcmGUI::openCollapsablePane(_BIZ_EXPORTRULE);
wcmGUI::openFieldset(_BIZ_EXPORTRULE);
wcmGUI::renderTextField('title', $bizobject->title, _TITLE);
wcmGUI::renderTextField('name', $bizobject->name, _NAME);
wcmGUI::renderTextField('code', $bizobject->code, _CODE);
wcmGUI::renderTextField('unitTemplate', $bizobject->unitTemplate, _UNITTEMPLATE);
wcmGUI::renderTextField('globalTemplate', $bizobject->globalTemplate, _GLOBALTEMPLATE);
wcmGUI::renderDropdownField('zip', $bizobject->getZipType(),$bizobject->zip, 'Zip Archive');

echo '<fieldset style="margin-left:5px">';
echo "<legend>Users Permission</legend>";
echo "<ul>";
	echo "<li>";
	$acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.users.php',
					   'paramName' => 'prefix',
					   'parameters' => '',
					   'className' => 'wcmUser');
	//relaxGUI::renderRelaxListField('loginAs', explode('|',$relaxTask->loginAs), array('style' => 'float:none;'), $acOptions);
	relaxGUI::renderRelaxListField('permissions', $bizobject->getPermissionsForGui(), array('style' => 'float:none;'), $acOptions);
	echo "</li>";
echo "</ul>";
echo "</fieldset>";

wcmGUI::renderBooleanField('copyIllustrations', $bizobject->copyIllustrations, "Copy illustrations");

echo '<fieldset style="margin-left:5px">';
echo "<legend>Photo</legend>";


relaxGUI::getArrayCheckboxes('formats', photo::getPhotoFormats(true), unserialize($bizobject->formats));
echo "</fieldset>";

wcmGUI::renderBooleanField('copyVideos', $bizobject->copyVideos, "Copy Videos");
echo '<fieldset style="margin-left:5px">';
echo "<legend>Video</legend>";


relaxGUI::getArrayCheckboxes('videoFormats', video::getVideoFormatsFromConfig(), unserialize($bizobject->videoFormats));
echo "</fieldset>";

wcmGUI::renderBooleanField('confirmationFile', $bizobject->confirmationFile, "Transfert Confirmation File");

wcmGUI::closeFieldset();
wcmGUI::closeCollapsablePane();

echo '</div>';
