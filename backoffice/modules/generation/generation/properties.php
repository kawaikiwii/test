<?php
/**
 * Project:     WCM
 * File:        modules/generation/generation/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $project = wcmProject::getInstance();
    $sysobject = wcmMVC_Action::getContext();

    // prepare sub menues
    $info = '';
    $info .= '<ul>';
    $info .= '<li><a href="'. wcmMVC_SysAction::computeObjectURL('wcmGenerationContent', 0, 'view', array('generationId' => $sysobject->id));
    $info .= '">' . _NEW_GENERATION_CONTENT . '</a></li>';
    $info .= '<li><a href="'. wcmDialogUrl('generate', 'rule='.$sysobject->generationSetId.':'.$sysobject->id);
    $info .= '">' . _EXECUTE . '</a></li>';
    $info .= '</ul>';

    // Retrieve available generation sets
    $genSets = array();
    foreach ($project->generator->getGenerationSets() as $genSet)
    {
        $genSets[$genSet->id] = getConst($genSet->name);
    }
    
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_GENERAL, true, $info);
    wcmGUI::openFieldset(_PROPERTIES);
    wcmGUI::renderDropdownField('generationSetId', $genSets, $sysobject->generationSetId, _GENERATION_SET);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('code', $sysobject->code, _CODE . ' *', array('class' => 'type-code-req'));
    wcmGUI::renderTextField('location', $sysobject->location, _LOCATION);
    wcmGUI::renderTextField('context', $sysobject->context, _CONTEXT);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';
