function searchControl(module, action, assetId, options, divId)
{
    wcmBizAjaxController.call("biz.controlSearchResults", {
        module: module,
        action: action,
        assetId: assetId,
        options: options, 
        divId: divId
    });
}
