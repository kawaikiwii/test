<?php
/**
 * Project:     WCM
 * File:        modules/workflow/transition/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $project = wcmProject::getInstance();
    $sysobject = wcmMVC_Action::getContext();

    // Retrieve workflows
    $workflows = array();
    foreach ($project->workflowManager->getWorkflows() as $workflow)
    {
        $workflows[$workflow->id] = getConst($workflow->name);
    }

    // Retrieve workflow states
    $states = array(wcmWorkflowTransition::ANY_STATE => '(' . _ANY_STATE . ')',
                    wcmWorkflowTransition::SELF_STATE => '(' . _SELF_STATE . ')');
    foreach($project->workflowManager->getWorkflowStates() as $state)
    {
        $states[$state->code] = getConst($state->name);
    }

    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_PROPERTIES);
    wcmGUI::openFieldset( _GENERAL);
    wcmGUI::renderDropdownField('workflowId', $workflows, $sysobject->workflowId, _WORKFLOW);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('code', $sysobject->code, _CODE . ' *', array('class' => 'type-code-req'));
    wcmGUI::renderDropdownField('fromState', $states, $sysobject->fromState, _FROM_STATE);
    unset($states[wcmWorkflowTransition::ANY_STATE]);
    wcmGUI::renderDropdownField('toState', $states, $sysobject->toState, _TO_STATE);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';