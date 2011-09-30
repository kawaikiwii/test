

function executeTransition(className, id, transitionId, updatediv)
{
    var parameters = {
        className    : className,
        id           : id,
        transitionId : transitionId,
        updatediv    : updatediv
    };
    var options = {
        onSuccess: function() { $(updatediv).remove(); }
    };
    wcmSysAjaxController.call('wcm.workflow', parameters, null, options);
}