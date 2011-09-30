<?php

/**
 * Project:     WCM
 * File:        mod_channel_content.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * folder Content
 *
 * Display all the content of the folder (forced or by date AND result of the request)
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    
    //save folder in the session for ajax calls

    $prefix = "_wcm_folderRel_";
    
    $config = wcmConfig::getInstance();
    
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_BIZ_SECTION_PROMO_PANEL);
    // @todo :: Relation picker that controls what is displayed in the promotional panel
    // Promotional panel can be a dynamic flash rotating panel, dynamic HTML panel, tabbed panel, etc.
    wcmGUI::closeCollapsablePane();

    /*
    // @todo :: Overide position with any BizObjects by pick-and-choose 
    // Functionnality already implemented in 3.2
    wcmGUI::openCollapsablePane(_BIZ_SECTION_CONTENT_RULES);
    wcmGUI::openCollapsableFieldset(_BIZ_SECTION_CONTENT_RULES_MANAGEMENT);
    // @todo :: Content filing rules (query builder?)
    // Functionnality already implemented in 3.2
    $specifyAttributes = array('onchange' => 'toggleQueryBuilder(this, \'queryBuilder\')');
    wcmGUI::renderBooleanField('specifyQuery', false, _BIZ_SPECIFY_QUERY, $specifyAttributes);
    wcmGUI::renderBooleanField('fixedContent', false, _BIZ_FORCED_folder);
    $dateAttributes = array('style' => 'margin-left: 30px;', 'onchange' => 'toggleQueryBuilder(this, \'queryCalendar\')');
    wcmGUI::renderBooleanField('dateManagement', false, _BIZ_MANAGEMENT_BY_DATE, $dateAttributes);
    wcmGUI::closeFieldset();


    wcmGUI::openFieldset(_BIZ_QUERY, array('class' => 'queryCalendar', 'style' => 'display: none;'));
    $calendar = new wcmHtmlCalendar();
    echo $calendar->render('cal', null, 'date', null, false, "en", 'calendar-mos', 'Y-m-d', 'H:i', true);
    wcmGUI::closeFieldset();
    */
    
    
    wcmGUI::openFieldset(_BIZ_QUERY, array('class' => 'queryBuilder'));
    $savedSearchAction = getArrayParameter($_SESSION, 'savedSearchAction', 'showSavedSearches');
    $savedSearchControl = new savedSearchControl();
    $savedSearches = array();
    $savedSearches[] = '(' . _BIZ_CHOOSE_QUERY . ')';
    foreach($savedSearchControl->getSavedSearches($savedSearchAction) as $search)
    {
        $savedSearches[$search['queryString']] = $search['name'];
    }
    
    //wcmGUI::renderDropdownField('savedQuery', $savedSearches, _BIZ_CHOOSE_QUERY, _BIZ_MY_SAVED_SEARCHES, array("onchange" => "javascript:savedSearchload();"));
    
    $search_query = unserialize($bizobject->request); 
    wcmGUI::renderTextField('query', isset($search_query['query'])?$search_query['query']:null, _BIZ_NEW_QUERY);
    wcmGUI::renderTextField('orderBy', isset($search_query['orderBy'])?$search_query['orderBy']:null, _BIZ_ORDER_BY);
    wcmGUI::renderTextField('limit', isset($search_query['limit'])?$search_query['limit']:null, _BIZ_LIMIT);

   
    wcmGUI::closeFieldset();
    
    /*
    wcmGUI::openFieldset(_BIZ_CONTENT);
    wcmModule('business/relationship/mainfolder', 
        array('kind' => wcmBizrelation::IS_COMPOSED_OF,
              'destinationClass' => '',
              'classFilter' => '',
              'resultStyle' => 'grid',
              'prefix' => $prefix,
              'searchEngine' => $config['wcm.search.engine'],
              'uid' => 'folderRelations',
			  'createTab' => false,
              'createModule' => 'business/subForms/uploadPhoto'
			));
    
	wcmGUI::closeFieldset();
    */
    /*
    @todo :: options
    wcmGUI::openCollapsableFieldset(_BIZ_SECTION_CONTENT_RULES_OPTIONS);
    select :: Number of documents display on the section page: 10, 20, 30 
    select :: Number of documents per archive page: 10, 20, 30, 40, 50 
	wcmGUI::closeFieldset();
    */
    wcmGUI::closeCollapsablePane();

    echo '</div>';
