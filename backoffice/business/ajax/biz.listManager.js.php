function getList(action, bizobjectId, bizobjectClass, parentId, parentObject, listDisplayId)
{
        
    document.getElementById('ajaxMessage').innerHTML = $I18N.LOADING + '...';
    if (!parentId)
        var parentId = document.getElementById(parentObject+'Id').value;

    if (action == "checkin")
    {
        if (!bizobjectId)
            var bizobjectId = document.getElementById('bizobjectId').innerHTML;

        var rank = document.getElementById('rank').value;
        
        tinyMCE.triggerSave();
        var text = document.getElementById('bizObject_manage').value;
      
        if (document.getElementById('title' + bizobjectClass) != null)
            var title = document.getElementById('title' + bizobjectClass).value;

        wcmBizAjaxController.callWithoutUpdate('biz.listManager', {
                ajaxMessageId: 'ajaxMessage',
                listDisplayId: listDisplayId,
                action: action,
                bizobjectId: bizobjectId,
                bizobjectClass: bizobjectClass,
                parentId: parentId,
                parentObject: parentObject,
                text: text,
                rank: rank,
                title: title,
                locked: 'false'
        }, null, {
                onComplete: showResponse
        });
    }
    else if ( (action == 'delete') || (action == 'moveup') || (action == 'movedown'))
    {
        wcmBizAjaxController.callWithoutUpdate('biz.listManager', {
                ajaxMessageId: 'ajaxMessage',
                listDisplayId: listDisplayId,
                action: action,
                bizobjectId: bizobjectId,
                bizobjectClass: bizobjectClass,
                parentId: parentId,
                parentObject: parentObject,
                text: text,
                title: title,
                rank: rank,
                locked: 'false'
        }, null, {
                onComplete: showResponse
        });
    }
    else
    {
        wcmBizAjaxController.callWithoutUpdate('biz.listManager', {
                ajaxMessageId: 'ajaxMessage',
                listDisplayId: listDisplayId,
                action: action,
                bizobjectId: bizobjectId,
                bizobjectClass: bizobjectClass,
                parentId: parentId,
                parentObject: parentObject,
                locked: 'true'
        }, null, {
                onComplete: showResponse
        });
    }
    if (action != 'show')
    {
        document.getElementById('new').focus();
    }
    
}

// parse ajax response
function showResponse(xmlDoc)
{
    // TODO : this code only works for chapters. Needs to be adapted to be more portable
    if (document.getElementById('phlnk') != null)
    {
        for (var i = 1; i < 50; i++)
        {
            document.getElementById('relatedPhotos_' + i).innerHTML = '';
        }
    }
    var response = xmlDoc.responseXML.getElementsByTagName("ajax-response");
    var elems    = response[0].childNodes;
    for ( var i = 0 ; i < elems.length ; i++ )
    {
        var responseElement = elems[i];
        // only process nodes of type element.....
        if ( responseElement.nodeType != 1 )
            continue;
        var responseId   = responseElement.getAttribute("id");


        switch (responseId)
        {
            case 'ajaxMessage':
            case 'bizobjectId':
            case 'objectList':
            case 'phlnk':
                $(responseId).innerHTML = responseElement.firstChild.nodeValue;
                if ((document.getElementById('phlnk') != null) && (responseId == 'bizobjectId'))
                {
                    ajaxRelation('refresh','chapter',responseElement.firstChild.nodeValue, 0, '','', '', '3', '', '', 'relatedPhotos_', 'false');
                }
            break;
            case 'bizObject_manage':
                var inst = tinyMCE.getInstanceById('bizObject_manage');
                if (inst != null)
                    inst.setHTML(responseElement.firstChild.nodeValue);
                else
                    $(responseId).innerHTML = responseElement.firstChild.nodeValue;
            break;
            default :
                $(responseId).value = responseElement.firstChild.nodeValue;
            break;
        }
    }

}

function resetFields(object)
{
    document.getElementById('rank').value = '';
    document.getElementById('bizobjectId').innerHTML = '';

    if (document.getElementById('title' + object) != null)
    {
        document.getElementById('title' + object).value = '';
    }
    
    var phlnk = document.getElementById('phlnk');
    if (phlnk != null)
    {
        phlnk.innerHTML = '';

        for (var i = 1; i < 50; i++)
        {
            document.getElementById('relatedPhotos_' + i).innerHTML = '';
        }
    }

    // Recovering the value of bizObject_manage (tinyMCE)
    var editor = tinyMCE.getInstanceById('bizObject_manage');
    // Clearing the content of the editor
    editor.setHTML('');
}