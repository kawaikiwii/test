<script type="text/javascript">
    wcmCheckProperties = function() {
        $('subscriptionStart').up('li').removeClassName('error');
        $('subscriptionEnd').up('li').removeClassName('error');
        if ($F('subscriptionStart') && $F('subscriptionEnd'))
        {
            if ($F('subscriptionStart') > $F('subscriptionEnd'))
            {
                $('subscriptionStart').up('li').addClassName('error');
                wcmMessage.error($I18N.SUBSTART_AFTER_SUBEND);
            }
            else
            {
                new Ajax.Updater('subscriptions','business/ajax/biz.addsubscription.php',
                        { method:'get', 
                          parameters: 
                          { newsletter_id: $F('newsletter_field_id'), 
                            subscriptionStart: $F('subscriptionStart'), 
                            subscriptionEnd: $F('subscriptionEnd'), 
                            webuser_id: $F('webuser')
                          }
                        }
                     );
            }
        }
        else if (!$F('subscriptionStart') ||  !$F('subscriptionEnd'))
        {
            if (!$F('subscriptionStart'))
    	        $('subscriptionStart').up('li').addClassName('error');
            if (!$F('subscriptionEnd'))
                $('subscriptionEnd').up('li').addClassName('error');
	        wcmMessage.error($I18N.SUBSTART_SUBEND_EMPTY);
        }
        else
            return null;
    }

</script>