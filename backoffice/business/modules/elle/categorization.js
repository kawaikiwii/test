<script type="text/javascript">
_wcmAddElementSpe = function(obj, id, event, specialId, specialI)
{
	if (!obj.value || obj.value == '') return;
    
    // check keypress
    if (event)
    {
        switch(event.keyCode)
        {
            // enter cause adding
            case 13:
                Event.stop(event);
                break;

            default:
                return;
        }
    }
    
    // normalize value to add/search for (word after last trailing slash for hierarchical terms)
    var newValue = obj.value;
    
    if (newValue.lastIndexOf('|') != -1)
    {
    	//wcmModal.showOk($I18N.NOTIFICATION, '<ul>'+$I18N.PIPE_NOT_ALLOWED+'</ul>');
    	return;
    }
    if (newValue.lastIndexOf('/') != -1)
    {
        newValue = newValue.substring(newValue.lastIndexOf('/')+1);
    }

    // do not allow 2 identical values (case insensitive)
    var existingValues = ($F(id)) ? '|' + $F(id).toLowerCase() +  '|' : '';
    if (existingValues.indexOf('|' + newValue.toLowerCase() + '|') != -1)
    {
    	//wcmModal.showOk($I18N.NOTIFICATION, '<ul>'+$I18N.CANNOTADDEXISTING+'</ul>');	
    }
    else
    {
    	// update value
        var value = $F(id);
        if (value != '') value += '|';
        value += newValue;
        $(id).setValue(value);

        //obj.next('ul').insert('<li><a href="#" onclick="_wcmDeleteElement($(this).up(), \'' + id + '\'); return false;"><span>' + $I18N.DELETE + '</span></a> <em>' + newValue + '</em></li>');
        //obj.value = '';
        $('iptc').next('ul').insert('<li><a id="relatedIptcTagsAnchor_'+ specialId +'_'+ specialI +'" href="#" onclick="_wcmDeleteElement($(this).up(), \'' + id + '\'); return false;"><span>' + $I18N.DELETE + '</span></a> <em>' + newValue + '</em></li>');
        if (!specialId || specialId == '') obj.value = '';
        return;
    }
}

_wcmDeleteElementSpe = function(obj, id)
{
    // rebuild value
    var value = '';
    
	
	var siblings = obj.siblings();
    if (siblings) siblings.each(function(li) {
        if (value != '') value += '|';
        value += li.down().next().innerHTML.replace(/&nbsp;/g, ' '); }
        );
    
	$(id).setValue(value);
    
    // remove tab
    obj.remove();
}

_addIptcTags = function(id, num, obj)
{
	//alert(obj.checked + '-' + obj.getAttribute('rel'));
	
	if (obj.getAttribute('rel') == 'checked' && obj.checked != true)
	{
		for (var i = 0; i < num; i++)
		{
			if ($('relatedIptcTagsAnchor_' + id + '_' + i))
			{
				_wcmDeleteElementSpe($('relatedIptcTagsAnchor_' + id + '_' + i).up(), 'iptc');
			}
		}
		obj.setAttribute('rel', 'none');
	}
	else if (obj.checked == true)
	{
		for (var i = 0; i < num; i++)
		{
			//alert('relatedIptcTags_' + id + '_' + i);
			
			_wcmAddElementSpe($('relatedIptcTags_' + id + '_' + i), 'iptc', null, id, i);
		}
		obj.setAttribute('rel', 'checked');
	}
	return;
}

</script>