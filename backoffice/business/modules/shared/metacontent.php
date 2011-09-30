<?php
    $bizobject = wcmMVC_Action::getContext();

    /* @todo :: Auto suggest summary (Nsummarizer call)
    * loads modal, accept and refuse (cancel) buttons
    */
    
    //special actions
    $info = isset($params['info'])?$params['info']:'';
    
    wcmGUI::openCollapsablePane(_META_CONTENT, true, $info);
    wcmGUI::openFieldset();

    if (get_class($bizobject) == 'channel')
        wcmGUI::renderDropdownField('parentId', channel::getChannelHierarchy(), $bizobject->parentId, _BIZ_CHANNEL);

    if (property_exists($bizobject, 'channelId') && get_class($bizobject) != 'article')
        wcmGUI::renderDropdownField('channelId', channel::getChannelHierarchy(), $bizobject->channelId, _BIZ_CHANNEL);

    if (property_exists($bizobject, 'title'))
    {
        switch($bizobject->getClass())
        {
            case 'article':
                wcmGUI::renderTextField('title', $bizobject->title, _BIZ_HEADLINE . ' *', array('class' => 'type-req'));
                break;

            default:
                wcmGUI::renderTextField('title', $bizobject->title, _BIZ_TITLE . ' *', array('class' => 'type-req'));
                break;
        }
    }
    
    if (property_exists($bizobject, 'subtitle'))
        wcmGUI::renderTextField('subtitle', $bizobject->subtitle, _BIZ_SUBTITLE);

    if (property_exists($bizobject, 'suptitle'))
        wcmGUI::renderTextField('suptitle', $bizobject->suptitle, _BIZ_TEASER);

    if (property_exists($bizobject, 'abstract'))
    {
        wcmGUI::renderEditableField('abstract', $bizobject->abstract, _BIZ_ABSTRACT);
        echo '<a href="#" class="summary" onclick="openmodal(\'' . _BIZ_NSUMMARIZER . '\'); modalPopup(\'tme\',\'abstract\', \'\', getData(), \'abstract\');">'._BIZ_NSUMMARIZER.'</a>'; 
    }

    if (property_exists($bizobject, 'caption'))
        wcmGUI::renderEditableField('caption', $bizobject->caption, _BIZ_CAPTION); 

    if (property_exists($bizobject, 'description'))
        wcmGUI::renderEditableField('description', $bizobject->description, _BIZ_DESCRIPTION); 

    if (property_exists($bizobject, 'author'))
        wcmGUI::renderTextField('author', $bizobject->author, _BIZ_AUTHOR);
    
    if (property_exists($bizobject, 'titleh2'))
        wcmGUI::renderTextField('titleh2', $bizobject->titleh2, _BIZ_TITLE.' H2');
        
    if (property_exists($bizobject, 'titleh3'))
        wcmGUI::renderTextField('titleh3', $bizobject->titleh3, _BIZ_TITLE.' H3');

    if (property_exists($bizobject, 'credits'))
        wcmGUI::renderTextField('credits', $bizobject->credits, _BIZ_CREDITS);
        
    if (property_exists($bizobject, 'image_url'))
    {
		$idForPicture = 'article';
		$photoId = 'chapter_photo_'.$idForPicture;
		wcmGUI::renderHiddenField('image_url', $bizobject->image_url, array('id' => $photoId));
	    echo '<div id="photo_chapter">';
			$command = '';
			$kind = '';
			$id = '';
			echo '<li>';
			echo '<label>'.textH8(getConst(_BIZ_PHOTO)).'</label>';
			$selectedPicture = 'selectedPicture_'.$idForPicture;
			if ($bizobject->image_url)
				$src = $bizobject->image_url;
			else
				$src = 'img/none.gif';
			echo '<img style="float:left; margin-bottom: 5px" width="100px" id="'.$selectedPicture.'" src="'.$src.'" onClick="openmodal(\''.getConst(_BIZ_PHOTOS_ADD).'\', \'650\' ); modalPopup(\'choosePhoto\',\'choosePhoto\', null, \''.$idForPicture.'\'); return false;" style="cursor:pointer" alt="Click to choose" title="Click to choose">';
			echo '<em class="removePicture" alt="Supprimer la photo" title="Supprimer la photo" style="cursor:pointer" onClick="removePicture(\''.$idForPicture.'\')"></em>';
			echo "</li>";
			wcmGUI::renderTextField('image_credits', $bizobject->image_credits, _BIZ_CREDITS);
			wcmGUI::renderTextField('image_caption', $bizobject->image_caption, _BIZ_CAPTION);
	    echo '</div>';
	 }

    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
