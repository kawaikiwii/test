<?php
/**
 * Project:     WCM
 * File:        modules/bizlogic/bizclass/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $project = wcmProject::getInstance();
    $config  = wcmConfig::getInstance();
    $sysobject = wcmMVC_Action::getContext();

    $tables = array('0' => '('._NONE.')');
    $connectors = array('0' => '('._NONE.')');
    $workflows = array('0' => '('._NONE.')');

    // Get connectors and tables
    foreach($project->datalayer->getConnectors() as $connector)
    {
        $connectors[$connector->id] = getConst($connector->name);

        // Add connector's tables
        if ($connector->id == $sysobject->connectorId)
        {
            $systemTables = wcmDatalayer::getSystemDBTables();
            $schema = $connector->getSchema();
            foreach($schema->getTables() as $table)
            {
                $name = $table->getName();
                if (!(strpos($name, $connector->tablePrefix) === 0) || !in_array($name, $systemTables))
                {
                    $tables[$name] = $name;
                }
            }
        }
    }

    // Get workflows
    foreach($project->workflowManager->getWorkflows() as $workflow)
    {
        $workflows[$workflow->id] = getConst($workflow->name);
    }

    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_PROPERTIES);
    wcmGUI::openFieldset( _GENERAL);
    wcmGUI::renderTextField('name', $sysobject->name, _NAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderTextField('className', $sysobject->className, _CLASSNAME . ' *', array('class' => 'type-req'));
    wcmGUI::renderDropdownField('connectorId', $connectors,
                                $sysobject->connectorId, _CONNECTOR,
                                array('onChange' => 'updateConnectorTables("'. $config['wcm.backOffice.url'] .'ajax/", this.value)'));
    wcmGUI::renderDropdownField('connectorTable', $tables,
                                $sysobject->connectorTable, _CONNECTOR_TABLE, array('id' => 'connectorTable'));
    wcmGUI::renderBooleanField('allowOptimisticLock', $sysobject->allowOptimisticLock, _ALLOW_OPTIMISTIC);
    wcmGUI::renderDropdownField('workflowId', $workflows,
                                $sysobject->workflowId, _WORKFLOW);
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';
