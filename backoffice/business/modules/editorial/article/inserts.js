<script type="text/javascript">
addInserts = function(url, id, obj)
{
    var ajax = new Ajax.Request(url, {
                onSuccess: function(transport) {
                    if (id)
                    {
                        $(id).insert({bottom: transport.responseText});
                        document.location.href='#newpagebutton';
                    }
                    else
                    {
                        $(obj).up('div.collapsable').insert({after: transport.responseText});
                    }
                }
            });
}

removeInserts = function(button, id)
{
	if($$('#inserts .collapsable').length > 1)
	{
		$(button).up('.collapsable').remove();
	}
	else
	{
		wcmMessage.warning($I18N.CANNOTREMOVEINSERT, 1500);
	}
}
</script>
