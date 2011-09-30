<?php
/**
 * Project:     WCM
 * File:        modules/generation/generationSet/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $project = wcmProject::getInstance();
    $sysobject = wcmMVC_Action::getContext();

    // prepare sub menues
    $info = '';
    $info .= '<ul>';
    $info .= '<li><a href="'. wcmMVC_Action::computeObjectURL('wcmGeneration', 0, 'view', array('generationSetId' => $sysobject->id));
    $info .= '">' . _NEW_GENERATION . '</a></li>';
    $info .= '<li><a href="'. wcmDialogUrl('generate', 'rule='.$sysobject->id);
    $info .= '">' . _EXECUTE . '</a></li>';
    $info .= '</ul>';
    
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_GENERAL, true, $info);
    wcmGUI::openFieldset(_PROPERTIES);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('code', $sysobject->code, _CODE . ' *', array('class' => 'type-code-req'));
    wcmGUI::renderTextField('location', $sysobject->location, _LOCATION);
    wcmGUI::renderTextField('context', $sysobject->context, _CONTEXT);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';
