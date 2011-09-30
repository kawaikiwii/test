function ajaxChannel(command, itemId, divId)
{
    wcmBizAjaxController.call("biz.channel", {
        command: command,
        itemId: itemId,
        divId: divId
    });
}
