<script type="text/javascript">
_wcmDeleteElement = function(obj, id)
{
    /* rebuild value */
    var value = '';
    var siblings = obj.siblings();
    if (siblings) siblings.each(function(li) { 
    if (value != '') value += ','; value += li.id; });
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
 // update value
    var bizobjectId = obj.value.split(':'); 
    
    var data = '';
    if (bizobjectId[1] == undefined)
    	data = obj.value;
    else
    	data = bizobjectId[1];
    
    var value = document.getElementById(id).value; 
    
    if (value != '' && bizobjectId[1] != undefined) value += '|';
    else if (value != '' && bizobjectId[1] == undefined) value += ',';
    
    value += data;

    document.getElementById(id).setValue(value);

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
                           alert($I18N.CANNOTADDEXISTING);
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

</script>