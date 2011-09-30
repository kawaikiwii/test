<?php
/**
 * Project:     WCM
 * File:        modules/workflow/state/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $project = wcmProject::getInstance();
    $sysobject = wcmMVC_Action::getContext();

    // Retrieve workflow scripts
    $scripts = array('0' => '(' . _NONE . ')');
    foreach($project->workflowManager->getWorkflowScripts() as $script)
    {
        $scripts[$script] = $script;
    }

    // Retrieve workflow states
    $states = array();
    foreach($project->workflowManager->getWorkflowStates() as $state)
    {
        $states[$state->id] = getConst($state->name);
    }
    
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_PROPERTIES);
    wcmGUI::openFieldset( _GENERAL);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('code', $sysobject->code, _CODE . ' *', array('class' => 'type-code-req'));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';