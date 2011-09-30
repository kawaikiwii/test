<script type="text/javascript">
    wcmCheckProperties = function() {
        if ($F('publicationDate') && $F('expirationDate'))
        {
            if ($F('publicationDate') > $F('expirationDate'))
            {
                $('publicationDate').up('li').addClassName('error');
                return $I18N.PUBDATE_AFTER_EXPDATE;
            }
        }
        else
            return null;
    }
    wcmActionController.registerCallback('save', wcmCheckProperties);
    wcmActionController.registerCallback('checkin', wcmCheckProperties);
</script>