<script type="text/javascript">

	bulkupdate = function()
	{
		var action = document.getElementById('bulkAction').options[document.getElementById('bulkAction').selectedIndex].value;
		var ArrayAccountJSON = Object.toJSON(arrayAccount);
		if (action != '' && ArrayAccountJSON != '{}')
		{
			openmodal('Account','800'); 
			modalPopup('account',action, '', '', ArrayAccountJSON);
		}
	}
	
	toggleCheckboxes = function(idPrefix)
	{
	    var newIds = new Array;
	    var oldIds = new Array;
	    eval('var rExp = /' + idPrefix + '/');
	
	    $A(document.getElementsByTagName('input')).select(
	                
	        function (element) {
	            return element.type == 'checkbox' && element.id.startsWith(idPrefix);
	        }).each(
	            function (checkbox) {
	                
	                if (checkbox.checked == true)
	                {
	                    oldIds.push(checkbox.id.replace(rExp,''));
	                    checkbox.checked = false;
	                } 
	                else 
	                {
	                    checkbox.checked = true;
	                    newIds.push(checkbox.id.replace(rExp,''));
	                }
	                manageArrayAccount(checkbox.id);

	            });
	}

	var arrayAccount = {};
	
	manageArrayAccount = function(id)
	{	
		var element = document.getElementById(id);
		var command = 'remove';
		if (element.checked)
			var command = 'add';
		
		if (command == 'add')
			arrayAccount[id] = id;
		else
			delete(arrayAccount[id]);
	}
	
</script>