<script type="text/javascript">
toggleQueryBuilder = function (element, target)
{
    $(element).up('div').down('fieldset.' + target).toggle();
}
/**
 * BuildQuery will call biz.getquerycontent to get the content of the channel according to the 
 * new query. It will first serialize the forced content and sends it as a parameter so we can
 * embed the forced content with the resutls of the query
 *
 */
buildQuery = function()
{
	//serialize the forced content
	
	rank = 1;
	forcedcontent = "[";	
	elem = $('<?php echo $prefix;?>list').down();
	if(elem.down('.relproperties').down().value != 0)
	{
		ser = '{"rank": "'+rank+'", "value":"'+elem.id+'"}';
		forcedcontent += ((forcedcontent=='[')?'':',') + ser;
	}
	while(elem = elem.next())
	{
		rank++;
		if(elem.down('.relproperties').down().value != 0)
		{
			ser = '{"rank": "'+rank+'", "value":"'+elem.id+'"}';
			forcedcontent += ((forcedcontent=='[')?'':',') + ser;
		}
	}
	forcedcontent += "]";

	
	new Ajax.Request('<?php echo $config['wcm.backOffice.url'];?>/business/ajax/biz.getquerycontent.php', 
	{   method: 'get',  
		parameters:  { channel_id: <?php echo $bizobject->id;?> , prefix: '<?php echo $prefix;?>',
			  		   query: $('newquery').value, orderBy: $('orderBy').value, limit: $('limit').value, forcedcontent: forcedcontent},
		onSuccess: function(transport) { 
			$('<?php echo $prefix;?>relations').innerHTML = transport.responseText;
			lmng = relationSearch.linkMgr.get('channelRelations');
			lmng.initSortableRelations(); 
		}
		
	}); 
}

savedSearchload = function ()
{
	var query = $('savedQuery').options[$('savedQuery').selectedIndex].value;
	
	if(query != 0)
		$('newquery').value = query;

}
</script>