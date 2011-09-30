<?php
/**
 * Project:     WCM
 * File:        modules/orange/otvEmission/properties.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
	$config = wcmConfig::getInstance();
    
	if (!empty($bizobject->text))
    	$text = unserialize($bizobject->text);
    if (!empty($bizobject->video))
    	$video = unserialize($bizobject->video);
    	
    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_SOURCE_INFORMATION);

	wcmGUI::openFieldset('');
    
	wcmGUI::renderTextField('title', 	$bizobject->title, 		'Title');
	wcmGUI::renderDateField('date', 	$bizobject->date, 		'Date', 'date');
	wcmGUI::renderTextArea('catcher', 	$bizobject->catcher, 	'Catcher');
	
	wcmGUI::renderTextField('subtitle1', isset($text[1]['subtitle'])?$text[1]['subtitle']:null, 	'Subtitle 1');
	wcmGUI::renderTextArea('paragraph1',  isset($text[1]['paragraph'])?$text[1]['paragraph']:null, 	'Paragrap 1');
	wcmGUI::renderTextField('subtitle2', isset($text[2]['subtitle'])?$text[2]['subtitle']:null, 	'Subtitle 2');
	wcmGUI::renderTextArea('paragraph2',  isset($text[2]['paragraph'])?$text[2]['paragraph']:null, 	'Paragrap 2');
	wcmGUI::renderTextField('subtitle3', isset($text[3]['subtitle'])?$text[3]['subtitle']:null, 	'Subtitle 3');
	wcmGUI::renderTextArea('paragraph3',  isset($text[3]['paragraph'])?$text[3]['paragraph']:null, 	'Paragrap 3');
	wcmGUI::renderTextField('subtitle4', isset($text[4]['subtitle'])?$text[4]['subtitle']:null, 	'Subtitle 4');
	wcmGUI::renderTextArea('paragraph4',  isset($text[4]['paragraph'])?$text[4]['paragraph']:null, 	'Paragrap 4');
	wcmGUI::renderTextField('subtitle5', isset($text[5]['subtitle'])?$text[5]['subtitle']:null, 	'Subtitle 5');
	wcmGUI::renderTextArea('paragraph5',  isset($text[5]['paragraph'])?$text[5]['paragraph']:null, 	'Paragrap 5');
	wcmGUI::renderTextField('subtitle6', isset($text[6]['subtitle'])?$text[6]['subtitle']:null, 	'Subtitle 6');
	wcmGUI::renderTextArea('paragraph6',  isset($text[6]['paragraph'])?$text[6]['paragraph']:null, 	'Paragrap 6');
	wcmGUI::renderTextField('subtitle7', isset($text[7]['subtitle'])?$text[7]['subtitle']:null, 	'Subtitle 7');
	wcmGUI::renderTextArea('paragraph7',  isset($text[7]['paragraph'])?$text[7]['paragraph']:null, 	'Paragrap 7');
	wcmGUI::renderTextField('subtitle8', isset($text[8]['subtitle'])?$text[8]['subtitle']:null, 	'Subtitle 8');
	wcmGUI::renderTextArea('paragraph8',  isset($text[8]['paragraph'])?$text[8]['paragraph']:null, 	'Paragrap 8');
	wcmGUI::renderTextField('subtitle9', isset($text[9]['subtitle'])?$text[9]['subtitle']:null, 	'Subtitle 9');
	wcmGUI::renderTextArea('paragraph9',  isset($text[9]['paragraph'])?$text[9]['paragraph']:null, 	'Paragrap 9');
	wcmGUI::renderTextField('subtitle10',isset($text[10]['subtitle'])?$text[10]['subtitle']:null, 	'Subtitle 10');
	wcmGUI::renderTextArea('paragraph10', isset($text[10]['paragraph'])?$text[10]['paragraph']:null, 'Paragrap 10');
	
	//wcmGUI::renderTextField('photo', 	$bizobject->photo, 		'Photo');
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
			echo '<label>Photo</label>';
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
	 
	wcmGUI::renderTextField('video1', 	isset($video['video'][1])?$video['video'][1]:null, 		'Video 1');
	wcmGUI::renderTextField('video2', 	isset($video['video'][2])?$video['video'][2]:null, 		'Video 2');
	wcmGUI::renderTextField('video3', 	isset($video['video'][3])?$video['video'][3]:null, 		'Video 3');
	
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();

    echo '</div>';
