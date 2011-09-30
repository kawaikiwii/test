<?php
/****************************************************
 * WARNING : DUPLICATED FILE CONTENT IN wcm.lock.php
 **************************************************** 
 * 
 * Project:     WCM
 * File:        biz.lock.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve parameters
$messageId          = getArrayParameter($_REQUEST, "messageId", null);
$className          = getArrayParameter($_REQUEST, "className", null);
$objectId           = getArrayParameter($_REQUEST, "objectId", 0);
$command            = getArrayParameter($_REQUEST, "command", null);
$userId             = getArrayParameter($_REQUEST, "userId", 0);
$editingDate        = getArrayParameter($_REQUEST, "editingDate", null);

$message = "";

if ($objectId)
{
    $bizObject = new $className(null, $objectId);
}

switch($command)
{
    case "unlock":
        if ($bizObject->unlock())
            $message = _BIZ_OBJECT_UNLOCKED;
        else
            $message = _BIZ_ERROR_ON_UNLOCK.$bizObject->lastErrorMsg;
        break;
        
    case "lock":
        if ($bizObject->lock($userId))
            $message = _BIZ_OBJECT_LOCKED;
        else
            $message = _BIZ_ERROR_ON_LOCK.$bizObject->lastErrorMsg;
        break;
        
    case "isObsolete":
        if($bizObject->id)
        {
            // Check if object has been locked by someone else
            if ($bizObject->isLocked())
            {
                echo "locked";
            }
            else
            {
                echo ($editingDate < $bizObject->modifiedAt) ? "true" : "false";
            }
        }
        else 
        {
            echo "true";
        }
        break;
        
        case "verifyMyLock":
            //Check if object is still locked by current user
            $lockInfo = $bizObject->getLockInfo();
            echo ($lockInfo->userId == $userId) ? "true" : "false";
            break;
}

if (($command!="isObsolete")&&($command!="verifyMyLock"))
{
    
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
    
    // Return message ?
    if ($messageId != null)
    {
        echo "<response type='item' id='".$messageId."'>";
        echo ($message) ? "<![CDATA[ ".$message." ]]>" : "";
        echo "</response>\n";
    }
    echo "</ajax-response>";
}   
    
?>
