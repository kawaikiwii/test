<?php
/**
 * Project:     WCM
 * File:        ajax/bizlogic/executeAction.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * Validate if an operation can be performed on current context
 * and return 'locked', 'deleted', 'obsolete' or null on success
 *
 * Note that lock/unlock/undocheckout are validated AND executed
 */
echo validateAction(getArrayParameter($_REQUEST, 'action'));

/**
 * Validate a specific action on current context
 *
 * @param string $action Action to validate
 * @return string status ('ok', 'deleted', 'locked', 'obsolete', 'unknown error'
 */
function validateAction($action)
{
    $context = wcmMVC_Action::getContext();
    switch($action)
    {
        case 'undoCheckout':
        case 'unlock':
            if (!$context->unlock())
                return getResultMessage();
            break;

        case 'lock':
            if ($context instanceof wcmSysObject && $context->isObsolete())
               return ($context->id == 0) ? 'deleted' : 'obsolete';

            if (!$context->lock())
                return getResultMessage();
            break;

        case 'save':
        case 'checkin':
        case 'checkout':
        case 'delete':
        case 'transition':
            return getResultMessage();

        case 'publish':
            // Even a locked object can be publish
            return getResultMessage(true);

        default:
            return _INVALID_ACTION . ': ' . $action;
    }

    // So far, so good!
    return 'ok';
}

/**
 * Display the result of the validation
 *
 * @param boolean $ignoreLocked TRUE to ignore 'locked' state
 *
 * @return string null on success or 'deleted','obsolete' or 'locked'
 */
function getResultMessage($ignoreLocked = false)
{
    // First, check obsolete state
    $context = wcmMVC_Action::getContext();
    if ($context instanceof wcmSysObject && $context->isObsolete())
    {
       return ($context->id == 0) ? 'deleted' : 'obsolete';
    }

    if (!$ignoreLocked)
    {
        $lockInfo = $context->getLockInfo();
        if ($lockInfo->userId != 0 && $lockInfo->userId != wcmSession::getInstance()->userId)
        {
            return 'locked';
        }
    }

    // So far, so good!
    return 'ok';
}
