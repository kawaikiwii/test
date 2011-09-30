<?php

/**
 * Project:     WCM
 * File:        biz.manageChannel.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Initialize return values
$message    = null;
$renderIds  = null;
$xslMode    = "list";

// Retrieve parameters for the bizrelation object
// Creating a new bizrelation

// TODO 
// correct workaround with urldecode.

$bizrelation                    = new bizrelation($project);
$bizrelation->sourceClass       = getArrayParameter($_REQUEST, "sourceClass", null);
$bizrelation->sourceId          = getArrayParameter($_REQUEST, "sourceId", 0);
$bizrelation->destinationClass  = getArrayParameter($_REQUEST, "destinationClass", null);
$bizrelation->destinationId     = getArrayParameter($_REQUEST, "destinationId", null);
$bizrelation->validityDate      = getArrayParameter($_REQUEST, "validityDate", null);
$bizrelation->expirationDate    = getArrayParameter($_REQUEST, "expirationDate", null);
$bizrelation->kind              = getArrayParameter($_REQUEST, "kind", 3);
$bizrelation->header            = urldecode(getArrayParameter($_REQUEST, "header", null));
$bizrelation->title             = urldecode(getArrayParameter($_REQUEST, "title", null));

// Retrieve others parameters
$command                        = getArrayParameter($_REQUEST, "command", null);
$messageId                      = getArrayParameter($_REQUEST, "messageId", null);
$from                           = getArrayParameter($_REQUEST, "from", 0);
$to                             = getArrayParameter($_REQUEST, "to", 0);
$locked                         = getArrayParameter($_REQUEST, "locked", "false");

// Name of the div
$divName                        = getArrayParameter($_REQUEST, "divName", "lien_");

// Date selected by the user to be managed
$dateDisplayed                  = getArrayParameter($_SESSION, "date", $bizrelation->validityDate);

$nearestDate = null;

// Boolean value that will display the appropriate message in case of a managing instruction was executed
$wasManaged = false;

// If there is a managing instruction and no bizrelation exists for that day, all bizrelations from the nearest
// day are copied.
if($command == 'move' OR $command == 'update' OR $command == 'insert' OR $command == 'remove')
{
    // Clone the bizrelation object to use it for the copy process
    $bizrelationCopy = clone($bizrelation);

    // Check if there is an empty kind relation for that day, delete it if it exists
    if($bizrelationCopy->exist($bizrelation->validityDate, bizrelation::IS_EMPTY))
    {
        $bizrelationCopy->removeOneKind(bizrelation::IS_EMPTY);
    }
    // If no empty relation, then copy the bizrelations from the latest content
    else
    {
        $nearestDate = $bizrelationCopy->getNearestDate();

        if($bizrelationCopy->exist($bizrelationCopy->validityDate, bizrelation::IS_COMPOSED_OF) == false)
        {
            global $displayError;
            $displayError = true;
            $bizrelationCopy->copy($nearestDate, $bizrelationCopy->validityDate);
            $displayError = false;
            // Set up a boolean variable that indicates a copy was done. This is done to display the appropriate message.
            $wasManaged = true;
        }
    }
}

switch($command)
{
// Create a bizrelation of kind bizrelation::IS_EMPTY for this particular day
case "empty":
    if($bizrelation->removeOneKind(bizrelation::IS_COMPOSED_OF))
    {
        $renderIds = "rank > 0";
        if($bizrelation->sourceId != 0 and $bizrelation->sourceClass != null)
            $renderIds .= " AND sourceId = '".$bizrelation->sourceId."' AND sourceClass = '".$bizrelation->sourceClass."' AND  kind = '".$bizrelation->kind."'";

        if(!$bizrelation->exist($bizrelation->validityDate, IS_EMPTY))
        {
            if (!$bizrelation->insert(null, null, bizrelation::IS_EMPTY, null, null))
                $message = "BizRelation insert has failed : " . $bizrelation->_lastErrorMsg;
        }
    }
    else
        $message = "BizRelation removeOneKind has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "reset":
    $bizrelation->copy($bizrelation->getNearestDate(), $bizrelation->validityDate);
    
    if($bizrelation->removeOneKind(bizrelation::IS_EMPTY) == false)
        $message = "BizRelation removeOneKind has failed : " . $bizrelation->_lastErrorMsg;

    $renderIds = "rank > 0 AND validityDate='" . $bizrelation->validityDate . "'";
    if($bizrelation->sourceId != 0 and $bizrelation->sourceClass != null)
        $renderIds .= " AND sourceId = '".$bizrelation->sourceId."' AND sourceClass = '".$bizrelation->sourceClass."' AND  kind = '".$bizrelation->kind."'";
            
    break;

case "move":
    if ($bizrelation->move($from, $to))
    {
        // Set up a boolean variable that indicates a copy was done. This is done to display the appropriate message.
        $wasManaged = true;

        $renderIds = "rank > 0";
        if($bizrelation->sourceId != 0 and $bizrelation->sourceClass != null)
            $renderIds .= " AND sourceId = '".$bizrelation->sourceId."' AND sourceClass = '".$bizrelation->sourceClass."' AND  kind = '".$bizrelation->kind."'";
    }
    else
        $message = "BizRelation move has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "update":
    if ($bizrelation->update($bizrelation->destinationClass, $bizrelation->destinationId, $bizrelation->kind, $bizrelation->header, $bizrelation->title, $from))
    {
        // Refresh all items as a similar destination  object may already exists
        $renderIds = "rank > 0";
        $renderIds .= " AND sourceId = '".$bizrelation->sourceId."' AND sourceClass = '".$bizrelation->sourceClass."' AND  kind = '".$bizrelation->kind."'";
    }
    else
        $message = "BizRelation update has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "insert":
    if ($bizrelation->insert($bizrelation->destinationClass, $bizrelation->destinationId, $bizrelation->kind, $bizrelation->header, $bizrelation->title))
    {
        // Set up a boolean variable that indicates an insert was done. This is done to display the appropriate message.
        $wasManaged = true;

        // Refresh all items as a similar destination  object may already exists
        $renderIds = "rank > 0";
        $renderIds .= " AND sourceId = '".$bizrelation->sourceId."' AND sourceClass = '".$bizrelation->sourceClass."' AND  kind = '".$bizrelation->kind."'";
    }
    else
        $message = "BizRelation insert has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "remove":
    if ($bizrelation->remove($from))
    {
        $renderIds  = "rank >= ". ($from-1);
        $renderIds .= " AND sourceId = '".$bizrelation->sourceId."' AND sourceClass = '".$bizrelation->sourceClass."' AND  kind = '".$bizrelation->kind."'";
    }
    else
        $message = "BizRelation delete has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "refresh":
    $renderIds = "rank > 0";
    if($bizrelation->sourceId != 0 and $bizrelation->sourceClass != null)
        $renderIds .= " AND sourceId = '".$bizrelation->sourceId."' AND sourceClass = '".$bizrelation->sourceClass."' AND  kind = '".$bizrelation->kind."'";
    break;

case "create":
case "edit":
    // Render a specific link in "edit" or "view" mode
    // See below...
    $xslMode = $command;
    break;
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

// Render items ?
if ($renderIds != null)
{
    // If there isnt any bizrelation of kind : bizrelation::IS_EMPTY
    if(!$bizrelation->exist($bizrelation->validityDate, bizrelation::IS_EMPTY))
    {
        $clauseWhere = $renderIds;

        // Add the validityDate to the sql Where clause
        if($bizrelation->validityDate != null and ($command != "create" and $command != "edit"))
            $clauseWhere .= " AND validityDate = '".$bizrelation->validityDate."'";

        $bizrelation->beginEnum($clauseWhere, "rank");

        // This variable will hold the value of the closest date with bizrelation (if the current date doesnt have any)
        $nearestDate = null;

        // If there is no result returned for this specified day, then look for the closest date with bizrelation
        if ($bizrelation->enumCount() == 0)
        {
            $bizrelation->endEnum();
            $clauseWhere = $renderIds;

            // Check if nearestDate wasnt already computed
            if($nearestDate == null)
                $nearestDate = $bizrelation->getNearestDate();

            if($bizrelation->validityDate != null and ($command != "create" and $command != "edit"))
                $clauseWhere .= " AND validityDate = '".$nearestDate."'";

            $bizrelation->beginEnum($clauseWhere, "rank");
        }

        // Set the appropriate date for the getLastPosition method
        if($nearestDate != null)
        {
            $tempDate = $bizrelation->validityDate;
            $bizrelation->validityDate = $nearestDate;
        }

        // Retrieve last position
        $lastPosition = $bizrelation->getLastPosition();

        // Restore back the original date
        if($nearestDate != null)
            $bizrelation->validityDate = $tempDate;

        // Display in the appropriate div a message saying what date is showed up
        if($nearestDate != null)
        {
            echo "<response type='item' id='dateMessage'><![CDATA[";
            echo _BIZ_CHANNEL_MANAGE_DATE.$bizrelation->getNearestDate();
            echo "]]></response>\n";
        }

        echo "<response type='item' id='3333'><![CDATA[";
        echo $bizrelation->validityDate;
        echo "]]></response>\n";

        // Display in the appropriate div a message saying what date is showed up
        if($wasManaged == true OR $nearestDate == null)
        {
            echo "<response type='item' id='dateMessage'><![CDATA[";
            echo _BIZ_CHANNEL_MANAGE_CONTENT_OF.$bizrelation->validityDate;
                echo "]]></response>\n";
        }
    
            // Enumerate content
            while ($bizrelation->nextEnum())
            {
                echo "<response type='item' id='".$divName.$bizrelation->rank."'><![CDATA[";
                renderRow($bizrelation, $lastPosition, $xslMode, $divName, $locked, $dateDisplayed);
                echo "]]></response>\n";
            }
            
            // Display empty div
            $maxRank = $lastPosition + 1;
            while(($maxRank + 1) <= 50)
            {
                echo "<response type='item' id='".$divName.$maxRank."'><![CDATA[";
                echo "]]></response>\n";
                $maxRank++;
            }
            $bizrelation->endEnum();
    }

    // If there is a bizrelation of kind : bizrelation::IS_EMPTY
    else
    {
        // Update the message div with appropriate message
        echo "<response type='item' id='dateMessage'><![CDATA[";
        echo _BIZ_CHANNEL_EMPTY_CONTENT;
        echo "]]></response>\n";

        // Display empty div
        $cpt = 1;
        while($cpt <= 50)
        {
            echo "<response type='item' id='".$divName.$cpt."'><![CDATA[";
            echo "]]></response>\n";
            ++$cpt;
        }
    }
}
elseif ($xslMode == "create")
{
    // Render the HTML div after user select an item in the search results table
    // The div is displayed on the top of the page of the module : mod_search_global
    echo "<response type='item' id='".$divName.$from."'><![CDATA[";
    renderRow($bizrelation, 0, $xslMode, $divName, $locked, $dateDisplayed);
    echo "]]></response>\n";
}
elseif ($xslMode == "edit")
{
    // Render in "edit" mode
    echo "<response type='item' id='".$divName.$from."'><![CDATA[";
    renderRow($bizrelation, 0, $xslMode, $divName, $locked, $dateDisplayed);
    echo "]]></response>\n";
}

// Render null item (last position is cleared)
if ($xslMode != "create" and $xslMode != "edit")
    echo "<response type='item' id='".$divName.($bizrelation->getLastPosition() + 1)."'/>\n";

// Return message ?
if ($messageId != null)
{
    echo "<response type='item' id='".$messageId."'>";
    echo ($message) ? "<![CDATA[ ".$message." ]]>" : "";
    echo "</response>\n";
}
echo "</ajax-response>";

/**
 * Render link
 *
 * @param array  $values        Aarray corresponding to lien enum
 * @param int    $lastPosition  Las used position
 * @param string $mode            Xsl mode parameter
 *
 */
function renderRow($bizrelation, $lastPosition, $mode = "list", $divName, $locked, $dateDisplayed)
{
    $position          = $bizrelation->rank;
    $sourceClass       = $bizrelation->sourceClass;
    $sourceId          = $bizrelation->sourceId;
    $kind              = $bizrelation->kind;
    $destinationClass  = $bizrelation->destinationClass;
    $destinationId     = $bizrelation->destinationId;
    $title             = $bizrelation->title;
    $header            = $bizrelation->header;
    $validityDate      = $bizrelation->validityDate;

    if($dateDisplayed == null)
        $dateDisplayed = $bizrelation->validityDate;

    $moveUp = "&nbsp;";
    $moveDn = "&nbsp;";
    $edit   = "&nbsp;";
    $delete = "&nbsp;";
    $properties = null;

    if ($destinationId)
    {
        // Retrieve destination object
        $project = $bizrelation->getProject();
        $bizobj  = new $destinationClass($project, $destinationId);

        // Update the header and the label
        if($title == "")
        {
            if (isset($bizobj->title))
            {
                $title = $bizobj->title;
            }
            else
            {
                // Display the appropriate param if object doesn't have a title
                switch($destinationClass)
                {
                case "channel":
                    $title = $bizobj->title;
                    break;
                case "contribution":
                    $title = $bizobj->nickname;
                    break;
                }
            }
        }

        if($header == "")
            $header = ucfirst($destinationClass);

        $html = null;

        if ($mode == "list")
            $html .= "<div style=\"cursor:pointer\" onClick=\"onSelectItem('".$destinationClass."',".$destinationId.")\">";

        $html .= "<table cellspacing='0' cellpadding='2' border='0'>";
        $html .= "<tr><td><img src='img/icons/".$destinationClass.".gif' alt='' border='0' hspace='2'></td>";
        $html .= "<td> (".$bizobj->id.') '. $title ."</td></tr>";
        $html .= "<tr><td></td><td> <strong>". $header ."</strong> ". $title ." &nbsp;</td></tr>";
        $html .= "</table>";

        if ($mode == "list")
            $html .= "</div>";
    }
    else
    {
        if ($mode == 'create')
        {
            $html = "&nbsp;";
            $locked = true;
        }

        // Build the HTML for the 1st div in case of an update
        elseif ($mode == 'edit')
        {
            $position = $bizrelation->position;

            $html = "<table cellspacing='0' cellpadding='2' border='0'>";
            $html .= "<tr><td><img src='img/icons/" . $destinationClass . ".gif' alt='' border='0' hspace='2'></td>";
            $html .= "<td> (" . $sourceId . ') '. $title ."</td></tr>";
            $html .= "<tr><td></td><td> <b>". $header ."</b> ". $title ." &nbsp;</td></tr>";
            $html .= "</table>";
        }
    }

    switch($mode)
    {
    case "list":
        $moveUp = "";
        $moveDn = "";
        $edit = "";
        $delete = "";

        if($locked  == "false")
        {
            if ($position > 1)
            {
                $moveUp  = "<img src='img/arrow_up.gif' alt='Monter' style='cursor:pointer'";
                $moveUp .= "onClick=\"ajaxUpdateChannelContent('move', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position-1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '".addslashes($title)."', '".$divName."', '".$locked."', '".$dateDisplayed."')\">";
            }
            if ($position < $lastPosition)
            {
                $moveDn  = "<img src='img/arrow_down.gif' alt='Descendre' style='cursor:pointer'";
                $moveDn .= "onClick=\"ajaxUpdateChannelContent('move', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position+1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '".addslashes($title)."', '".$divName."', '".$locked."', '".$dateDisplayed."')\">";
            }
            $edit  = "<img src='img/edit.gif' alt='Modifier' style='cursor:pointer'";
            $edit .= "onClick=\"openDialog('popup.php', 'module=select_bizobject&classSearch=".$destinationClass."&typeSource=".$sourceClass."&idSource=".$sourceId."&position=".$position."&mode=update&typeDestination=".$destinationClass."&idDestination=".$destinationId."&titre=".$title."&label=".$header."&typeRelation=".$kind."&manageChannel=true&validityDate=".$dateDisplayed."&locked=".$locked."',1030,570,null,null,'liens')\">";

            $delete  = "<img src='img/delete.gif' alt='Supprimer' style='cursor:pointer'";
            $delete .= "onClick=\"ajaxUpdateChannelContent('remove', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position-1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '".addslashes($title)."', '".$divName."', '".$locked."', '".$dateDisplayed."')\">";
        }
        break;

    case "create":
    case "edit":
        $properties  = "<input type='hidden' id='position' value='".textH8($position)."'/>";
        $properties .= "<input type='hidden' id='idDestination'   value='".textH8($destinationId)."'/>";
        $properties .= "<input type='hidden' id='typeDestination' value='".textH8($destinationClass)."'/>";
        $properties .= "<table cellspacing='1' cellpadding='2' border='0'> <tr>";
        $properties .= "<td align='right'> "._BIZ_LABEL." : </td>";
        $properties .= "<td> <input type='text' size='20' value=\"". textH8($header) ."\" id='label'/> </td>";
        $properties .= "<td align='right'> &nbsp; "._TITLE." : </td>";
        $properties .= "<td> <input type='text' size='30' value=\"". textH8($title) ."\" id='titre'/> </td>";
        $properties .= "</tr> </table>";
        break;
    }

    ?>
    <table width="100%" height="50" bgcolor="#c0c0c0" cellspacing="1" cellpadding="0">
        <tr bgcolor="#f4f4f4" height="25">
            <td width="30" rowspan="2" align="center" class="position"> <?php echo $position;?></td>
            <td width="20" align="center"> <?php echo $moveUp; ?> </td>
            <td width="*" rowspan="2"><?php echo $html ?></td>
            <td width="20" align="center"> <?php echo $edit; ?> </td>
        </tr>
        <tr bgcolor="#f4f4f4" height="25">
            <td width="20" align="center"> <?php echo $moveDn; ?> </td>
            <td align="center"> <?php echo $delete; ?> </td>
        </tr>
    </table>
    <?php echo $properties; ?>
    <?php
}

?>
