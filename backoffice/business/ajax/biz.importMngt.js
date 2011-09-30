/**
 * Project:     WCM
 * File:        biz.importMngt.js
 *
 * @copyright   (c)2007 Nstein Technologies
 * @version     4.x
 *
 */


function saveImport(title, object, itemId, transId, divId)
{
	import_object = object;
	import_itemId = itemId;
	import_transId = transId;
	import_divId = divId;
	wcmModal.confirm($I18N.SAVE, $I18N.SAVE+' "'+title+'" ?', saveImportCallback);
}
function saveImportCallback(btn)
{
	switch (btn) {
		case "YES":
			executeTransition(import_object, import_itemId, import_transId, import_divId);
		break;
  		case "NO":
		break;
		default:
		break;
	}
}



function deleteImport(title, object, itemId, divId)
{
	import_object = object;
	import_itemId = itemId;
	import_divId = divId;
	wcmModal.confirm($I18N.DELETE, $I18N.DELETE+' "'+title+'" ?', deleteImportCallback);
}

function deleteImportCallback(btn)
{
	switch (btn) {
		case "YES":
			wcmBizAjaxController.call("biz.importMngt", {
		        command: 'delete',
				object: import_object,
		        itemId: import_itemId,
		        divId: import_divId
		    });
		break;
  		case "NO":
		break;
		default:
		break;
	}
}


