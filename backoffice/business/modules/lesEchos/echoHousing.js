<script type="text/javascript">
addEchoHousing = function(url, id, obj)
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

removeEchoHousing = function(button, id)
{
	if($$('#echoHousing .collapsable').length > 1)
	{
		$(button).up('.collapsable').remove();
	}
	else
	{
		wcmMessage.warning('Impossible', 1500);
	}
}

_wcmDeleteAllElement = function(obj, id){
    $(id).setValue('');
    var elems = obj.next('ul').childElements();
    for (var i = 0; i < elems.length; i++) 
        elems[i].remove();
    
}
_wcmAddElement = function(obj, id, event)
{
	if (!obj.value || obj.value == '') 
        return;
    
    // check keypress
    if (event) {
    	switch (event.keyCode) {
            // enter cause adding
            case 13:
                Event.stop(event);
                break;
                
            // comma is not allowed (as it's used for internal separator)
            case 188:
                Event.stop(event);
                return;default:
                return;        }
    }
    else
    {
    	_wcmDeleteAllElement(obj, id);
    }
    
    // update value
    var value = $F(id);
    //if (value != '') 
    //    value += ',';
    value = obj.value;
    $(id).setValue(value);
     
    // do not allow 2 identical values
    var exists = false;
    var elements = obj.up('li').descendants();
    if (elements) {
        elements.each(function(elem){
            if (elem.className == 'tags') {
                var emTags = elem.descendants('em');
                if (elem.descendants('em')) {
                    emTags.each(function(emTag){
                        if (emTag.innerHTML == obj.value) {
                            alert($I18N.CANNOTADDEXISTING);
                            exists = true;
                        }
                    });
                }
            }
        });
    }
    
    // update display but only if value is unique
    if (!exists) {
        obj.next('ul').insert('<li style="clear:both"><a href="#" onclick="_wcmDeleteElement($(this).up(), \'' + id + '\'); return false;"><span>' + $I18N.DELETE + '</span></a> <em>' + obj.value + '</em></li>');
        obj.value = '';
        return;
    }
}

_wcmDeleteElement = function(obj, id){
    // rebuild value
    var value = '';
    var siblings = obj.siblings();
    if (siblings) 
        siblings.each(function(li){
            if (value != '') 
                value += ',';
            value += li.down().next().innerHTML;
        });
    $(id).setValue(value);
    
    // remove tab
    obj.remove();
}

</script>
