/**
 * Project:     NCM
 * File:        biz.issue.js
 *
 * @copyright   (c)2007 Nstein Technologies
 * @version     4.x
 *
 */

// gestion d'une issue
function ajaxIssue(command, itemId, divId)
{
    wcmBizAjaxController.call("biz.issue", {
        command: command,
        itemId: itemId,
        divId: divId
    });
}
