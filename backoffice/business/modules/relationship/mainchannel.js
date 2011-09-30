<script type="text/javascript">
wcmgetformdata = function()
{
	 if (!$('title')) 
	 {
     	alert($I18N.PLEASE_LOAD_CONTENT_TAB);
        return -1;
     }
     else
     {
     	return Object.toJSON(wcmGetFulltext('<?php echo $bizobject->getClass();?>'));
     }
}
</script>