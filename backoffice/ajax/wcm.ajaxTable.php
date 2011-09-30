<?php

/**
 * Project:     WCM
 * File:        wcm.ajaxTable.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * @see         wcm.ajaxTable.js
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Open current session
$session = wcmSession::getInstance();

// Retrieve query parameters
$tableId = getArrayParameter($_REQUEST, "tableId", null);
$command = getArrayParameter($_REQUEST, "command", null);

// Prepare ajax result
$html = null;

// Search table by id
try
{
    $table = $project->datalayer->getTableById($tableId);
    if (!$table)
    {
        $html = "<span class='error'>Invalid table id " . $tableId . "</span>";
    }
    else
    {
        // Execute specific command
        switch($command)
        {
        case "create_db":
            $failed = $table->createDB();
            if ($failed)
                $html = "<span class='error'>Erreur lors de la cr?ation de la table : " . $failed . "<br/>";
            else
                $html = "<span class='success'>Table " . $table->reference . " cr??e</span>";
            break;

        case "create_api":
            $failed = $table->createAPI($session);
            if ($failed)
                $html = "<span class='error'>Erreur lors de la cr?ation de l'api : " . $failed . "<br/>";
            else
                $html = "<span class='success'>Classe biz." . strtolower($table->name) . ".php cr??e</span>";
            break;
            break;

        case "create_bo":
            $failed = $table->createBO($session);
            if ($failed)
                $html = "<span class='error'>Erreur lors de la cr?ation de l'interface : " . $failed . "<br/>";
            else
                $html = "<span class='success'>Interface cr??e</span>";
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
echo "<response type=\"item\" id=\"tableCommandResult\">";
echo "<![CDATA[";

// Return command result
echo $html;
echo "]]>";
echo "</response>\n";
echo "</ajax-response>\n";

?>
