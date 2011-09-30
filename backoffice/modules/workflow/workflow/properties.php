<?php
/**
 * Project:     WCM
 * File:        modules/workflow/workflow/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $project = wcmProject::getInstance();
    $sysobject = wcmMVC_Action::getContext();

    // Special actions
    $info  = '';
    if ($sysobject->id)
    {
        $info .= '<ul class="actions">';
        $info .= '<li><a href="'. wcmMVC_SysAction::computeObjectURL('wcmWorkflowTransition', 0, 'view', array('workflowId' => $sysobject->id));
        $info .= '">' . _NEW_WORKFLOW_TRANSITION . '</a></li>';
        $info .= '</ul>';
    }

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
        $states[$state->code] = getConst($state->name);
    }
    
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_PROPERTIES, true, $info);
    wcmGUI::openFieldset( _GENERAL);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderDropdownField('initialState', $states, $sysobject->initialState, _INITIAL_STATE);
    wcmGUI::renderDropdownField('script', $scripts, $sysobject->script, _WORKFLOW_SCRIPT);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';