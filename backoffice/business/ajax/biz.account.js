/**
 * Project:     WCM
 * File:        biz.account.js
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

function ajaxAccount(command, type, wcmUserId, itemId, managerIdFrom, managerIdTo, divId, formDatas, fullText, orderBy){
    document.body.style.cursor = 'wait';
    wcmBizAjaxController.call("biz.account", {
        command: command,
        type: type,
        wcmUserId: wcmUserId,
        itemId: itemId,
        managerIdFrom: managerIdFrom,
        managerIdTo: managerIdTo,
        divId: divId,
        formDatas: formDatas,
        fullText: fullText,
        orderBy: orderBy
    });
    document.body.style.cursor = 'default';
}

function ajaxAccountSpe(command, type, wcmUserId, itemId, managerIdFrom, managerIdTo, divId, formDatas, fullText, orderBy, hideInactive){
    document.body.style.cursor = 'wait';
    wcmBizAjaxController.call("biz.account", {
        command: command,
        type: type,
        wcmUserId: wcmUserId,
        itemId: itemId,
        managerIdFrom: managerIdFrom,
        managerIdTo: managerIdTo,
        divId: divId,
        formDatas: formDatas,
        fullText: fullText,
        orderBy: orderBy,
        hideInactive: hideInactive
    });
    document.body.style.cursor = 'default';
}

function getAllUsers(managerId){
    document.body.style.cursor = 'wait';
    wcmBizAjaxController.call("biz.account", {
        command: 'getAllUsers',
        managerId: managerId
    });
    document.body.style.cursor = 'default';
}

function divAccountWait(){
    $("accountSummary").innerHTML = "<div class='wait' style='display:inline;'>Loading...</div>";
}

saveTreeAccountPermissions = function(accountId,permissionString)
{
	wcmBizAjaxController.call("biz.permissions", {
	command: 'setTreePermissions',
	accountId: accountId,
	permissionString: permissionString
    });
}
