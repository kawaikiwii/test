<script type="text/javascript">
/*
addChoice = function(position, url, obj)
{
    var ajax = new Ajax.Request(url, {
                onSuccess: function(transport) {
					$('pages').insert({bottom: transport.responseText});
					document.location.href='#newpagebutton';
                }
            });
}

removeChoice = function(button, id)
{
	if($$('#pages .collapsable').length > 1)
	{
		$(button).up('.collapsable').remove();
	}
	else 
	{
		wcmMessage.warning($I18N.CANNOTREMOVECHOICE, 1500);
	}
}
*/
addChoice = function(url, id, obj)
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

removeChoice = function(button, id)
{
    if($$('#pages .collapsable').length > 1)
    {
        $(button).up('.collapsable').remove();
    }
    else
    {
        wcmMessage.warning($I18N.CANNOTREMOVECHOICE, 1500);
    }
}

</script>
