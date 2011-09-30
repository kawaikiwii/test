<?php

/**
 * Project:     WCM
 * File:        biz.relation.php
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

// Retrieve parameters
$messageId          = getArrayParameter($_REQUEST, "messageId", null);
$sourceClass        = getArrayParameter($_REQUEST, "sourceClass", null);
$sourceId           = getArrayParameter($_REQUEST, "sourceId", 0);
$command            = getArrayParameter($_REQUEST, "command", null);

$from               = getArrayParameter($_REQUEST, "from", 0);
$to                 = getArrayParameter($_REQUEST, "to", 0);
$destinationClass   = getArrayParameter($_REQUEST, "destinationClass", null);
$destinationId      = getArrayParameter($_REQUEST, "destinationId", null);


$kind      = getArrayParameter($_REQUEST, "kind", null);
$header    = urldecode(getArrayParameter($_REQUEST, "header", null));
$title     = urldecode(getArrayParameter($_REQUEST, "title", null));
$locked    = getArrayParameter($_REQUEST, "locked", false);
$callback  = getArrayParameter($_REQUEST, "callback", null);
$link_article = getArrayParameter($_REQUEST, "link_article", null);

// TODO : this solution is somewhat hackish. Make better
if ($destinationClass && $destinationId)
{
    $bizobject = new $destinationClass($project);
    $bizobject->refresh($destinationId);

    $header = ($header == '') ? $bizobject->getClass() : $header;
    if ($header == 'channel') $header = getConst('_BIZ_CHANNEL');
    else if ($header == 'contribution') $header = getConst('_BIZ_CONTRIBUTION');
    $title  = ($title  == '') ? $bizobject->title      : $title ;
}

// Name of the div
$divName = getArrayParameter($_REQUEST, "divName", "links_");

// force delete in case of referents (Quick and dirty : needs to be removed and optimized)
if ($command == 'remove' && $divName == 'refered_')
    $forceDelete = true;

// Creating a new bizrelation
$bizrelation                    = new bizrelation($project);
$bizrelation->sourceClass       = $sourceClass;
$bizrelation->sourceId          = $sourceId;
$bizrelation->destinationClass  = $destinationClass;
$bizrelation->destinationId     = $destinationId;
$bizrelation->header            = $header;
$bizrelation->title             = $title;
$bizrelation->kind              = $kind;
$bizrelation->rank              = $from;

switch($command)
{
case "move":
    if ($bizrelation->move($from, $to))
    {
        $renderIds = "rank > 0";
        if ($sourceId != 0 and $sourceClass != null)
            $renderIds .= " AND (sourceId = '".$sourceId."' AND sourceClass = '".$sourceClass."' AND  kind = '".$kind."')";
    }
    else
        $message = "BizRelation move has failed : " . $bizrelation->_lastErrorMsg;
    break;

/** Il ne fait pas l'update correctement ici : */
case "update":
case "updateRef":
    if ($bizrelation->update($destinationClass, $destinationId, $kind, urldecode($header), urldecode($title), $from))
    {
        // Refresh all items as a similar destination  object may already exists
        $renderIds = "rank > 0";
        $renderIds .= " AND sourceId = '".$sourceId."' AND sourceClass = '".$sourceClass."' AND  kind = '".$kind."'";
    }
    else
        $message = "BizRelation update has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "insert":
    if ($bizrelation->insert($destinationClass, $destinationId, $kind, $header, $title))
    {
        // Refresh all items as a similar destination  object may already exists
        $renderIds = "rank > 0";
        $renderIds .= " AND sourceId = '".$sourceId."' AND sourceClass = '".$sourceClass."' AND  kind = '".$kind."'";
    }
    else
        $message = "BizRelation insert has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "remove":
    if ($bizrelation->remove($from))
    {
        $renderIds  = "rank >= ". ($from-1);
        $renderIds .= " AND sourceId = '".$sourceId."' AND sourceClass = '".$sourceClass."' AND  kind = '".$kind."'";
    }
    else
        $message = "BizRelation delete has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "refresh":
    $renderIds = "rank > 0";
    if ($sourceId != 0 and $sourceClass != null)
    {
        $renderIds .= " AND sourceId = '".$sourceId."' AND sourceClass = '".$sourceClass."' AND  kind = '".$kind."'";
    }
    break;

case "referent":
    $renderIds = "rank > 0";
    if ($destinationId != 0 and $destinationClass != null)
        $renderIds .= " AND destinationId = '".$destinationId."' AND destinationClass = '".$destinationClass."'";
    $xslMode = 'referent';
    break;

case "import":
    if ($bizrelation->import($destinationClass, $destinationId))
    {
        // Refresh all items as a similar destination  object may already exists
        $renderIds = "rank > 0";
    }
    else
    {
        $message = "BizRelation import has failed : " . $bizrelation->_lastErrorMsg;
    }
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
echo '<?xml version="1.0" encoding="UTF-8"?>';

// Write ajax response
echo "<ajax-response>\n";

// Render items ?
if ($renderIds != null)
{
    // Retrieve last position (if needed)
    $lastPosition = null;
    if ($kind)
        $lastPosition = $bizrelation->getLastPosition();

    $counter = 1;
    if ($bizrelation->beginEnum($renderIds, "rank"))
    {
        while ($bizrelation->nextEnum())
        {
            //patch for referer since its rank can be the same for multiple relations
            $div_id = $bizrelation->rank;
            if($divName == "refered_"){
                $div_id = $counter;
                $counter++;
            }
        
            echo "<response type='item' id='".$divName.$div_id."'><![CDATA[";
            renderRow($project, $bizrelation, $lastPosition, $xslMode, $divName, $locked, $callback, $link_article);
            echo "]]></response>\n";
        }
        $bizrelation->endEnum();
    }
    else
    {
        $message .= "<br/>BizRelation enum has failed : ".$bizrelation->_lastErrorMsg;
    }
}
elseif ($xslMode == "create")
{
    // Render the HTML div after user select an item in the search results table
    // The div is displayed on the top of the page of the module : mod_search_global

    echo "<response type='item' id='".$divName.$from."'><![CDATA[";
    renderRow($project, $bizrelation, 0, $xslMode, $divName, $locked, $callback);
    echo "]]></response>\n";
}
elseif ($xslMode == 'edit')
{
    // Render in "edit" mode
    echo "<response type='item' id='" . $divName . $from . "'><![CDATA[";
    renderRow($project, $bizrelation, 0, $xslMode, $divName, $locked, $callback);
    echo "]]></response>\n";
}

// Render null item (last position is cleared)
if ((isset($forceDelete)) || ($xslMode == 'list' && $divName != 'refered_') && $xslMode != 'create' && $xslMode != 'referent' && $xslMode != 'updateRef' && $xslMode != 'edit' )
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
 * @param string $mode          Xsl mode parameter
 * @param $link_article -patch- the last parameter is sent to disable the buttons (answer/delete) of the comments (contribution.xsl)
 */
function renderRow($project, $bizrelation, $lastPosition, $mode = "list", $divName, $locked, $callback = null, $link_article)
{
    $position           = $bizrelation->rank;
    $sourceClass        = $bizrelation->sourceClass;
    $sourceId           = $bizrelation->sourceId;
    $kind               = $bizrelation->kind;
    $destinationClass   = $bizrelation->destinationClass;
    $destinationId      = $bizrelation->destinationId;
    $title              = $bizrelation->title;
    $header             = $bizrelation->header;
    $validityDate       = $bizrelation->validityDate;
    $expirationDate     = $bizrelation->expirationDate;

    $moveUp = "&nbsp;";
    $moveDn = "&nbsp;";
    $edit   = "&nbsp;";
    $delete = "&nbsp;";
    $properties = null;

    // check if relation is expired
    $expired = '';
    $today = date("Y-m-d");
    if (($expirationDate != '') && ($expirationDate < $today))
        $expired = ' style="background-color:#f39897;"';

    $renderParameters = array();
    $renderParameters["mode"]     = 'list';
    $renderParameters["callback"] = $callback;
    $renderParameters["link_article"] = $link_article;
    

    $header_html = '';

    if ($header || $title)
    {
        $header_html = "<table cellspacing='0' cellpadding='2' border='0'>";
        $header_html .= "<tr><td>&nbsp;&nbsp;</td><td></td><td> <strong>". $header ."</strong> ". $title ." &nbsp;</td></tr>";
        $header_html .= "</table>";
    }

    if ($destinationId)
    {
        // Retrieve destination object
        $bizobj = new $destinationClass($project, $destinationId);

        $html = '';

        if ($mode == "list")
            $html .= '<div style="cursor:pointer">';
        elseif (($mode == 'referent') || ($mode == 'updateRef'))
            $html .= '<div style="cursor:pointer">';

        $html .= $header_html;

        if (($mode == 'referent') || ($mode == 'updateRef') || ($mode == 'list' && $divName == 'refered_'))
        {
            $srcBizobj = new $sourceClass($project, $sourceId);
            $html .= renderBizobject($srcBizobj, 'render_relation', $renderParameters);
        }
        else
        {
            $html .= renderBizobject($bizobj, 'render_relation', $renderParameters);
        }

        if (($mode == "list") || ($mode == 'referent') || ($mode == 'updateRef'))
            $html .= "</div>";
    }
    else
    {
        if ($mode == 'create')
        {
            $html = "&nbsp;";
        }

        // Build the HTML for the 1st div in case of an update
        elseif ($mode == 'edit')
        {
            $position = $bizrelation->rank;

            $html .= $header_html;

            $srcBizobj = new $sourceClass($project, $sourceId);
            $html .= renderBizobject($srcBizobj, 'render_relation', $renderParameters);
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
            $title  = urlencode($title);
            $header = urlencode($header);
            if ($position > 1)
            {
                $moveUp .= "<img src='img/arrow_up.gif' alt='Monter' style='cursor:pointer'";
                $moveUp .= "onClick=\"ajaxRelation('move', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position-1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '" . $title ."', '".$divName."', '".$locked."','','1')\">";
            }
            if ($position < $lastPosition)
            {
                $moveDn  = "<img src='img/arrow_down.gif' alt='Descendre' style='cursor:pointer'";
                $moveDn .= "onClick=\"ajaxRelation('move', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position+1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '".$title."', '".$divName."', '".$locked."','','1')\">";
            }
            $dsClass = ($divName == 'links_') ? 'global' : $destinationClass;
            $edit  = "<img src='img/edit.gif' alt='" . _BIZ_UPDATE . "' style='cursor:pointer'";
            $edit .= "onClick=\"openDialog('popup.php', 'module=select_bizobject&classSearch=".$dsClass."&typeSource=".$sourceClass."&idSource=".$sourceId."&position=".$position."&mode=update&typeDestination=".$destinationClass."&idDestination=".$destinationId."&titre=".$title."&label=".$header."&typeRelation=".$kind."&locked=".$locked."&div=" . $divName ."',1030,570,null,null, '" . $divName ."')\">";

            $delete  = "<img src='img/delete.gif' alt='" . _BIZ_DELETE . "' style='cursor:pointer'";
            $delete .= "onClick=\"ajaxRelation('remove', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position-1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '".$title."', '".$divName."', '".$locked."','','1')\">";
        }
        break;

    case "create":
    case "edit":
        $properties  = "<input type='hidden' id='position' value='".textH8($position)."'/>";
        $properties .= "<input type='hidden' id='idDestination'   value='".textH8($destinationId)."'/>";
        $properties .= "<input type='hidden' id='typeDestination' value='".textH8($destinationClass)."'/>";
        $properties .= "<table cellspacing='1' cellpadding='2' border='0'> <tr>";
        $properties .= "<td align='right'>" . _BIZ_LABEL . " : </td>";
        $properties .= "<td> <input type='text' size='20' value=\"". textH8($header) ."\" id='label'/> </td>";
        $properties .= "<td align='right'>" . _BIZ_TITLE . " : </td>";
        $properties .= "<td> <input type='text' size='30' value=\"". textH8($title) ."\" id='titre'/> </td>";
        $properties .= "</tr> </table>";
        break;
    }

    ?>
    <table width="100%" height="50" bgcolor="#c0c0c0" cellspacing="1" cellpadding="0">
        <tr bgcolor="#f4f4f4" height="25"<?php echo $expired; ?>>
            <?php
            if ($divName != 'refered_')
            {
            ?>
            <td width="30" rowspan="2" align="center" class="position"> <?php echo $position;?></td>
            <td width="20" align="center"> <?php echo $moveUp; ?> </td>
            <?php
            }
            ?>
            <td width="*" rowspan="2"><?php echo $html ?></td>
            <?php
            if ($divName != 'refered_')
            {
            ?>
            <td width="20" align="center"> <?php echo $edit; ?> </td>
            <?php
            }
            ?>
        </tr>
        <tr bgcolor="#f4f4f4" height="25"<?php echo $expired; ?>>
            <?php
            if ($divName != 'refered_')
            {
            ?>
            <td width="20" align="center"> <?php echo $moveDn; ?> </td>
            <td align="center"> <?php echo $delete; ?> </td>
            <?php
            }
            ?>
        </tr>
    </table>
    <?php
    echo $properties;
}
?>