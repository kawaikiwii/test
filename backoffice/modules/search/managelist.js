<script type="text/javascript">
manage = function(action, objectclass, objectid, params)
{
	new Ajax.Request(
		'<?php echo $config['wcm.backOffice.url'];?>/ajax/managelist/managelist.php',
		{ 
			method:'get', 
		  	parameters: 
		  	{ 
		  		object_id: objectid,
		  		object_class: objectclass,
		  		action: action,
		  		params: params
		  	}, 
		  	onSuccess: 
		  		function(transport)
		  		{
		  			if(transport.responseText == 1)
		  			{
		  				if(action == "delete")
		  				{
		  					$('_nlsub_'+objectid).hide();
		  					wcmMessage.info('<?php echo _OBJECT_DELETED;?>');
		  				}
		  				else
		  				{
		  					wcmMessage.info('<?php echo "updated";?>');
		  				}
		  			}
		  			else
		  			{
		  				wcmMessage.error('<?php echo _UNEXPECTED_ERROR; ?>')
		  			}
		  		}
		  } 
	);	
}
var objectclass;
var objectid;
deletesearch = function (objclass, objid)
{
	objectclass = objclass;
	objectid = objid;
	wcmModal.confirm($I18N.DELETE, $I18N.CONFIRM_DELETE_CURRENT_OBJECT, deletecallback);
}
deletecallback = function(name)
{
	switch(name){
            case "YES":
				manage('delete',objectclass,objectid); 
  		 	break;
  	}
}
edit = function (classname, id)
{
	wcmModal.showAjaxButtons('Edit', wcmBaseURL + 'business/modules/modalbox/edit_saved_search.php', {id: id}, editCallback, 	[wcmModal.getButtonByName("CANCEL"), 
  		 wcmModal.getButtonByName("SAVE")]); 
}
editCallback = function(name)
{
	switch(name){
            case "SAVE":
				wcmModal.showAjaxButtons('Edit', wcmBaseURL + 'business/modules/modalbox/edit_saved_search.php', {id: $('saved_search_id').value, action: 'save', saved_search_name: $('saved_search_name').value, saved_search_description: $('saved_search_description').value}, editCallback, 	[wcmModal.getButtonByName("OK")] 
  		 		);
  		 	break;
  		 	case "OK":
				$('name_'+$('saved_search_id').value).innerHTML = $('saved_search_name').value;
				$('description_'+$('saved_search_id').value).innerHTML = $('saved_search_description').value;
					 	
  		 	break;
  	}
}			

</script>