/**
 * Project:     WCM
 * File:        biz.contribution.js
 *
 * @copyright   (c)2007 Nstein Technologies
 * @version     4.x
 *
 */

// gestion d'une contribution
function ajaxContribution(command, itemId, divId)
{
    wcmBizAjaxController.call("biz.contribution", {
        command: command,
        itemId: itemId,
        divId: divId
    });
}
