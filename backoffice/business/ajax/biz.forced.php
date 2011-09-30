<?php

/**
 * Project:     WCM
 * File:        biz.forced.php
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

$kind               = getArrayParameter($_REQUEST, "kind", null);
$header             = urldecode(getArrayParameter($_REQUEST, "header", null));
$title              = urldecode(getArrayParameter($_REQUEST, "title", null));
$retrolien          = getArrayParameter($_REQUEST, "retrolien", null);
$locked             = getArrayParameter($_REQUEST, "locked", false);
$callback           = getArrayParameter($_REQUEST, "callback", null);

if ($destinationClass && $destinationId)
{
    $bizobject = new $destinationClass($project);
    $bizobject->refresh($destinationId);

    $header = ($header == '') ? $bizobject->getClass() : $header;
    $title  = ($title  == '') ? $bizobject->title      : $title ;
}

// Name of the div
$divName = getArrayParameter($_REQUEST, "divName", "forced_");

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
    if (!$bizrelation->move($from, $to))
        $message = "BizRelation move has failed : " . $bizrelation->_lastErrorMsg;
    break;
case "update":
    if (!$bizrelation->update($destinationClass, $destinationId, $kind, urldecode($header), urldecode($title), $from))
        $message = "BizRelation update has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "insert":
    if (!$bizrelation->insert($destinationClass, $destinationId, $kind, $header, $title))
        $message = "BizRelation insert has failed : " . $bizrelation->_lastErrorMsg;
    break;

case "remove":
    if (!$bizrelation->remove($from,false))
        $message = "BizRelation delete has failed : " . $bizrelation->_lastErrorMsg;
    break;

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

if ($xslMode == "list")
{
    // Render items
    $whereClause = "sourceId = '".$sourceId."' AND sourceClass = '".$sourceClass."' AND  kind = '".$kind."'";

    // Retrieve last position (if needed)
    $lastPosition = null;
    if ($kind)
        $lastPosition = $bizrelation->getLastPosition();
    
    $forcedRanks = array();
    
    if ($bizrelation->beginEnum($whereClause, "rank"))
    {
        // Enumerate content
        while ($bizrelation->nextEnum())
        {
            echo "<response type='item' id='".$divName.$bizrelation->rank."'><![CDATA[";
            renderRow($project, $bizrelation, $lastPosition, $xslMode, $divName, $locked, $callback);
            echo "]]></response>\n";
            $forcedRanks[] = $bizrelation->rank;
        }
        $bizrelation->endEnum();
        for ($i=1;$i<50;$i++)
        {
            if (!in_array($i,$forcedRanks))
            {
                echo "<response type='item' id='".$divName.$i."'><![CDATA[";
                ECHO "<HR width='150' size='2' color='#CCCCCC'>";
                /*?>
                    <table width="100%" height="10" bgcolor="#c0c0c0" cellspacing="1" cellpadding="0">
                        <tr bgcolor="#f4f4f4" height="10">
                            <td> <?php echo $i;?></td>
                        </tr>
                    </table>
                <?php
*/              
                echo "]]></response>\n";
            }
        }
    }
    else
    {
        $message .= "<br/>BizRelation enum has failed : ".$bizrelation->_lastErrorMsg;
    }
}
elseif ($xslMode == 'edit')
{
    // Render in "edit" mode
    echo "<response type='item' id='" . $divName . $from . "'><![CDATA[";
    renderRow($project, $bizrelation, 0, $xslMode, $divName, $locked, $callback);
    echo "]]></response>\n";
}

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
 *
 */
function renderRow($project, $bizrelation, $lastPosition, $mode = "list", $divName, $locked, $callback = null)
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

        $html .= $header_html;
        $html .= renderBizobject($bizobj, 'render_relation', $renderParameters);

        if ($mode == "list")
            $html .= "</div>";
    }
    else
    {
        // Build the HTML for the 1st div in case of an update
        if ($mode == 'edit')
        {
            $position = $bizrelation->position;

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
                $moveUp .= "onClick=\"ajaxForced('move', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position-1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '" . $title ."', '".$divName."', '".$locked."')\">";
            }
            //if ($position < $lastPosition)
            //{
                $moveDn  = "<img src='img/arrow_down.gif' alt='Descendre' style='cursor:pointer'";
                $moveDn .= "onClick=\"ajaxForced('move', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position+1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '".$title."', '".$divName."', '".$locked."')\">";
            //}
            $edit  = "<img src='img/edit.gif' alt='" . _BIZ_UPDATE . "' style='cursor:pointer'";
            $edit .= "onClick=\"ajaxForced('edit', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position+1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '".$title."', '".$divName."', '".$locked."')\">";

            $delete  = "<img src='img/delete.gif' alt='" . _BIZ_DELETE . "' style='cursor:pointer'";
            $delete .= "onClick=\"ajaxForced('remove', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position-1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '".$title."', '".$divName."', '".$locked."')\">";
        }
        break;

    case "edit":
        $properties .= "<tr bgcolor='#f4f4f4' height='25'".$expired.">";
        $properties .= "<td align='right' colspan='3' rowspan='3'>";
        $properties .= _BIZ_LABEL . " : ";
        $properties .= "<input type='text' size='20' value=\"". textH8($header) ."\" id='editLabel_".$position."'/> <br/>";
        $properties .= _BIZ_TITLE . " : ";
        $properties .= "<input type='text' size='30' value=\"". textH8($title) ."\" id='editTitle_".$position."'/> <br/>";
        $properties .= "</td>";
        $properties .= "<td width='20' align='center'>";
        $properties .= "<img src='img/refresh.gif' style='cursor:pointer'";
        $properties .= "onClick=\"ajaxForced('update', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position+1).", '".$destinationClass."', ".$destinationId.", '".$kind."', $('editLabel_".$position."').value, $('editTitle_".$position."').value, '".$divName."', '".$locked."')\">";
        $properties .= "</td>";
        $properties .= "</tr>";
        $properties .= "<tr bgcolor='#f4f4f4' height='25'".$expired.">";
        $properties .= "<td width='20' align='center'>";
        $properties .= "<img src='img/actions/cancel.gif' style='cursor:pointer'";
        $properties .= "onClick=\"ajaxForced('refresh', '".$sourceClass."', ".$sourceId.", ".$position.", ".($position+1).", '".$destinationClass."', ".$destinationId.", '".$kind."', '".$header."', '".$title."', '".$divName."', '".$locked."')\">";
        $properties .= "</td>";
        $properties .= "</tr>";
        break;
    }

    ?>
    <table width="100%" height="50" bgcolor="#c0c0c0" cellspacing="1" cellpadding="0">
        <tr bgcolor="#f4f4f4" height="25"<?php echo $expired; ?>>
            <td width="30" rowspan="2" align="center" class="position"> <?php echo $position;?></td>
            <td width="20" align="center"> <?php echo $moveUp; ?> </td>
            <td width="*" rowspan="2"><?php echo $html ?></td>
            <td width="20" align="center"> <?php echo $edit; ?> </td>
        </tr>
        <tr bgcolor="#f4f4f4" height="25"<?php echo $expired; ?>>
            <td width="20" align="center"> <?php echo $moveDn; ?> </td>
            <td align="center"> <?php echo $delete; ?> </td>
        </tr>
        <?php
        if ($properties) 
        {
            echo $properties;
        }
        ?>
    </table>
    <?php
}

?>
