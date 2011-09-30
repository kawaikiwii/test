/**
 * Project:     WCM
 * File:        biz.foldersmngt.js
 *
 * @copyright   (c)2007 Nstein Technologies
 * @version     4.x
 *
 */


/*
 * SAVE
 */
function saveFolder(title, itemId)
{
	import_itemId = itemId;
	import_title = title;
	wcmModal.confirm($I18N.SAVE, $I18N.SAVE+' "'+title+'" ?', saveFolderCallback);
}
function saveFolderCallback(btn)
{
	switch (btn) {
		case "YES":
			//executeTransition(import_title, import_itemId);
			wcmBizAjaxController.call("biz.foldersmngt", {
		        command: 'save',
				title: import_title,
		        itemId: import_itemId
		    });
			window.location.href = window.location;
		break;
  		case "NO":
			break;
		default:
			break;
	}
}

/*
 * SAVE NEW
 */
function saveNewFolder(title, parentId)
{
	import_title = title;
	import_parentId = parentId;
	wcmModal.confirm($I18N.SAVE, $I18N.SAVE+' "'+title+'" ?', saveNewFolderCallback);
}
function saveNewFolderCallback(btn)
{
	switch (btn) {
		case "YES":
			//executeTransition(import_title, import_itemId);
			wcmBizAjaxController.call("biz.foldersmngt", {
		        command: 'saveNew',
				title: import_title,
				parentId: import_parentId
		    });
			window.location.href = window.location;
		break;
  		case "NO":
			break;
		default:
			break;
	}
}


/*
 * DELETE
 */
function deleteFolder(title, itemId)
{
	import_itemId = itemId;
	wcmModal.confirm($I18N.DELETE, $I18N.DELETE+' "'+title+'" ?', deleteFolderCallback);
}

function deleteFolderCallback(btn)
{
	switch (btn) {
		case "YES":
			wcmBizAjaxController.call("biz.foldersmngt", {
		        command: 'delete',
		        itemId: import_itemId
		    });
			window.location.href = window.location;
			break;
  		case "NO":
			break;
		default:
			break;
	}
}
