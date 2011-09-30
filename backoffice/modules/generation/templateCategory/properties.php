<?php
/**
 * Project:     WCM
 * File:        modules/generation/templateCategory/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $config = wcmConfig::getInstance();
    $sysobject = wcmMVC_Action::getContext();
    $categoryId = (!$sysobject->categoryId && isset($_REQUEST['parentId']))?$_REQUEST['parentId']:$sysobject->categoryId;
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_CATEGORY);
    wcmGUI::openFieldset( _GENERAL);
    //wcmGUI::renderTextField('nameId', $sysobject->id, _ID);
    $url = $config['wcm.backOffice.url'] . 'ajax/autocomplete/wcm.templateCategories.php';
    $acOptions = array('url' => $url,
                       'paramName' => 'prefix',
                       'parameters' => '');
    wcmGUI::renderAutoCompletedField($url, 'categoryId', str_replace($sysobject->name, "", $sysobject->id), _CATEGORY, null, $acOptions);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';