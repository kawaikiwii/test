<script language="javascript" type="text/javascript">
    /**
     * onSave callback used to validate mainForm before submit
     *
     * @return null if form is validated, error message otherwise
     */
    onSave = function()
    {
        var error = null;

        if ($('mainForm').className.value == '')
        {
            error += 'Classname is mandatory';
        }
        
        return error;
    }
    wcmActionController.registerCallback('save', onSave);

    /**
     * Update connectors table (invoked onChange of connectorId)
     *
     * @param ajaxBaseURL  Absolute URL to base ajax folder
     * @param id Current connector id
     * @param table Current connector table
     */
    updateConnectorTables = function(ajaxBaseURL, id, table)
    {
        new Ajax.Updater(   $('connectorTable'),
                            wcmBaseURL + 'ajax/controller.php?ajaxHandler=bizlogic/updateConnectorTables',
                            {
                                asynchronous: false,
                                parameters:
                                {
                                    id: id,
                                    table: table
                                }
                            });
    }
</script>
