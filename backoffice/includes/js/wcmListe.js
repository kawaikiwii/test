/*

function getFinalContent(id,depth,key)
{
    var parameters = {
        command: 'getFinalContent',
        id: id,
        depth: depth,
        key: key
    };
    
    wcmSysAjaxController.call('wcm.ajaxListe', parameters);
}

function addList(code,label,parentId)
{
    var parameters = {
        command: 'addList',
        code: code,
        label: label,
        parentId: parentId
    };
    
    wcmSysAjaxController.call('wcm.ajaxListe', parameters);
    getFinalContent(parentId,1,'id');
    
    $('labelliste').value='';
    $('codeliste').value='';
    $('idliste').value=0;
}

function updateList(id,code,label)
{
    var parameters = {
        command: 'updateList',
        id: id,
        code: code,
        label: label,
    };
    
    wcmSysAjaxController.call('wcm.ajaxListe', parameters);
    getFinalContent($('listeselect').value,1,'id');
    
    $('btnUpdate').style.display='none';
    $('btnAdd').style.display='';
    $('labelliste').value='';
    $('codeliste').value='';
    $('idliste').value=0;
}

function deleteList(id)
{
    var parameters = {
        command: 'deleteList',
        id: id
    };
    
    wcmSysAjaxController.call('wcm.ajaxListe', parameters);
    getFinalContent($('listeselect').value,1,'id');
    
    $('btnUpdate').style.display='none';
    $('btnAdd').style.display='';
    $('labelliste').value='';
    $('codeliste').value='';
    $('idliste').value=0;
}

function clickEdit(idliste,code,label)
{
  $('btnUpdate').style.display='';
  $('btnAdd').style.display='none';
  $('labelliste').value=label;
  $('codeliste').value=code;
  $('idliste').value=idliste;
}
*/
//-------------------------

function addList(parentId, label, code)
{
  var url = 'index.php?_wcmAction=list';
  url += '&parentId='+parentId;
  url += '&label='+label;
  url += '&code='+code;
  url += '&todo=addList';
  
  window.location = url;
}

function updateList(parentId, id, label, code)
{
  var url = 'index.php?_wcmAction=list';
  url += '&id='+id;
  url += '&parentId='+parentId;
  url += '&label='+label;
  url += '&code='+code;
  url += '&todo=updateList';
  
  window.location = url;
}

function deleteList(parentId, id)
{

  var answer = myConfirm();
  if(answer)
  {
    var url = 'index.php?_wcmAction=list';
    url += '&id='+id;
    url += '&parentId='+parentId;
    url += '&todo=deleteList';
    
    window.location = url;
  }
}

function updateListChild(id, idchild, labelchild, codechild)
{
  var url = 'index.php?_wcmAction=list';
  url += '&id='+id;
  url += '&idchild='+idchild;
  url += '&labelchild='+labelchild;
  url += '&codechild='+codechild;
  url += '&todo=updatechild';
  
  window.location = url;
}

function deleteListChild(id, idchild)
{
  var answer = myConfirm();
  if(answer)
  {
    var url = 'index.php?_wcmAction=list';
    url += '&id='+id;
    url += '&idchild='+idchild;
    url += '&todo=deletechild';
    
    window.location = url;
  }
}

function addListChild(id, labelchild, codechild)
{
  var url = 'index.php?_wcmAction=list';
  url += '&id='+id;
  url += '&labelchild='+labelchild;
  url += '&codechild='+codechild;
  url += '&todo=addchild';
  
  window.location = url;
}

