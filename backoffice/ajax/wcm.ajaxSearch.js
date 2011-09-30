/**
 * Project:     WCM
 * File:        wcm.ajaxSearch.js
 *
 * @copyright   (c)2007 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Ajax to perform a search.
 */

/**
 * Perform a search.
 *
 * @param id         The form id (frmSearch_$id) and the div id (result_$id) to populate
 * @param callback   The javascript function to call on item selection
 * @param mode       The xsl mode (default is "list")
 * @param someResult The sentence to display when some result are found
 * @param noResult   The sentence to display when to result are found
 * @param pageSize   The size or result page (default 10)
 * @param pageNumber The page number (default 1)
 *
 */
function executeSearch(id, callback, mode, someResult, noResult, pageSize, pageNumber)
{
    // Fix some options
    if (!callback)   callback   = "onSelectItem";
    if (!pageSize)   pageSize   = 20;
    if (!pageNumber) pageNumber = 1;

    // Retrieve query parameters
    var search_id              = '';
    var search_className       = '';
    var search_createdAt       = '';
    var search_modifiedAt      = '';
    var search_checkedOutAt    = '';
    var search_fulltext        = '';
    var search_orderBy         = '';

    if (document.forms['frmSearch_'+id].search_orderBy)
        search_orderBy = document.forms['frmSearch_'+id].search_orderBy.value;

    if (document.forms['frmSearch_'+id].search_className)
        search_className = document.forms['frmSearch_'+id].search_className.value;

    if (document.forms['frmSearch_'+id].search_id)
        search_id = document.forms['frmSearch_'+id].search_id.value;

    if (document.forms['frmSearch_'+id].search_createdAt)
        search_createdAt = document.forms['frmSearch_'+id].search_createdAt.value;

    if (document.forms['frmSearch_'+id].search_modifiedAt)
        search_modifiedAt = document.forms['frmSearch_'+id].search_modifiedAt.value;

    if (document.forms['frmSearch_'+id].search_checkedOutAt)
        search_checkedOutAt = document.forms['frmSearch_'+id].search_checkedOutAt.value;

    if (document.forms['frmSearch_'+id].search_fulltext)
        search_fulltext = document.forms['frmSearch_'+id].search_fulltext.value;

    var parameters = {
        id: 'result_' + id,
        formId: id,
        callback: callback,
        mode: mode,
        someResult: someResult,
        noResult: noResult,
        search_className: search_className,
        search_id: search_id,
        search_createdAt: search_createdAt,
        search_modifiedAt: search_modifiedAt,
        search_checkedOutAt: search_checkedOutAt,
        search_fulltext: search_fulltext,
        search_orderBy: search_orderBy,
        pageSize: pageSize,
        pageNumber: pageNumber
    };
    wcmSysAjaxController.call('wcm.ajaxSearch', parameters);
}
