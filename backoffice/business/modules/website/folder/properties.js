<script type="text/javascript">
/**
@todo : add this if publication date and expiration date are added
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
*/
</script>