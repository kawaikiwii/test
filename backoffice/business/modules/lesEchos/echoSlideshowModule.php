<?php
/**
 * Project:     WCM
 * File:        modules/lesEchos/echoSlideshowModule.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $uniqid = uniqid('echoSlideshow_text_');
    $echoSlideshow = getArrayParameter($params, 'echoSlideshow', new echoSlideshow());

    $menus = array(
				getConst(_ADD) => '\'' . wcmModuleURL('business/lesEchos/echoSlideshowModule') . '\', null, this',
				getConst(_DELETE)    => 'removeEchoSlideshow'
			);

    $info = '<ul>';
    foreach ($menus as $title => $action)
    {
    	if ($title == getConst(_DELETE))
    	{
           $info .= '<li><a href="#" onclick="'.$action.'(this, \''.$uniqid.'\'); return false;">' . $title . '</a></li>';
    	}
    	else
    	{        
    	   $info .= '<li><a href="#" onclick="addEchoSlideshow(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    $title = (isset($echoSlideshow->title)) ? "Diaporama" . ' ' . $echoSlideshow->title : _BIZ_NEW;
    echo '<div class="zone">';
		echo '<div id="echoSlideshow" style="clear: both;">';
		wcmGUI::openCollapsablePane($title, true, $info);
		wcmGUI::openFieldset('', array('id' => 'echoSlideshowFieldset'. $echoSlideshow->id));
		
		wcmGUI::openFieldset("");
		wcmGUI::renderHiddenField('echoSlideshow_id[]', $echoSlideshow->id, array('id' => uniqid()));
		wcmGUI::renderTextField('echoSlideshow_title[]',	$echoSlideshow->title, 		"Titre");
		wcmGUI::renderTextField('echoSlideshow_credits[]',	$echoSlideshow->credits, 	"Crédits");
		//wcmGUI::renderTextField('echoSlideshow_file[]',		$echoSlideshow->file, 		"Fichier");
		$idForPicture = 'slideshowFile'.uniqid();
		wcmGUI::renderHiddenField('echoSlideshow_file2[]', $echoSlideshow->file, array('id' => $idForPicture.'2'));
	    wcmGUI::renderFileField('echoSlideshow_file[]', 			"Fichier", array("id"=>$idForPicture));
	    if (!empty($echoSlideshow->file)) echo "<a href='".$config['wcm.backOffice.url'].$config['wcm.backOffice.photosPathLesEchos'].DIRECTORY_SEPARATOR.$echoSlideshow->file."' target='_blank'>cliquez ici pour voir l'image :  ".$echoSlideshow->file."</a>";
	    /*
		$idForPicture = 'slideshowFile'.uniqid();
		$photoId = 'photo_'.$idForPicture;
		wcmGUI::renderHiddenField('echoSlideshow_file[]', $echoSlideshow->file, array('id' => $photoId));
	    echo '<div id="photo_chapter">';
			echo '<li>';
			echo '<label>Fichier</label>';
			$selectedPicture = 'selectedPicture_'.$idForPicture;
			if ($echoSlideshow->file)
				$src = $echoSlideshow->file;
			else
				$src = 'img/none.gif';
			echo '<img style="float:left; margin-bottom: 5px" width="100px" id="'.$selectedPicture.'" src="'.$src.'" onClick="openmodal(\''.getConst(_BIZ_PHOTOS_ADD).'\', \'650\' ); modalPopup(\'chooseFileLesEchos\',\'chooseFileLesEchos\', null, \''.$idForPicture.'\'); return false;" style="cursor:pointer" alt="Click to choose" title="Click to choose">';
			echo '<em class="removePicture" alt="Supprimer la photo" title="Supprimer la photo" style="cursor:pointer" onClick="removePicture(\''.$idForPicture.'\')"></em>';
			echo "</li>";
		echo '</div>';
		*/
		wcmGUI::closeFieldset();
    
		wcmGUI::closeFieldset();
		wcmGUI::closeCollapsablePane();
		echo "</div>";
	echo "</div>";