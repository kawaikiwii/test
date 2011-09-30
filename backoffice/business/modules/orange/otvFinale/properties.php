<?php
/**
 * Project:     WCM
 * File:        modules/orange/otvFinale/properties.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
	$config = wcmConfig::getInstance();
    
	if (!empty($bizobject->description))
    	$description = unserialize($bizobject->description);
    if (!empty($bizobject->description2))
    	$description2 = unserialize($bizobject->description2);
    	
    echo '<div class="zone">';
  
    $script = "var count = 0;";
    $script .= "var firstname = new Array();";
    $script .= "var lastname = new Array();"; 
    $arrayType = array();
    $otvPortrait = new otvPortrait();
    $otvPortrait->beginEnum("status=1");
    while ($otvPortrait->nextEnum())
    {
    	$script .= "firstname['".$otvPortrait->number."'] = '".addslashes($otvPortrait->firstname)."';";
		$script .= "lastname['".$otvPortrait->number."'] = '".addslashes($otvPortrait->lastname)."';";
		$script .= "count++;";
	}
	$otvPortrait->endEnum();  
	
	wcmGUI::openFieldset('');
    wcmGUI::renderTextArea('subtitle', 			$bizobject->subtitle, 	'subtitle');
	wcmGUI::closeFieldset();
	
    wcmGUI::openCollapsablePane("Finaliste 1");

	wcmGUI::openFieldset('');
    
	wcmGUI::renderDropdownField('type', 		array( "finaliste 1"=>"Finaliste 1"), $bizobject->type, 'Type');
	
	$onChange = $script."if(lastname.length != null && (options[selectedIndex].value != 0)) {document.getElementById('lastname').value = lastname[options[selectedIndex].value];document.getElementById('firstname').value = firstname[options[selectedIndex].value];}";
	wcmGUI::renderDropdownField('number', 		$bizobject->getFinalNumber(), $bizobject->number, 'Number', array('onChange'=>$onChange));	
	wcmGUI::renderTextField('firstname', 		$bizobject->firstname, 	_BIZ_LOCATION_FIRSTNAME, array('id'=>'firstname'));
	wcmGUI::renderTextField('lastname', 		$bizobject->lastname, 	_BIZ_LOCATION_LASTNAME, array('id'=>'lastname'));
	wcmGUI::renderDateField('date', 			$bizobject->date, 		'Date', 'date');
	wcmGUI::renderDropdownField('status', 		array("1"=>"1", "0"=>"0"), $bizobject->status, 'Status');
	
	wcmGUI::renderTextField('subtitle1', 		isset($description[1]['subtitle'])?$description[1]['subtitle']:null, 	'Subtitle 1');
	wcmGUI::renderTextArea('paragraph1', 		isset($description[1]['paragraph'])?$description[1]['paragraph']:null, 	'Paragraph 1');
	wcmGUI::renderTextField('subtitle2', 		isset($description[2]['subtitle'])?$description[2]['subtitle']:null, 	'Subtitle 2');
	wcmGUI::renderTextArea('paragraph2', 		isset($description[2]['paragraph'])?$description[2]['paragraph']:null, 	'Paragraph 2');
	wcmGUI::renderTextField('subtitle3', 		isset($description[3]['subtitle'])?$description[3]['subtitle']:null, 	'Subtitle 3');
	wcmGUI::renderTextArea('paragraph3', 		isset($description[3]['paragraph'])?$description[3]['paragraph']:null, 	'Paragraph 3');
	wcmGUI::renderTextField('subtitle4', 		isset($description[4]['subtitle'])?$description[4]['subtitle']:null, 	'Subtitle 4');
	wcmGUI::renderTextArea('paragraph4', 		isset($description[4]['paragraph'])?$description[4]['paragraph']:null, 	'Paragraph 4');
	wcmGUI::renderTextField('subtitle5', 		isset($description[5]['subtitle'])?$description[5]['subtitle']:null, 		'Subtitle 5');
	wcmGUI::renderTextArea('paragraph5', 		isset($description[5]['paragraph'])?$description[5]['paragraph']:null, 	'Paragraph 5');
	
	wcmGUI::renderTextField('video', 			$bizobject->video, 			'Video');
	
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
	
    wcmGUI::openCollapsablePane("Finaliste 2");

	wcmGUI::openFieldset('');
    
	wcmGUI::renderDropdownField('type2', 		array( "finaliste 2"=>"Finaliste 2"), $bizobject->type, 'Type');
	
	$onChange = $script."if(lastname.length != null && (options[selectedIndex].value != 0)) {document.getElementById('lastname2').value = lastname[options[selectedIndex].value];document.getElementById('firstname2').value = firstname[options[selectedIndex].value];}";
	wcmGUI::renderDropdownField('number2', 		$bizobject->getFinalNumber(), $bizobject->number2, 'Number', array('onChange'=>$onChange));	
	wcmGUI::renderTextField('firstname2', 		$bizobject->firstname2, 	_BIZ_LOCATION_FIRSTNAME, array('id'=>'firstname2'));
	wcmGUI::renderTextField('lastname2', 		$bizobject->lastname2, 	_BIZ_LOCATION_LASTNAME, array('id'=>'lastname2'));
	wcmGUI::renderDateField('date2', 			$bizobject->date2, 		'Date', 'date');
	wcmGUI::renderDropdownField('status2', 		array("1"=>"1", "0"=>"0"), $bizobject->status, 'Status');
	
	wcmGUI::renderTextField('subtitle21', 		isset($description2[1]['subtitle'])?$description2[1]['subtitle']:null, 		'Subtitle 1');
	wcmGUI::renderTextArea('paragraph21', 		isset($description2[1]['paragraph'])?$description2[1]['paragraph']:null, 	'Paragraph 1');
	wcmGUI::renderTextField('subtitle22', 		isset($description2[2]['subtitle'])?$description2[2]['subtitle']:null, 		'Subtitle 2');
	wcmGUI::renderTextArea('paragraph22', 		isset($description2[2]['paragraph'])?$description2[2]['paragraph']:null, 	'Paragraph 2');
	wcmGUI::renderTextField('subtitle23', 		isset($description2[3]['subtitle'])?$description2[3]['subtitle']:null, 		'Subtitle 3');
	wcmGUI::renderTextArea('paragraph23', 		isset($description2[3]['paragraph'])?$description2[3]['paragraph']:null, 	'Paragraph 3');
	wcmGUI::renderTextField('subtitle24', 		isset($description2[4]['subtitle'])?$description2[4]['subtitle']:null, 		'Subtitle 4');
	wcmGUI::renderTextArea('paragraph24', 		isset($description2[4]['paragraph'])?$description2[4]['paragraph']:null, 	'Paragraph 4');
	wcmGUI::renderTextField('subtitle25', 		isset($description2[5]['subtitle'])?$description2[5]['subtitle']:null, 		'Subtitle 5');
	wcmGUI::renderTextArea('paragraph25', 		isset($description2[5]['paragraph'])?$description2[5]['paragraph']:null, 	'Paragraph 5');
	
	wcmGUI::renderTextField('video2', 			$bizobject->video2, 			'Video');
	
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';
