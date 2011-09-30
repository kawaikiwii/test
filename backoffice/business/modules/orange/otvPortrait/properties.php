<?php
/**
 * Project:     WCM
 * File:        modules/orange/otvPortrait/properties.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
	$config = wcmConfig::getInstance();
    
	if (!empty($bizobject->description))
    	$paragraphs = unserialize($bizobject->description);
    	
    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_SOURCE_INFORMATION);

	wcmGUI::openFieldset('');
    
	if (isset($bizobject->number)) $numbers = $bizobject->getAvailableNumbers(true);
	else $numbers = $bizobject->getAvailableNumbers();
	
	wcmGUI::renderDropdownField('type', 		array( "jury"=>"Jury", "candidat"=>"Candidat"), $bizobject->type, 'Type');
	wcmGUI::renderDropdownField('number', 		$numbers, $bizobject->number, 'Number');	
	wcmGUI::renderTextField('firstname', 		$bizobject->firstname, 	_BIZ_LOCATION_FIRSTNAME);
	wcmGUI::renderTextField('lastname', 		$bizobject->lastname, 	_BIZ_LOCATION_LASTNAME);
	wcmGUI::renderDateField('date', 			$bizobject->date, 		'Date', 'date');
	wcmGUI::renderDropdownField('status', 		array("1"=>"1","0"=>"0"), $bizobject->status, 'Status');
	
	wcmGUI::renderTextArea('paragraph1', 		isset($paragraphs['paragraph'][1])?$paragraphs['paragraph'][1]:null, 	'Paragraph 1');
	wcmGUI::renderTextArea('paragraph2', 		isset($paragraphs['paragraph'][2])?$paragraphs['paragraph'][2]:null, 	'Paragraph 2');
	wcmGUI::renderTextArea('paragraph3', 		isset($paragraphs['paragraph'][3])?$paragraphs['paragraph'][3]:null, 	'Paragraph 3');
	wcmGUI::renderTextArea('paragraph4', 		isset($paragraphs['paragraph'][4])?$paragraphs['paragraph'][4]:null, 	'Paragraph 4');
	wcmGUI::renderTextArea('paragraph5', 		isset($paragraphs['paragraph'][5])?$paragraphs['paragraph'][5]:null, 	'Paragraph 5');
	
	
	if (property_exists($bizobject, 'photo'))
    {
		$idForPicture = 'article';
		$photoId = 'chapter_photo_'.$idForPicture;
		wcmGUI::renderHiddenField('photo', $bizobject->photo, array('id' => $photoId));
	    echo '<div id="photo_chapter">';
			$command = '';
			$kind = '';
			$id = '';
			echo '<li>';
			echo '<label>Portrait</label>';
			$selectedPicture = 'selectedPicture_'.$idForPicture;
			if ($bizobject->photo)
				$src = $bizobject->photo;
			else
				$src = 'img/none.gif';
			echo '<img style="float:left; margin-bottom: 5px" width="100px" id="'.$selectedPicture.'" src="'.$src.'" onClick="openmodal(\''.getConst(_BIZ_PHOTOS_ADD).'\', \'650\' ); modalPopup(\'choosePhotoOTV\',\'choosePhotoOTV\', null, \''.$idForPicture.'\'); return false;" style="cursor:pointer" alt="Click to choose" title="Click to choose">';
			echo '<em class="removePicture" alt="Supprimer la photo" title="Supprimer la photo" style="cursor:pointer" onClick="removePicture(\''.$idForPicture.'\')"></em>';
			echo "</li>";
		echo '</div>';
	 }
	
	if (property_exists($bizobject, 'photoLandscape'))
    {
		$idForPicture = 'article2';
		$photoId = 'chapter_photo_'.$idForPicture;
		wcmGUI::renderHiddenField('photoLandscape', $bizobject->photoLandscape, array('id' => $photoId));
	    echo '<div id="photo_chapter2">';
			$command = '';
			$kind = '';
			$id = '';
			echo '<li>';
			echo '<label>Landscape</label>';
			$selectedPicture = 'selectedPicture_'.$idForPicture;
			if ($bizobject->photoLandscape)
				$src = $bizobject->photoLandscape;
			else
				$src = 'img/none.gif';
			echo '<img style="float:left; margin-bottom: 5px" width="100px" id="'.$selectedPicture.'" src="'.$src.'" onClick="openmodal(\''.getConst(_BIZ_PHOTOS_ADD).'\', \'650\' ); modalPopup(\'choosePhotoOTV\',\'choosePhotoOTV\', null, \''.$idForPicture.'\'); return false;" style="cursor:pointer" alt="Click to choose" title="Click to choose">';
			echo '<em class="removePicture" alt="Supprimer la photo" title="Supprimer la photo" style="cursor:pointer" onClick="removePicture(\''.$idForPicture.'\')"></em>';
			echo "</li>";
		echo '</div>';
	 }
	//wcmGUI::renderTextField('photoPortrait', 	$bizobject->photoPortrait, 	'Portrait');
	//wcmGUI::renderTextField('photoLandscape',	$bizobject->photoLandscape, 'Landscape');
	wcmGUI::renderTextField('video', 			$bizobject->video, 			'Video');
	
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    echo '</div>';
