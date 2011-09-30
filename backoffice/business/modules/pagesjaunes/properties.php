<?php
/**
 * Project:     WCM
 * File:        modules/pagesjaunes/properties.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */
    
	/* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
	$config = wcmConfig::getInstance();
    
	echo '<div class="zone">';
	
    wcmGUI::openCollapsablePane(_INFORMATIONS);
    wcmGUI::openFieldset();
    
	wcmGUI::renderTextField('guid', 		$bizobject->guid, 			"GUID");
    wcmGUI::renderDateField('startDate', 	$bizobject->startDate, 		_BIZ_STARTDATE);
    wcmGUI::renderTextField('title', 	 	$bizobject->title, 			_BIZ_TITLE);
    wcmGUI::renderTextField('header', 	 	$bizobject->header, 		_BIZ_HEADER, array('id'=>'header2'));
    wcmGUI::renderTextArea('description',	$bizobject->description, 	_BIZ_DESCRIPTION, array('rows'=>5));
	
    wcmGUI::renderTextField('photoUrl', 	$bizobject->photoUrl, 		_BIZ_PHOTO." "._BIZ_URL);   
    if (!empty($bizobject->photoUrl))
    {
    	// init chemin et nom des images
    	$path = $config['wcm.webSite.urlRepository']."client/pagesjaunes/";
    	$file2 = basename($bizobject->photoUrl, ".jpg");
    	$finalFile = $path.$file2."_p.jpg";
    	list($width, $height, $type, $attr) = getimagesize($finalFile);
    	
    	if (fopen($finalFile, 'r'))
    		echo "<img style='margin-left:150px' src='$finalFile' border='0'><br><span style='margin-left:150px'>( IMAGE 2 / Format ".$width."x".$height." : ".$finalFile." )</span><br>";   		
    }    
    wcmGUI::renderTextField('photoCredit', $bizobject->photoCredit, 	_BIZ_CREDITS);
	
	wcmGUI::renderTextArea('search', 		$bizobject->search, 		_BIZ_SEARCH, array('rows'=>3));
    wcmGUI::renderDropdownField('searchType', 	$bizobject->getSearchType(), $bizobject->searchType, _BIZ_SEARCHTYPE);
	
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    
    echo '</div>';