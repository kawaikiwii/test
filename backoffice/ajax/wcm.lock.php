<?php
/**
 * Project:     WCM
 * File:        wcm.lock.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// Retrieve parameters
$itemId = getArrayParameter($_REQUEST, 'itemId', null);
$objectClass = getArrayParameter($_REQUEST, 'objectClass', null);
$objectId    = getArrayParameter($_REQUEST, 'objectId', 0);
$command     = getArrayParameter($_REQUEST, 'command', null);
$messageKind = 'info';
$message     = null;

if ($objectClass)
{
    wcmLock::deleteByClass($objectClass, $objectId);
    $message = _BIZ_OBJECT_UNLOCKED;
}
else
{
    // retrieve object to lock/unlock from parameters or MVC context?
    if ($objectClass)
    {
        $context = new $objectClass;
        $context->refresh($objectId);
    }
    else
    {
        $context = wcmMVC_Action::getContext();
    }
    
    // lock/unlock the object
    if ($context)
    {
        switch($command)
        {
            case 'lock':
                if ($context->lock())
                {
                    $message = _BIZ_OBJECT_LOCKED;
                }
                else
                {
                    $messageKind = 'error';
                    $info = $context->getLockInfo();
                    if ($info->userId != wcmSession::getInstance()->userId)
                    {
                        $message = _BIZ_OBJECT_LOCKED;
                    }
                    else
                    {
                        $message = _UNEXPECTED_ERROR . ':' . $context->getErrorMsg();
                    }
                }
                break;

            case 'unlock':
                if ($context->unlock())
                {
                    $message = _BIZ_OBJECT_UNLOCKED;
                }
                else
                {
                    $messageKind = 'error';
                    $info = $context->getLockInfo();
                    if ($info->userId != wcmSession::getInstance()->userId)
                    {
                        $message = _BIZ_OBJECT_LOCKED;
                    }
                    else
                    {
                        $message = _UNEXPECTED_ERROR . ':' . $context->getErrorMsg();
                    }
                }
                break;
        }
    }
}

// Xml output
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';

// Write ajax response
echo '<ajax-response>';
if ($itemId)
{
    echo '<response type="item" id="'.$itemId.'">';
    echo _BIZ_DONE;
    echo '</response>';
}
if ($message)
{
    echo '<response type="item" id="messagebox">';
    echo '<div id="sysmessage" class="' . $messageKind . '">' . $message . '</div>';
    echo '</response>';
}
echo '</ajax-response>';