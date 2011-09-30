<script type="text/javascript">
_wcmPurge = function() {

    wcmModal.confirm(
        $I18N.PURGE_CONTENT,
        '<ul>' + $I18N.PURGE_CONFIRM_MESSAGE + '</ul>',
        function(response) {
            if (response == 'YES') {
                $('wcmPurgeForm').submit();
            }
        });
}
</script>