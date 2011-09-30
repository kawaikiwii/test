function manageSaveSearch(command, name, description, queryString, url, id, divId, dashboard, shared)
{
    var params = {
        command: command,
        name: name,
        description: description,
        queryString: queryString,
        url: url,
        id: id,
        divId: divId,
        dashboard: dashboard,
        shared: shared
    };
    var options = {};

    var cancelled = false;
    switch (command)
    {
    case 'create':
        var items = $A($('savedSearches').childNodes);
        if (undefined != items.find(function (item) {
                    return item.childNodes.item(1).firstChild.nodeValue == name;
                }))
        {
            alert($I18N.OBJECT_ALREADY_EXISTS);
            cancelled = true;
        }
        break;

    case 'remove':
        cancelled = !confirm($I18N.CONFIRM_DELETE_CURRENT_OBJECT);
        break;
    }

    if (cancelled)
        return false;

    wcmBizAjaxController.call("biz.manageSaveSearch", params, null, options);
    return true;
}
