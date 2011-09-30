<script type="text/javascript">
tmeSimilarsCallback = function(response, message)
{
	
	if (response == 'ADD') 
	{
		targetid = message.parameters.kind;
		lmng = relationSearch.linkMgr.get(message.parameters.uid);
		
		elem = $('_tmeSuggestions').down();
		if(elem.down().down().checked)
		{
				val = (elem.down().down().value).split('_');
				lmng.addRelationManual(val[1], val[0]);
		}
					
		while(elem = elem.next())
		{
			if(elem.down().down().checked)
			{
				val = (elem.down().down().value).split('_');
				lmng.addRelationManual(val[1], val[0]);
			}
			
		}
	}
	else if (response == 'REPLACE')
	{
		targetid = message.parameters.kind;
		lmng = relationSearch.linkMgr.get(message.parameters.uid);
		
		//remove all existing relations
		lmng.removeRelations();
		elem = $('_tmeSuggestions').down();
		if(elem.down().down().checked)
		{
				val = (elem.down().down().value).split('_');
				lmng.addRelationManual(val[1], val[0]);
		}
					
		while(elem = elem.next())
		{
			if(elem.down().down().checked)
			{
				val = (elem.down().down().value).split('_');
				lmng.addRelationManual(val[1], val[0]);
			}
			
		}

	}
	
}

</script>
