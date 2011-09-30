<script type="text/javascript">
    wcmCheckProperties = function() {
        if ($F('publicationDate') && $F('expirationDate') && $F('embargoDate'))
        {
            if ($F('publicationDate') > $F('expirationDate'))
            {
                $('publicationDate').up('li').addClassName('error');
                return $I18N.PUBDATE_AFTER_EXPDATE;
            }
            else if ($F('embargoDate') > $F('expirationDate'))
            {
                $('embargoDate').up('li').addClassName('error');
                return $I18N.EMBARGO_AFTER_EXPDATE;
            }
            else
            	return null;
        }
        else
            return null;
    }
    wcmActionController.registerCallback('save', wcmCheckProperties);
    wcmActionController.registerCallback('checkin', wcmCheckProperties);

</script>