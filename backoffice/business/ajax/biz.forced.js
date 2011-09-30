/**
 * Project:     NCM
 * File:        biz.forced.js
 *
 * @copyright   (c)2007 Nstein Technologies
 * @version     4.x
 *
 */

function ajaxForced(command, sourceClass, sourceId, from, to, destinationClass, destinationId, kind, header, title, divName, locked, callback)
{
    // Appel d'un callback JS avant traitement ?
    var messageId = null;
    if (window.onAjaxForced)
    {
        messageId  = window.onAjaxForced(command, sourceClass, sourceId, from, to, destinationClass, destinationId, kind, header, title, divName);
        
        // Annuler l'appel ?
        if (!messageId) return;
    }

    wcmBizAjaxController.call("biz.forced", {
            messageId: messageId,
            command: command,
            sourceClass: sourceClass,
            sourceId: sourceId,
            from: from,
            to: to,
            destinationClass: destinationClass,
            destinationId: destinationId,
            kind: kind,
            header: header,
            divName: divName,
            title: title,    
            locked: locked,
            callback: callback
    });
}
