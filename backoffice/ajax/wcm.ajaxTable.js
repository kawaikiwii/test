/**
 * Project:     WCM
 * File:        wcm.ajaxTable.js
 *
 * @copyright   (c)2007 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Ajax to manipulate datalayer tables.
 */

/**
 * Executes a simple datalayer table command like 'create_db',
 * 'create_api' or 'create_bo'.
 *
 * @param command The command to execute
 * @param tableId The ID of the table to affect
 */
function executeTableCommand(command, tableId)
{
    var parameters = {
        command: command,
        tableId: tableId
    };
    wcmSysAjaxController.call('wcm.ajaxTable', parameters, 'onAjaxTableCommand');
}
