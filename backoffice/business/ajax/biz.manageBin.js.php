function manageBin(command, name, description, object, id, divId, dashboard)
{
    var params = {
        command: command,
        name: name,
        description: description,
        object: object,
        id: id,
        divId: divId,
        dashboard: dashboard
    };
    var options = {};

    var cancelled = false;
    switch (command)
    {
    case 'addSessionToSelectedBin':
        options['onComplete'] = manageCheckBox(true);
        break;
    case 'createBinFromSession':
        options['onComplete'] = manageCheckBox(false);
        // fall through
    case 'createEmpty':
        if (undefined != $A($('selectBin').options).find(function (item) { return item.text == name }))
        {
            alert($I18N.OBJECT_ALREADY_EXISTS);
            cancelled = true;
        }
        break;
    case 'remove':
        if (name == $I18N.MY_BIN)
        {
            alert($I18N.INFORM_CANNOT_DELETE_CURRENT_OBJECT);
            cancelled = true;
        }
        else
        {
            cancelled = !confirm($I18N.CONFIRM_DELETE_CURRENT_OBJECT);
        }
        break;
    case 'clear':
        cancelled = !confirm($I18N.CONFIRM_DELETE_OBJECTS);
        break;
    }

    if (cancelled)
        return false;

    wcmBizAjaxController.call("biz.manageBin", params, null, options);
    return true;
}

function manageCheckBox(messageNoItem)
{
    var inputOptions;
    inputOptions=document.getElementsByTagName("input");
    for(i=0; i < inputOptions.length;i++)
    {
        if(inputOptions[i].type=="checkbox")
        {
            if (new String(inputOptions[i].getAttribute("id")).indexOf("item_") != -1)
            {
                if (inputOptions[i].checked == true){
                    inputOptions[i].checked=false;
                    messageNoItem = false;
                }
            }
        }
    }
    if (messageNoItem) _wcmNoItemsSelected();
}

_wcmNoItemsSelected = function() {
    wcmModal.showOk($I18N.NO_ITEMS_SELECTED, $I18N.NO_ITEMS_SELECTED);
}
