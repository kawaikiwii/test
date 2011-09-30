    <script language="javascript" type="text/javascript">
            function refresh()
            {
                window.location = '?_wcmClass=<?php echo get_class($sysobject) ?>&id=<?php echo $sysobject->id ?>&_wcmTodo=refresh';
            }
     
        function getTemplate(templateId)
        {
            if (confirm('<?php echoH8(_CONFIRM_LOAD_TEMPLATE);?>'))
            {
                wcmSysAjaxController.call('wcm.ajaxGetTemplate', {
                                            templateId: templateId, 
                                            divId: 'dbcontent'
                                            });
            }
        }
    </script>
