function ajaxRelation(command, sourceClass, sourceId, from, to, destinationClass, destinationId, kind, header, title, divName, locked, callback, link_article)
{
    var messageId = null;
    if (window.onAjaxRelation)
    {
        messageId  = window.onAjaxRelation(command, sourceClass, sourceId, from, to, destinationClass, destinationId, kind, header, title, divName);
        if (!messageId) return;
    }
    wcmBizAjaxController.call("biz.relation", {
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
            callback: callback,
            link_article: link_article
    });
}

function ajaxUpdateChannelContent(command, sourceClass, sourceId, from, to, destinationClass, destinationId, kind, header, title, divName, locked, validityDate)
{
    var messageId = null;
    if (window.onAjaxRelation)
    {
        messageId  = window.onAjaxRelation(command, sourceClass, sourceId, from, to, destinationClass, destinationId, kind, header, title, divName);
        if (!messageId) return;
    }
    wcmBizAjaxController.call("biz.manageChannel", {
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
            validityDate: validityDate
    });
}