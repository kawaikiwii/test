<script type="text/javascript">
_wcmRollbackVersion = function(id) {
    wcmModal.confirm(
        $I18N.ROLLBACK_VERSION,
        $I18N.ROLLBACK_VERSION_CONFIRM_MSG,
        function(response) {
            if (response == 'YES') {
                // trigger the controller to rollback this specific version
                wcmActionController.triggerEvent('rollback', { versionId: id });
            }
        });
}

_wcmRestoreVersion = function(id) {
    wcmModal.confirm(
        $I18N.RESTORE_VERSION,
        $I18N.RESTORE_VERSION_CONFIRM_MSG,
        function(response) {
            if (response == 'YES') {
                // trigger the controller to restore this specific version
                wcmActionController.triggerEvent('restore', { versionId: id });
            }
        });
}

_wcmAddVersion = function() {
    wcmModal.prompt(
        $I18N.ADD_NEW_VERSION,
        $I18N.VERSION_COMMENT, '',
        function(response) {
            if (response != null) {
                // trigger the controller to save and create a version with this comment.
                wcmActionController.triggerEvent('save', { comment: response });
            }
        });
}
</script>