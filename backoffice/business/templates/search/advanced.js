<script language="javascript" type="text/javascript">
updateChannel = function(siteId)
{
    wcmBizAjaxController.call("biz.updateChannel", {
        siteId: siteId,
                selectName: 'search_channelId',
                divName: 'channels'
                });
}

updateWorkflowState = function(className)
{
    wcmBizAjaxController.call("biz.updateWorkflowState", {
        elementId: "workflowStates",
                selectId: "search_workflowState",
                selectName: "search_workflowState",
                className: className
                });
}
</script>
