<?php

/**
 * Project:     WCM
 * File:        biz.manageItem.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by an Ajax call, It returns a DIV containing whatever
 * is needed to manage a item field in a form.
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Name of the div where the HTML content will be updated
$divName = getArrayParameter($_REQUEST, "divName", "");

// Define if the fields must be locked
$locked = getArrayParameter($_REQUEST, "locked", null);

// Source class of the bizrelation
$sourceClass = getArrayParameter($_REQUEST, "sourceClass", null);

$width = getArrayParameter($_REQUEST, "width", "260");
$height = getArrayParameter($_REQUEST, "height", "180");

// Source id of the bizrelation
$sourceId = getArrayParameter($_REQUEST, "sourceId", null);

// Destination id of the bizrelation
$destinationId = getArrayParameter($_REQUEST, "destinationId", null);

// Action to be executed : delete / display / update
$action = getArrayParameter($_REQUEST, "action", "display");

// Recover the bizitem to display it
if($action == "display")
{
    $bizrelation = new bizrelation($project);
    $where = "sourceClass = '".$sourceClass."' AND sourceId = '".$sourceId."' AND destinationClass = 'item' AND kind = '1'";

    if ($bizrelation->beginEnum($where, "rank"))
    {
        if($bizrelation->nextEnum())
            $bizitem = new item($project, $bizrelation->destinationId);
        $bizrelation->endEnum();
    }
}

// Initialize a bizitem
if (isset($bizitem) == false)
    $bizitem = new item($project);

$bizitem->refresh($destinationId);

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// XML output
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo "<response type=\"item\" id=\"".$divName."\"><![CDATA[";

echo renderHtmlItemForm($divName, $bizitem, $locked, $width, $height,$project);

echo "]]></response>";
echo "</ajax-response>";

/**
 * Returns a string containing the HTML code for the item Form
 *
 * @param   string      $divName    Name of the div
 * @param   bizitem $bizitem    The bizitem to display if there is any
 * @param   boolean     $locked     Define if the form is locked or not
 * @param   int         $width      Width of the div displaying the item
 * @param   int         $height     Height of the div displaying the item
 *
 * @return  string  $result     A string containing the concatenation of all value of the array
 */
function renderHtmlItemForm($divName, $bizitem = null, $locked = true, $width = "240", $height = "160", $project)
{
    $config = wcmConfig::getInstance();

    $result .= '<table width="100%" border="0" cellpadding="0" cellspacing="5">';
    $result .= '<tr>';
    $result .= '<td align="right" width="70">';
    $result .= '<input type="hidden" name="id_'.$divName.'" id="id_'.$divName.'" value="'.textH8($bizitem->id).'">';
    $result .= _BIZ_ITEM;
    $result .= '</td>';
    $result .= '<td>';
    $result .= '<table cellspacing="0" cellpadding="0" border="0">';
    $result .= '<tr>';
    $result .= '<td width="17">';

    if (!$locked)
        $result .= '<img class="button" src="'.$config['wcm.backOffice.url'].'img/delete.gif" alt="'._BIZ_DELETE_ITEM.'" onClick="clearItem(\''.$divName.'\')"/>';

    $result .= '</td>';
    $result .= '<td>';
    $result .= '<input type="text" style="width:240px" id="location_'.$divName.'" name="location_'.$divName.'" value="'.textH8($bizitem->location).'"';

    if ($locked)
        $result .= ' disabled="disabled"';

    $result .= '/></td>';
    $result .= '<td>';

    if (!$locked)
        $result .= '<img class="button" src="'.$config['wcm.backOffice.url'].'img/folder.gif" alt="'._BIZ_SELECT_ITEM.'" onclick="updateItem(\'\', \''.$divName.'\',$(\'itemType\').value)"/>';

    $result .= '</td>';
    $result .= '</tr>';
    $result .= '</table>';
    $result .= '</td>';
    $result .= '</tr>';
    $result .= '<tr>';
    $result .= '<td></td>';
    $result .= '<td>';

    $result .= '<div id="itemPreview" style="width:'.$width.'px; height:'.$height.'px; border:1px solid #c0c0c0; overflow:scroll';
    if ($bizitem->externalLink || !$bizitem->location || !@getimagesize($bizitem->location))
    {
        $result .= '; display:none';
    }
    $result .= '">';

    $result .= '<img id="locationImg_'.$divName.'" alt="" src="'.$bizitem->location.'" border="0"';
    if ($locked)
    {
        $result .= " disabled='disabled'";
    }
    $result .= '>';
    $result .= '</div>';
    $result .= '</td>';
    $result .= '</tr>';
    $result .= '</table>';

    return $result;
}
?>
