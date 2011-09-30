<?php
/**
 * Project:     WCM
 * File:        modules/editorial/work/editor.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    
    $contents = ($bizobject->id) ? $bizobject->getContents() : array(new content());

    echo '<div class="zone">';

	echo '<div id="contents" style="clear: both;">';
	
	foreach ($contents as $content)
    {
        wcmModule('business/editorial/work/content', array('content' => $content, 'bizObjectClass' => $bizobject->getClass()));
    }
    echo '</div>';

    wcmGUI::openFieldset();
    $onChange = "for(i=0;i<options.length;i++) {if (document.getElementById(options[i].value) != null) {document.getElementById(options[i].value).style.display='none';}};if (document.getElementById(options[selectedIndex].value) != null) {document.getElementById(options[selectedIndex].value).style.display='';}";
	wcmGUI::renderDropdownField('type', $bizobject->getTypeList(), $bizobject->type, _BIZ_TYPE, array('onChange'=>$onChange));
	wcmGUI::closeFieldset();

	
	if (!empty($bizobject->specific))
    	$workInfos = unserialize($bizobject->specific);
    
    // Attention l'ajout des id/noms zones doit être conforme au code contenu dans la liste référence
    
    // CD est la zone affichée par défaut
    /*************************** CD ************************************/
    $cssDisplay = "none";
    if ((!empty($bizobject->type) && $bizobject->type == 'cd') || empty($bizobject->type)) $cssDisplay = "\'\'";
    echo '<div id="cd" style="display:'.$cssDisplay.'">';
	wcmGUI::openCollapsablePane(getConst("_LIST_CD"));
	wcmGUI::openFieldset('');
	
	wcmGUI::renderDropdownField('cd_otype', $bizobject->getCdOutputType(), isset($workInfos['otype'])?$workInfos['otype']:null, 'Type');
	wcmGUI::renderTextField('cd_titlenb'	, isset($workInfos['titlenb'])?$workInfos['titlenb']:null, 'Tracks numbers');
	wcmGUI::renderTextArea('cd_summary'	, isset($workInfos['summary'])?$workInfos['summary']:null, 'Summary');
	
	echo '<div id="cdstylefields">';
	wcmGUI::openFieldset("Music Style");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workcdmusicstyle.php',
                       'paramName' => 'prefix', 'parameters' => 'type=cdMusicStyle', 'tokens' => ',');
    wcmGUI::renderCommonListField('cd_musicStyle', isset($workInfos['musicStyle'])?$workInfos['musicStyle']:null, null, $acOptions);
	echo '</div>';
	
	echo '<div id="cdlabelsfields">';
	wcmGUI::openFieldset("Label");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workcdlabellist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=cdcountry', 'tokens' => ',');
    wcmGUI::renderCommonListField('cd_label', isset($workInfos['label'])?$workInfos['label']:null, null, $acOptions);
	echo '</div>';
	
	echo '<div id="cdcountryfields">';
	wcmGUI::openFieldset("Country");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workcountrylist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=cdcountry', 'tokens' => ',');
    wcmGUI::renderCommonListField('cd_country', isset($workInfos['country'])?$workInfos['country']:null, null, $acOptions, "work_country");
	echo '</div>';
	
	wcmGUI::closeFieldset();
	wcmGUI::closeCollapsablePane();
	echo '</div>';
	
    /*************************** CINEMA ************************************/
	$cssDisplay = "none";
    if (!empty($bizobject->type) && $bizobject->type == 'cinema') $cssDisplay = "\'\'";
	echo '<div id="cinema" style="display:'.$cssDisplay.'">';
	wcmGUI::openCollapsablePane(getConst("_LIST_CINEMA"));
	wcmGUI::openFieldset('');
	wcmGUI::renderTextField('cinema_originalTitle', isset($workInfos['originalTitle'])?$workInfos['originalTitle']:null, 'Original Title');
	wcmGUI::renderTextField('cinema_duration', isset($workInfos['duration'])?$workInfos['duration']:null, 'Duration');
	wcmGUI::renderTextField('cinema_copies', isset($workInfos['copies'])?$workInfos['copies']:null, 'Copies');
	wcmGUI::renderTextField('cinema_director'	, isset($workInfos['director'])?$workInfos['director']:null, 'Director');
	wcmGUI::renderTextField('cinema_casting'	, isset($workInfos['casting'])?$workInfos['casting']:null, 'Casting');
	wcmGUI::renderTextArea('cinema_summary'	, isset($workInfos['summary'])?$workInfos['summary']:null, 'Summary');
	wcmGUI::closeFieldset();
	
	echo '<div id="cinemagenderfields">';
	wcmGUI::openFieldset("Gender");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workmoviegenderlist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=cinemagender');
    wcmGUI::renderCommonListField('cinema_gender', isset($workInfos['gender'])?$workInfos['gender']:null, null, $acOptions);
	wcmGUI::closeFieldset();
    echo '</div>';
	
    // specific field used for biz_relation composed of destination class name '_' kind
    echo '<fieldset style="margin-left:5px">';
	echo "<legend>Distributed by</legend>";
	echo "<ul>";
		echo "<li>";
		$acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.organisations.php',
						   'paramName' => 'prefix',
						   'parameters' => '',
						   'className' => 'organisation');
		relaxGUI::renderRelaxListField('cinema_organisation_distributed', work::getRelationObjectIdByKindForGui($bizobject, bizrelation::IS_DISTRIBUTED_BY), array('style' => 'float:none;'), $acOptions);
		echo "</li>";
	echo "</ul>";
	echo "</fieldset>";
    
    
	echo '<div id="countryfields">';
	wcmGUI::openFieldset("Country");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workcountrylist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=country', 'tokens' => ',');
    wcmGUI::renderCommonListField('cinema_country', isset($workInfos['country'])?$workInfos['country']:null, null, $acOptions);
	echo '</div>';
	
	wcmGUI::closeFieldset();
	wcmGUI::closeCollapsablePane();
	echo '</div>';
	
	/*************************** VIDEO ************************************/
	$cssDisplay = "none";
    if (!empty($bizobject->type) && $bizobject->type == 'video') $cssDisplay = "\'\'";
	echo '<div id="video" style="display:'.$cssDisplay.'">';
	wcmGUI::openCollapsablePane(getConst("_LIST_DVD"));
	wcmGUI::openFieldset('');
	wcmGUI::renderTextField('video_originalTitle', isset($workInfos['originalTitle'])?$workInfos['originalTitle']:null, 'Original Title');
	wcmGUI::renderTextField('video_duration', isset($workInfos['duration'])?$workInfos['duration']:null, 'Duration');
	wcmGUI::renderTextField('video_copies', isset($workInfos['copies'])?$workInfos['copies']:null, 'Copies');
	wcmGUI::renderTextField('video_director'	, isset($workInfos['director'])?$workInfos['director']:null, 'Director');
	wcmGUI::renderTextField('video_casting'	, isset($workInfos['casting'])?$workInfos['casting']:null, 'Casting');
	wcmGUI::renderDropdownField('video_format', $bizobject->getMovieFormats(), isset($workInfos['format'])?$workInfos['format']:null, 'Format');
	wcmGUI::renderTextField('video_bonus'		, isset($workInfos['bonus'])?$workInfos['bonus']:null, 'Bonus');
	wcmGUI::renderTextField('video_price'		, isset($workInfos['price'])?$workInfos['price']:null, 'Price');
	wcmGUI::renderTextArea('video_summary'	, isset($workInfos['summary'])?$workInfos['summary']:null, 'Summary');
	wcmGUI::closeFieldset();
	
	echo '<div id="moviegenderfields">';
	wcmGUI::openFieldset("Gender");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workmoviegenderlist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=moviegender');
    wcmGUI::renderCommonListField('video_gender', isset($workInfos['gender'])?$workInfos['gender']:null, null, $acOptions);
	wcmGUI::closeFieldset();
    echo '</div>';
	
    echo '<fieldset style="margin-left:5px">';
	echo "<legend>Distributed by</legend>";
	echo "<ul>";
		echo "<li>";
		$acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.organisations.php',
						   'paramName' => 'prefix',
						   'parameters' => '',
						   'className' => 'organisation');
		relaxGUI::renderRelaxListField('video_organisation_distributed', work::getRelationObjectIdByKindForGui($bizobject, bizrelation::IS_DISTRIBUTED_BY), array('style' => 'float:none;'), $acOptions);
		echo "</li>";
	echo "</ul>";
	echo "</fieldset>";
	
	echo '<div id="countryfields">';
	wcmGUI::openFieldset("Country");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workcountrylist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=country', 'tokens' => ',');
    wcmGUI::renderCommonListField('video_country', isset($workInfos['country'])?$workInfos['country']:null, null, $acOptions);
	echo '</div>';
	
	wcmGUI::closeFieldset();
	wcmGUI::closeCollapsablePane();
	echo '</div>';
	
	/*************************** BOOK ************************************/
    $cssDisplay = "none";
    if (!empty($bizobject->type) && $bizobject->type == 'book') $cssDisplay = "\'\'";
	echo '<div id="book" style="display:'.$cssDisplay.'">';
	wcmGUI::openCollapsablePane(getConst("_LIST_BOOK"));
	wcmGUI::openFieldset('');
	wcmGUI::renderTextField('book_price'	, isset($workInfos['price'])?$workInfos['price']:null, 'Price');
	wcmGUI::renderTextField('book_pagenb'	, isset($workInfos['pagenb'])?$workInfos['pagenb']:null, 'Page Number');
	wcmGUI::renderTextArea('book_summary'	, isset($workInfos['summary'])?$workInfos['summary']:null, 'Summary');
	
	wcmGUI::renderRadiosField('book_format', array("pocket size", "large size"), isset($workInfos['format'])?$workInfos['format']:null, 'Format');
    
	echo '<div id="bookgenderfields">';
	wcmGUI::openFieldset("Gender");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workbookgenderlist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=bookgender');
    wcmGUI::renderCommonListField('book_gender', isset($workInfos['gender'])?$workInfos['gender']:null, null, $acOptions);
	wcmGUI::closeFieldset();
    echo '</div>';
    
    echo '<div id="bookthemefields">';
	wcmGUI::openFieldset("Theme");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workbookthemelist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=booktheme');
    wcmGUI::renderCommonListField('book_theme', isset($workInfos['theme'])?$workInfos['theme']:null, null, $acOptions);
	wcmGUI::closeFieldset();
    echo '</div>';
    
    echo '<fieldset style="margin-left:5px">';
	echo "<legend>Edited by</legend>";
	echo "<ul>";
		echo "<li>";
		$acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.organisations.php',
						   'paramName' => 'prefix',
						   'parameters' => '',
						   'className' => 'organisation');
		relaxGUI::renderRelaxListField('book_organisation_edited', work::getRelationObjectIdByKindForGui($bizobject, bizrelation::IS_EDITED_BY), array('style' => 'float:none;'), $acOptions);
		echo "</li>";
	echo "</ul>";
	echo "</fieldset>";
	
	echo '<div id="countryfields">';
	wcmGUI::openFieldset("Country");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workcountrylist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=country', 'tokens' => ',');
    wcmGUI::renderCommonListField('book_country', isset($workInfos['country'])?$workInfos['country']:null, null, $acOptions);
	echo '</div>';
	
	wcmGUI::closeFieldset();
	wcmGUI::closeCollapsablePane();
	echo '</div>';
    
	/*************************** PRODUCT ************************************/
    $cssDisplay = "none";
    if (!empty($bizobject->type) && $bizobject->type == 'product') $cssDisplay = "\'\'";
	echo '<div id="product" style="display:'.$cssDisplay.'">';
	wcmGUI::openCollapsablePane(getConst("_LIST_PRODUCT"));
	wcmGUI::openFieldset('');
	wcmGUI::renderTextField('product_author'	, isset($workInfos['author'])?$workInfos['author']:null, 'Author');
	wcmGUI::renderTextField('product_producer'	, isset($workInfos['producer'])?$workInfos['producer']:null, 'Producer');
	wcmGUI::renderTextArea('product_summary'	, isset($workInfos['summary'])?$workInfos['summary']:null, 'Summary');
	
	echo '<div id="countryfields">';
	wcmGUI::openFieldset("Country");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workcountrylist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=country', 'tokens' => ',');
    wcmGUI::renderCommonListField('product_country', isset($workInfos['country'])?$workInfos['country']:null, null, $acOptions);
	echo '</div>';
	
	
	wcmGUI::closeFieldset();
	wcmGUI::closeCollapsablePane();
	echo '</div>';
	
	/*************************** VIDEO GAME ************************************/
    $cssDisplay = "none";
    if (!empty($bizobject->type) && $bizobject->type == 'videogame') $cssDisplay = "\'\'";
	echo '<div id="videogame" style="display:'.$cssDisplay.'">';
	wcmGUI::openCollapsablePane(getConst("_LIST_VIDEOGAME"));
	wcmGUI::openFieldset('');
	wcmGUI::renderTextField('videogame_developer'	, isset($workInfos['developer'])?$workInfos['developer']:null, 'Developer');
	wcmGUI::renderTextField('videogame_public'	, isset($workInfos['public'])?$workInfos['public']:null, 'Public');
	wcmGUI::renderTextField('videogame_plateforms'	, isset($workInfos['plateforms'])?$workInfos['plateforms']:null, 'Plateforms');
	wcmGUI::renderTextField('videogame_price'	, isset($workInfos['price'])?$workInfos['price']:null, 'Price');
	wcmGUI::renderTextArea('videogame_summary'	, isset($workInfos['summary'])?$workInfos['summary']:null, 'Summary');
	
	echo '<fieldset style="margin-left:5px">';
	echo "<legend>Edited by</legend>";
	echo "<ul>";
		echo "<li>";
		$acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.organisations.php',
						   'paramName' => 'prefix',
						   'parameters' => '',
						   'className' => 'organisation');
		relaxGUI::renderRelaxListField('videogame_organisation_edited', work::getRelationObjectIdByKindForGui($bizobject, bizrelation::IS_EDITED_BY), array('style' => 'float:none;'), $acOptions);
		echo "</li>";
	echo "</ul>";
	echo "</fieldset>";
	
	echo '<div id="videogamegenderfields">';
	wcmGUI::openFieldset("Gender");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workvideogamegenderlist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=videogamegender');
    wcmGUI::renderCommonListField('videogame_gender', isset($workInfos['gender'])?$workInfos['gender']:null, null, $acOptions);
	wcmGUI::closeFieldset();
    echo '</div>';
	
	echo '<div id="videogamecountryfields">';
	wcmGUI::openFieldset("Country");
    $acOptions = array('url' => $config['wcm.backOffice.url'] . 'business/ajax/autocomplete/wcm.workcountrylist.php',
                       'paramName' => 'prefix', 'parameters' => 'type=videogamecountry', 'tokens' => ',');
    wcmGUI::renderCommonListField('videogame_country', isset($workInfos['country'])?$workInfos['country']:null, null, $acOptions);
	echo '</div>';
	
	wcmGUI::closeFieldset();
	wcmGUI::closeCollapsablePane();
	echo '</div>';
	
    echo '</div>';