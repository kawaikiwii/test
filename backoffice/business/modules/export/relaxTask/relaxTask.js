<script type="text/javascript">
_wcmDeleteElement = function(obj, id)
{
    /* rebuild value */
    var value = '';
    var siblings = obj.siblings();
    if (siblings) siblings.each(function(li) { 
    if (value != '') value += '|'; value += li.id; });
    $(id).setValue(value);
    obj.remove();
}


_wcmAddElement = function(obj, id, event, className)
{
    if (!obj.value || obj.value == '') return;

    /* check keypress */
    if (event)
    {
        switch(event.keyCode)
        {
		/* enter cause adding */
		case 13:
		    Event.stop(event);
		    break;

		default:
		    return;
		    break;
        }
    }
    var parameters = {
    	prefix: obj.value,
    	className: className
    };
    wcmBizAjaxController.callWithoutUpdate('autocomplete/wcm.relaxTask', parameters, null,
    {onComplete: function(myAjaxResponse) 
	{
		if (myAjaxResponse.responseText == 'invalid')
			alert('Export Rule invalide');
		else
		{
			var separator = myAjaxResponse.responseText.indexOf('#');
			var bizobjectId    = myAjaxResponse.responseText.substring(0,separator);
			var bizobjectLabel = myAjaxResponse.responseText.substring(separator+1);

			var existingValues = ($F(id)) ? '|' + $F(id) +  '|' : '';
			if (existingValues && (className == 'account'))
			{
				alert('only one');
			}
			else
			{
				if (existingValues.indexOf('|' + bizobjectId + '|') != -1)
				{
				    alert($I18N.CANNOTADDEXISTING);
				}
				else
				{
				    var value = $F(id);
				    if (value != '') value += '|';

				    regValue = bizobjectId.replace(/\s/g, ' ');

				    value += regValue;
				    $(id).setValue(value);
				    obj.next('ul').insert('<li style="clear:both" id=\'' + bizobjectId + '\'><a href="#" onclick="_wcmDeleteElement($(this).up(), \'' + id + '\'); return false;"><span>' + $I18N.DELETE + '</span></a> <em>' + bizobjectLabel + '</em></li>');
				    obj.value = '';
				    return;
				}
			}
		}
	}	    
    });
}

</script>