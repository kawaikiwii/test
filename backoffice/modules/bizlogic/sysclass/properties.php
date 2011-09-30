<?php
/**
 * Project:     WCM
 * File:        modules/bizlogic/sysclass/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $project = wcmProject::getInstance();
    $config  = wcmConfig::getInstance();
    $sysobject = wcmMVC_Action::getContext();

    // Get workflows
    $workflows = array('0' => '('._NONE.')');
    foreach($project->workflowManager->getWorkflows() as $workflow)
    {
        $workflows[$workflow->id] = getConst($workflow->name);
    }

    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_PROPERTIES);
    wcmGUI::openFieldset( _GENERAL);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('className', $sysobject->className, _CLASSNAME . ' *', array('class' => 'type-req', 'disabled' => 'disabled'));
    wcmGUI::renderBooleanField('allowOptimisticLock', $sysobject->allowOptimisticLock, _ALLOW_OPTIMISTIC);
    wcmGUI::renderDropdownField('workflowId', $workflows,
                                $sysobject->workflowId, _WORKFLOW);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';