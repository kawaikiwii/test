function modalPopup(command, kind, id, input, targetid, type, order)
{	
	wcmBizAjaxController.call("biz.modalPopup", {
        command: command,
        kind: kind,
        id: id,
        input: input,
        targetid: targetid,
        type: type,
        order: order
    });
}