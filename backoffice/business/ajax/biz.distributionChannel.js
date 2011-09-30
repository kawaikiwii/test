/**
 * Project:     WCM
 * File:        biz.distributionChannel.js
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

function ajaxDistributionChannel(command, exportRuleId, itemId, divId, formDatas)
{
    wcmBizAjaxController.call("biz.distributionChannel", {
        command: command,
        exportRuleId: exportRuleId,
        itemId: itemId,
        divId: divId,
        formDatas: formDatas
    });
}
