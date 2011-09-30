<?php
/**
 * Project:     WCM
 * File:        biz.manageBin.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve current session
$session = wcmSession::getInstance();

$action          = getArrayParameter($_REQUEST, "command", null);
$name            = getArrayParameter($_REQUEST, "name", null);
$description      = getArrayParameter($_REQUEST, "description", null);
$object          = getArrayParameter($_REQUEST, "object", null);
$id              = getArrayParameter($_REQUEST, "id", null);
$divId           = getArrayParameter($_REQUEST, "divId", null);
$dashboard       = getArrayParameter($_REQUEST, "dashboard", null);

$md = new binSearchControl();
// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header('Content-Type: text/xml;charset=UTF-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo '<response type="item" id="'.$divId.'"><![CDATA[';
switch ($action)
{
    case "createBinFromSession":
        $content = '';
        if (isset($_SESSION['tempBin']))
        {
            foreach ($_SESSION['tempBin'] as $key => $value)
            {
                if ($value)
                    $content .= "/".$key;
            }
            unset($_SESSION['tempBin']);
        }
        $md->saveBin($name, $description, $content);
        echo $md->initialLoad();
        break;
    case "createEmpty":
    case "updateBin":
        $md->saveBin($name, $description, '', $dashboard, $id);
        echo $md->initialLoad($name);
        break;
    case "remove":
        $md->removeBin($id);
        echo $md->initialLoad();
        break;
    case "clear":
        $md->clearBins();
        echo $md->initialLoad();
        break;
    case "display":
    	echo "<SCRIPT language=\"Javascript\">document.getElementById('search_string').value='".$md->renderBinDataForSearch($id)."';document.globalSearch.submit();</SCRIPT>";
    	echo $md->initialLoad($name);
        break;
    case "addSessionToSelectedBin":
        // Add into the selected bin the selected items
        if (isset($_SESSION['tempBin']) && count($_SESSION['tempBin']) > 0)
        {
            foreach ($_SESSION['tempBin'] as $key => $value)
            {
                $md->addToSelectedBin($id, $key);
            }
            unset($_SESSION['tempBin']);
        }
        echo $md->renderBinData($id);
        break;
    case "addToSelectedBin":
        if ($object)
            $md->addToSelectedBin($id, $object);
        echo $md->renderBinData($id);
        break;
    // Add into the session a temporary table of the selected itmes
    case "addToSessionBin":
        if (!isset($_SESSION['tempBin']))
        {
            $_SESSION['tempBin'] = array();
            $_SESSION['tempBin'][$object] = 1;
        }
        else
            $_SESSION['tempBin'][$object] = 1;
        break;
    case "massAddToSessionBin":
        
        $objects = json_decode($object);
        
        foreach ($objects as $key => $value) $_SESSION['tempBin'][$value] = 1;
        break;
    case "massRemoveFromSessionBin":
        $objects = json_decode($object);
        foreach ($objects as $key => $value) unset($_SESSION['tempBin'][$value]);
        break;
    //Remove from the session's temporary table the items that have been unselected
    case "removeFromSessionBin":
        if (isset($_SESSION['tempBin'][$object]))
        {
            unset($_SESSION['tempBin'][$object]);
        }
        break;
    case "renderBinData":
        if (!is_null($id))
        {
            $md->renderBinData($id);
        }
        break;   
    //Deleted a selected item from the bin
    case "removeFromSelectedBin":
        $md->removeFromSelectedBin($id, $object);
        echo $md->renderBinData($id);
        break;
}
echo ']]></response>';
echo '<response type="item" id="compteur"><![CDATA[';
echo "<span id='compteur'><h4>"._BIZ_SELECTED_ITEMS." (<span>";
if (isset($_SESSION['tempBin']))
    echo count($_SESSION['tempBin']);
else
    echo "0";
echo "</span>)</h4></span>";
echo ']]></response>';
echo "</ajax-response>";
?>