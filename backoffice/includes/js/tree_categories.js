function doTreeCategoriesNode(tree, path, command) {
    wcmSysAjaxController.call("wcm.ajaxTreeview", {
        tree: tree,
        path: path,
        command: command
    });

    var bizInfo  = tree.split('_');
    var bizClass = bizInfo[1];
    var bizId    = bizInfo[2];
    var locked   = bizInfo[3];

    if (command == 'reload') {
        ajaxUpdateTagsList(null, bizClass, bizId, null, 'reload_categories', locked);
    }
    else if (command == 'select') {
        var pathItems = path.split(':');
        var category  = pathItems[pathItems.length - 1];

        ajaxUpdateTagsList(category, bizClass, bizId, null, null, locked);
    }
}

function doTreeCategoriesNodeAndRedirect(tree, path, command, redirectTo) {
    wcmSysAjaxController.redirect("wcm.ajaxTreeview", {
        tree: tree,
        path: path,
        command: command,
        url: escape(redirectTo)
    });
}
