<script type="text/javascript">
_wcmAddElement = function(obj, id, event)
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

    // update value
    var value = $F(id);
    
    if (obj.value.lastIndexOf('|') != -1)
    {
    	wcmModal.showOk($I18N.NOTIFICATION, '<ul>'+$I18N.PIPE_NOT_ALLOWED+'</ul>');
    	return;
    }
    
    if (value != '') value += '|';
    value += obj.value;
    $(id).setValue(value);

    // do not allow 2 identical values
    var exists = false;
    var elements = obj.up('li').descendants();
    if (elements)
    {
        elements.each(function (elem)
        {
           if (elem.className == 'tags')
           {
               var emTags = elem.descendants('em');
               if (elem.descendants('em'))
               {
                   emTags.each(function(emTag)
                   {
                       if (emTag.innerHTML == obj.value)
                       {
                           	wcmModal.showOk($I18N.NOTIFICATION, '<ul>'+$I18N.CANNOTADDEXISTING+'</ul>');
                           exists = true;
                       }
                   });
               }
            }
        });
    }

    // update display but only if value is unique
    if (!exists)
    {
        obj.next('ul').insert('<li><a href="#" onclick="_wcmDeleteElement($(this).up(), \'' + id + '\'); return false;"><span>' + $I18N.DELETE + '</span></a> <em>'+obj.value+'</em></li>');
        obj.value = '';
        return;
    }
}

_wcmAddElementText = function(obj,text, id, event)
{
 
    // check keypress
    if (event)
    {
        switch(event.keyCode)
        {
            // enter cause adding
            case 13:
                Event.stop(event);
                break;
                
            // comma is not allowed (as it's used for internal separator)
            case 188:
                Event.stop(event);
                return;

            default:
                return;
        }
    }

    // update value
    var value = $F(id);
    var reg = RegExp(text);
   
    if(! reg.exec(value))
    {
    	if (value != '') value += '|';
    	value += text;
    	$(id).setValue(value);

    	// update display
    	obj.next('ul').insert('<li><a href="#" onclick="_wcmDeleteElement($(this).up(), \'' + id + '\'); return false;"><span>' + $I18N.DELETE + '</span></a> <em>'+text+'</em></li>');
        	
    	return;
    }
}

_wcmDeleteAllElement = function(obj, id)
{
	$(id).setValue('');
	var elems = obj.next('ul').childElements();
	for(var i=0; i<elems.length; i++)
		elems[i].remove();
		
}

_wcmDeleteElement = function(obj, id)
{
    // rebuild value
    var value = '';
    var siblings = obj.siblings();
    if (siblings) siblings.each(function(li) { if (value != '') value += '|'; value += li.down().next().innerHTML; });
    $(id).setValue(value);
    
    // remove tab
    obj.remove();
}
</script>