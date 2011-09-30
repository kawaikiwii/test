<?php
/**
 * Project:     WCM
 * File:        modules/editorial/article/chapters.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    // This uniq id will be use for the tinyMCE editor and the addPage after js function
    $uniqid = uniqid('chapter_text_');
    $page = getArrayParameter($params, 'page', new chapter());

    $menus = array(
                    getConst(_ADD_PAGE_AFTER) => '\'' . wcmModuleURL('business/editorial/article/chapter') . '\', null, this',
                    getConst(_DELETE_PAGE)    => 'removeChapter'
                    );

    $info = '<ul>';
    foreach ($menus as $title => $action)
    {
    	if ($title == getConst(_DELETE_PAGE))
    	{
            $info .= '<li><a href="#" onclick="'.$action.'(this, \''.$uniqid.'\'); return false;">' . $title . '</a></li>';
    	}
    	else
    	{        
    	   $info .= '<li><a href="#" onclick="addPage(' . $action . '); return false;">' . $title . '</a></li>';
    	}
    }
    $info .= '</ul>';
    
    // Cut title to 50 chars max
    $title = (isset($page->title)) ? _BIZ_CHAPTER . ' ' . $page->rank . ' :: ' . $page->title : _BIZ_NEW_CHAPTER;
    if (strlen($title) > 50)
        $title = substr($title, 0, 50) . '...';

    wcmGUI::openCollapsablePane($title, true, $info);
    wcmGUI::openFieldset('', array('id' => 'pageFieldset'. $page->id));
    wcmGUI::renderTextField('chapter_title[]', $page->title, _BIZ_PAGE_TITLE);
    wcmGUI::renderTextField('chapter_subtitle[]', $page->subtitle, _BIZ_PAGE_SUBTITLE);
    $idForPicture = uniqid();
    $photoId = 'chapter_photo_'.$idForPicture;
    wcmGUI::renderHiddenField('chapter_photo[]', $page->image_url, array('id' => $photoId));
    // @todo :: Auto suggest summary (Nsummarizer call) 
    // wcmGUI::renderEditableField('meta_description', $page->title, _BIZ_META_DESCRIPTION);
    wcmGUI::renderEditableField('chapter_text[]', $page->text, _BIZ_PAGE_CONTENT, array('id' => $uniqid));
    wcmGUI::renderTextField('chapter_author[]', $page->author, _BIZ_CHAPTER_AUTHOR);
    wcmGUI::renderTextField('chapter_company[]', $page->company, _BIZ_CHAPTER_COMPANY);
    wcmGUI::renderTextField('chapter_link[]', $page->link, _BIZ_WEB_LINKS);
    echo '<div id="photo_chapter">';
    $command = '';
    $kind = '';
    $id = '';
    echo '<li>';
	echo '<label>'.textH8(getConst(_BIZ_PHOTO)).'</label>';
	$selectedPicture = 'selectedPicture_'.$idForPicture;
	if ($page->image_url)
		$src = $page->image_url;
	else
		$src = 'img/none.gif';
    echo '<img style="float:left; margin-bottom: 5px" width="100px" id="'.$selectedPicture.'" src="'.$src.'" onClick="openmodal(\''.getConst(_BIZ_PHOTOS_ADD).'\', \'650\' ); modalPopup(\'choosePhoto\',\'choosePhoto\', \'' . $page->id . '\', \''.$idForPicture.'\'); return false;" style="cursor:pointer" alt="Click to choose" title="Click to choose">';
    echo '<em class="removePicture" alt="Supprimer la photo" title="Supprimer la photo" style="cursor:pointer" onClick="removePicture(\''.$idForPicture.'\')"></em>';
    echo "</li>";
    wcmGUI::renderTextField('chapter_photo_credit[]', $page->image_credits, _BIZ_CREDITS);
    wcmGUI::renderTextField('chapter_photo_caption[]', $page->image_caption, _BIZ_CAPTION);
    echo '</div>';
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
