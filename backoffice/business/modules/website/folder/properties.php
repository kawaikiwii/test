<?php

/**
 * Project:     WCM
 * File:        modules/channel/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
	//$folders = array('0' => _BIZ_ROOT_ELEMENT );
	$folders = array();
	
	$bizobject->getFolderHierarchy($folders, $bizobject->id);
	
	$typeList = folder::getTypeList();
	
    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_META_CONTENT);

    wcmGUI::openCollapsableFieldset(_GENERAL);
    //wcmGUI::renderDropdownField('parentId', $folders, $bizobject->parentId, _BIZ_PARENT_FOLDER);
	wcmGUI::renderTextField('title', $bizobject->title, _BIZ_TITLE . ' *', array('class' => 'type-req'));
	$onChange = "var types = ['".implode("','",array_keys($typeList))."'];for (var i=0;i<types.length;i++){\$(types[i]).hide();}\$(this.options[this.selectedIndex].value).show();";
	wcmGUI::renderDropdownField('type', $typeList, $bizobject->type, _TYPE,array('onChange'=>$onChange));
    wcmGUI::renderTextField('rank', $bizobject->rank, _BIZ_POSITION, array('class' => 'type-int'));
    wcmGUI::renderTextField('css', $bizobject->css, _BIZ_CSS);
	wcmGUI::renderTextArea('description', $bizobject->description, _BIZ_DESCRIPTION);
	wcmGUI::closeFieldset();    
    wcmGUI::closeCollapsablePane();

    //$temps_debut = microtime(true);
	//0.0036 ------ 0.0012
    //$dataStored = wcmCache::fetch('ArrayObjectsStored');
    //print_r($dataStored);
    
    //0.0368 ------ 0.0135
    //$dataStored = $bizobject->storeObjects($bizobject->getClass(),false);
    //print_r($dataStored);
    
    // 6.3463 de génération fichier
    //$temps_fin = microtime(true);
	//echo 'Temps d\'execution : '.round($temps_fin - $temps_debut, 4);

    /*wcmGUI::openCollapsablePane(_PUBLICATION_GENERATION);
    wcmGUI::openFieldset( _LIFETIME);
    wcmGUI::renderDateField('publicationDate',_BIZ_PUBLICATIONDATE,_BIZ_PUBLICATIONDATE);
    wcmGUI::renderDateField('expirationDate', _BIZ_EXPIRATIONDATE,_BIZ_EXPIRATIONDATE);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    */
    echo '</div>';
