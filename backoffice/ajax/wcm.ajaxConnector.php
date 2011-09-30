<?php

/**
 * Project:     WCM
 * File:        wcm.ajaxConnector.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * @see         wcm.ajaxConnector.js
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve query parameters
$connectorId = getArrayParameter($_REQUEST, "connectorId", null);
$command = getArrayParameter($_REQUEST, "command", null);
$tableReference = getArrayParameter($_REQUEST, "tableReference", null);

// Prepare ajax result
$html = null;

// Search connector by id
try
{
    $connector = $project->datalayer->getConnectorById($connectorId);
    if (!$connector)
    {
        $html = "<span class='error'>Invalid connector id " . $connectorId. "</span>";
    }
    else
    {
        // Execute specific command
        switch($command)
        {
        case "remove_dbtable":
            if ($connector->removeDBTable($tableReference))
                $html = "<span class='success'>Table " . $tableReference . " retir?e</span>";
            else
                $html = "<span class='error'>" . $connector->getErrorMsg() . "<br/>";
            break;

        case "import_dbtable":
            if ($connector->importDBTable($tableReference))
                $html = "<span class='success'>Table " . $tableReference . " import?e</span>";
            else
                $html = "<span class='error'>" . $connector->getErrorMsg() . "<br/>";
            break;

        case "alter_dbtable":
            if ($connector->alterDBTable($tableReference))
                $html = "<span class='success'>Table " . $tableReference . " modifi?e</span>";
            else
                $html = "<span class='error'>" . $connector->getErrorMsg() . "<br/>";
            break;

        case "drop_dbtable":
            if ($connector->dropDBTable($tableReference))
                $html = "<span class='success'>Table supprim?e</span>";
            else
                $html = "<span class='error'>" . $connector->getErrorMsg() . "<br/>";
            break;

        default:
            $html = "<span class='error'>Unexpected command " . $command . "</span>";
            break;
        }
    }
}
catch(Exception $e)
{
    $html = "Error : " . $e->getMessage();
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo "<response type=\"item\" id=\"connectorCommandResult_" . $tableReference . "\">";
echo "<![CDATA[";

// Return command result
echo $html;
echo "]]>";
echo "</response>\n";
echo "</ajax-response>\n";

?>
