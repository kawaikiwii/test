<?php

/**
 * Project:     WCM
 * File:        wcm.ajaxTreeview.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * @see         wcm.ajaxTreeview.js
 */

// Retrieve query parameters
$html = null;
$treeId = getArrayParameter($_REQUEST, "tree", null);
$path = stripslashes(getArrayParameter($_REQUEST, "path", null));
$command = getArrayParameter($_REQUEST, "command", null);

// Execute command on specific node and wcmTree
try
{
    $tree = new wcmTree($treeId);
    $tree->initFromSession();

    // Reload wcmTree ?
    if ($command == "reload")
    {
        $tree->refresh();
        // Save changes into session object
        $tree->saveIntoSession();
    }
    else
    {
        // Retrieve node from session object
        $node = $tree->getNodeByPath($path);
        if ($node)
        {
            // Execute command
            switch($command)
            {
            case "collapse":
                $node->collapse();
                break;

            case "expand":
                $node->refresh();
                $node->expand();
                break;

            case "refresh":
                $node->refresh();
                $node->expand();
                $node->select();
                break;

            case "select":
                // If there is no child, refresh node
                // as it may be the first select operation
                if ($node->childCount() == 0)
                    $node->refresh();
                $node->select();
                break;
            }

            // Save changes into session object
            $tree->saveIntoSession();

            // Render html node
            $html = $node->renderHTML();
        }
        else
        {
            $html = "Invalid path node : " . $treeId . " : " . $path . "<hr/>";
            $html .= textH8($tree->toXml());
        }
    }
}
catch(Exception $e)
{
    $html = "Error : " . $e->getMessage();
}

// Redirect ?
$url = getArrayParameter($_REQUEST, "url", null);
if ($url != null)
{
    header('Location: ' . urldecode($url));
    exit();
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
echo "<response type=\"item\" id=\"" . $path . "\">";
echo "<![CDATA[";

// Return content after xsl transformation
echo $html;
echo "]]>";
echo "</response>\n";
echo "</ajax-response>\n";

?>
