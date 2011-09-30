/**
 * Project:     WCM
 * File:        biz.channelRequest.js
 *
 * @copyright   (c)2007 Nstein Technologies
 * @version     4.x
 *
 */
function ajaxChannelRequest(command, channelRequestId, divName, locked, xmlWhere, xmlOrder, className, name)
{
    // Appel d'un callback JS avant traitement ?
    var messageId = null;
    if (window.onAjaxChannelRequest)
    {
        messageId  = window.onAjaxChannelRequest(command, channelRequestId, divName, locked, xmlWhere, xmlOrder, className, name);
        // Annuler l'appel ?
        if (!messageId) return;
    }
    wcmBizAjaxController.call("biz.channelRequest", {
            messageId: messageId,
            command: command,
            channelRequestId: channelRequestId,
            divName: divName,
            locked: locked,
            xmlWhere: xmlWhere,
            xmlOrder: xmlOrder,
            className: className,
            name: name
            });
    // Appel de la method prepareForm()
    // Sauf dans le cas d'un test de validité de requête
    if (command != "checkRequestValidity")
        window.prepareForm();
}