<?php
/**
 * Project:     WCM
 * File:        modules/menu/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */
    $project = wcmProject::getInstance();
    $config  = wcmConfig::getInstance();
    $sysobject = wcmMVC_Action::getContext();

    // Special actions
    $info  = '';
    if ($sysobject->id)
    {
        $info .= '<ul class="actions">';
        $info .= '<li><a href="'. wcmMVC_SysAction::computeObjectURL('wcmMenu', 0, 'view', array('parentId' => $sysobject->id));
        $info .= '">' . _NEW_SUBMENU . '</a></li>';
        $info .= '</ul>';
    }    

    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_PROPERTIES, true, $info);
    wcmGUI::openFieldset( _GENERAL);
    
    // Get menus hierarchy except for root menu
    if ($sysobject->id && !$sysobject->parentId)
    {
        $menus = array('' => '(' . _AT_ROOT_LEVEL . ')'); 
    }
    else
    {
        $menus = array();
        $project->layout->getMenuHierarchy($menus, $sysobject->id);
    }
    
    wcmGUI::renderDropdownField('parentId', $menus, $sysobject->parentId, _PARENT_MENU);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('rank', $sysobject->rank, _RANK, array('class' => 'type-int'));
    wcmGUI::renderTextField('_action', $sysobject->action, _ACTION);
    wcmGUI::renderTextField('url', $sysobject->url, _URL);
    
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';