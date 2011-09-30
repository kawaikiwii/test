<script type="text/javascript">

editcomment = function (id)
{
	wcmModal.showAjaxButtons('Edit', wcmBaseURL + 'business/modules/modalbox/contribution_edit.php', {id: id}, editcommentCallback, 	[wcmModal.getButtonByName("CANCEL"), 
  		 wcmModal.getButtonByName("SAVE")]); 
}
editcommentCallback = function(name)
{
	switch(name){
            case "SAVE":
				wcmModal.showAjaxButtons('Edit', wcmBaseURL + 'business/modules/modalbox/contribution_edit.php', {id: $('comment_id').value, action: 'save', contribution_text: $('contribution_text').value}, updateUICallback, 	[wcmModal.getButtonByName("OK")] 
  		 		);
  		 	break;
  	}
}			
updateUICallback = function()
{
	var uivalue = "";
	if($('contribution_title').value != "")
		uivalue += $('contribution_title').value +": ";
	
	uivalue += $('contribution_text').value;
	
	$('title_text_'+$('comment_id').value).innerHTML = uivalue; 
}

var idtodelete = -1;

deletecomment = function (id)
{
	idtodelete = id;
	wcmModal.confirm($I18N.DELETE, $I18N.CONFIRM_DELETE_CURRENT_OBJECT, deletecallback);

}
deletecallback = function (name)
{
	switch(name){
            case "YES":
				wcmModal.showAjaxButtons($I18N.DELETE, wcmBaseURL + 'business/modules/modalbox/contribution_edit.php', {id: idtodelete, action: 'delete'}, deletecallback, 	[wcmModal.getButtonByName("OK")] 
  		 		);
  		 	break;
  		 	case "OK":
  		 		if($('comment_id_remove').value)
  		 		{
  		 			$('contribution_'+$('comment_id_remove').value).remove();
  		 		}
  		 	break;
  	}
}

var showmore =0;
var limit = <?php echo $params["limit"];?>;

modifyview = function (state)
{
	showmore = 0;
	new Ajax.Updater(
		'results',
		'<?php echo $config['wcm.backOffice.url'];?>/business/ajax/biz.contributionviews.php',
		{
		   parameters: 
		   {
		   	state: state,
		   	className:'<?php echo get_class($bizobject);?>',
		   	objectId: <?php echo $bizobject->id;?>
		   } 
		 }
	); 
}

addtoview = function (state)
{
	showmore++;

	new Ajax.Request(
		'<?php echo $config['wcm.backOffice.url'];?>/business/ajax/biz.contributionviews.php',
		{
		   parameters: 
		   {
		   	state: state,
		   	className:'<?php echo get_class($bizobject);?>',
		   	from: (showmore*limit),
		   	limit: limit,
		   	objectId: <?php echo $bizobject->id;?>,
		   	showmore:1
		   },
		   onSuccess: 
		  		function(transport)
		  		{
		  			
		  			var dd = new Element('div');
		  			dd.innerHTML = transport.responseText;
		  			var elem = dd.down('tr');
		  			
		  			var lines = new Array();
		  			var counter = 0;
		  			
		  			while(elem = elem.next())
		  			{
		  				lines[counter] = elem;
		  				counter++;
		  			}
		  			for(i=0; i< counter; i++)
		  			{
		  				$('comments').insert(lines[i]);
		  			}
		  			
		  		} 
		 }
	); 
}

</script>