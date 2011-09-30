var wcmGetFulltext = function (className)
{
    var fulltext = '';
    $('mainForm').disable();
    formElements = $('mainForm').getElements();
    switch (className)
    {
    // @todo : add specifics for every bizobject
        case 'photo' :
        case 'article' :
             formElements.each(function(e) { 
                if (e.name == 'title' ||
                    e.name == 'subtitle' ||
                    e.name == 'suptitle' ||
                    e.name == 'chapter_title[]' ||
                    e.name == 'abstract' ||
                    e.name == 'chapter_text[]' ||
                    e.name == 'caption'
                    )
                {
                    e.enable();
                    if(e.type == "textarea")
                    	e.value = tinyMCE.get(e.id).getContent({format : 'text'})
                }
                
              });
            var data = $('mainForm').serialize(true);
            break;
    }
    $('mainForm').enable();
    return data;
}