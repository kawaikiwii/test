/**
 * Project:     WCM
 * File:        biz.relaxTask.js
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

function ajaxRelaxTask(command, divId, taskId, datas, displayFilter, sortField, sortOrder, searchdata)
{
	document.body.style.cursor = 'wait';
    wcmBizAjaxController.call("export/biz.relaxTask", {
        command: command,
        divId: divId,
        taskId: taskId,
        datas: datas,
        searchdata: searchdata,
        displayFilter: displayFilter,
        sortOrder: sortOrder,
        sortField: sortField
    });
	document.body.style.cursor = 'default';
}

function divWait() {
	$("relaxTaskSummary").innerHTML = "<div class='wait' style='display:inline;'>Loading...</div>";
}


function toggleSortOrder(sortedField)
{
    var sortOrderBy = $('sortOrder').value;

    if (sortOrderBy == 'ASC')
    	sortOrderBy = 'DESC';
    else
    	sortOrderBy = 'ASC';
    	
    $('sortOrder').value = sortOrderBy;	
    ajaxRelaxTask('refresh', 'results', null, null, $('displayFilter').value, sortedField, sortOrderBy);
}

function toggleCheckBoxes(formName)
{
	// toggle Check Boxes using Prototype Library
		var form=$(formName);
		var i=form.getElements('checkbox');
		i.each(function(item)
		{
			if (item.checked)
				item.checked=false;
			else 
				item.checked=true;
		}
	);
}

function checkUncheckAll(idParent)
{
	checkboxes = $(idParent).select('input[type=checkbox]');
	checkboxes.each(function(item)
		{
			if(!item.disabled) {
				if (item.checked)
					item.checked=false;
				else 
					item.checked=true;
			}
		}
	);
}

function checkBoxesAction(formName, action, divId, datas, displayFilter, sortField, sortOrder, searchdata)   
{
	var form=$(formName);
	var i=form.getElements('checkbox');
	i.each(function(item)
	{
		if (item.checked)
		{
			 wcmBizAjaxController.call("export/biz.relaxTask", {
			        command: action,
			        divId: divId,
			        taskId: item.id,
			        datas: datas,
			        searchdata: searchdata,
			        displayFilter: displayFilter,
			        sortOrder: sortOrder,
			        sortField: sortField
			    });
		}
	}
);
} 
