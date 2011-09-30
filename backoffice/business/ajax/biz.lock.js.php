function ajaxLock(command, className, objectId)
{
alert(command + ' ' +  className + ' ' +  objectId +  ' ' + userId + ' ' + editingDate);
    // Execute javascript callback before ajax call?
    var messageId = null;
    if (window.onAjaxLock)
    {
        messageId  = window.onAjaxLock(command, className, objectId);
        // Cancel call?
        if (!messageId) return;
    }

    switch (command)
    {
        case 'isObsolete':
            wcmBizAjaxController.callWithoutUpdate("biz.lock", {
                    messageId: messageId,
                    command: command,
                    className: className,
                    objectId: objectId,
                    userId: userId,
                    editingDate: editingDate
            }, null, {
                    onComplete: onComplete_isObsolete
            });
            break;

        case 'verifyMyLock':
            wcmBizAjaxController.callWithoutUpdate("biz.lock", {
                    messageId: messageId,
                    command: command,
                    className: className,
                    objectId: objectId,
                    userId: userId,
                    editingDate: editingDate
            }, null, {
                    onComplete: onComplete_verifyMyLock
            });
            break;

        default:
            wcmBizAjaxController.call("biz.lock", {
                    messageId: messageId,
                    command: command,
                    className: className,
                    objectId: objectId,
                    userId: userId,
                    editingDate: editingDate
            });
            break;
    }
}

function onComplete_isObsolete(myAjaxResponse)
{
    // Check AJAX response
    if (myAjaxResponse.responseText == 'locked')
    {
        alert($I18N.OBJECT_IS_LOCKED);
        refresh();
    }
    else if (myAjaxResponse.responseText == 'true')
    {
        alert($I18N.OBJECT_IS_OBSOLETE);
        refresh();
    }
    else
    {
        if (!window.frmEditSubmit)
        {
            document.forms['frmEdit'].submit();
        }
        else
        {
            frmEditSubmit();
        }
    }
}

function onComplete_verifyMyLock(myAjaxResponse)
{
    if (myAjaxResponse.responseText == 'true')
    {
        if (!window.frmEditSubmit)
        {
            document.forms['frmEdit'].submit();
        }
        else
        {
            frmEditSubmit();
        }
    }
    else
    {
        alert($I18N.OBJECT_LOCK_EXPIRED);
        refresh();
    }
}





