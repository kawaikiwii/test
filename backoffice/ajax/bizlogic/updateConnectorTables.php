<?php
/**
 * Project:     WCM
 * File:        ajax/api/bizlogic/updateConnectorTables.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * @see         module/api/bizlogic/sysclass/properties.js
 * @see         module/api/bizlogic/bizclass/properties.js
 */

    // Retrieve parameters
    $connectorId = getArrayParameter($_REQUEST, 'id', null);
    $tableName   = getArrayParameter($_REQUEST, 'table', null);

    echo '<option value="0">(' .  _NONE . ')</option>';
    $connector = $project->datalayer->getConnectorById($connectorId);
    if ($connector)
    {
        $systemTables = wcmDatalayer::getSystemDBTables();
        foreach($connector->getSchema()->getTables() as $table)
        {
            $name = $table->getName();
            if (!(strpos($name, $connector->tablePrefix) === 0) || !in_array($name, $systemTables))
            {
                echo '<option value="'.$name.'"';
                if ($name == $tableName) echo ' selected="selected"';
                echo '>'.$name.'</option>';
            }
        }
    }