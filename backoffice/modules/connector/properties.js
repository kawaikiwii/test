<script language="javascript" type="text/javascript">
    /**
     * onSave callback used to validate mainForm before submit
     *
     * @return null if form is validated, error message otherwise
     */
    onSave = function()
    {
        var error = null;

        if ($F('mainForm').name.value == '')
            error += '- Name is mandatory\n';

        if ($F('mainForm').reference.value == '')
            error += '- Reference is mandatory\n';
        
        if ($F('mainForm').connectionString.value == '')
            error += '- ConnectionString is mandatory\n';

        return error;
    }
</script>