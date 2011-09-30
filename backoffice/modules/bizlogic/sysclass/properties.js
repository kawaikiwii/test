<script language="javascript" type="text/javascript">
    /**
     * onSave callback used to validate mainForm before submit
     *
     * @return null if form is validated, error message otherwise
     */
    onSave = function()
    {
        var error = null;

        if ($F('mainForm').className.value == '')
            error += '- Classname is mandatory\n';
        
        return error;
    }
</script>