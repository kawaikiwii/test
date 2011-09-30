<script type="text/javascript">

removel = function(){
	   if(arguments[1]>0){
	     var _temp=arguments[0].splice(0,arguments[1]);
	     arguments[0].shift();
	     while(_temp.length>0){
	        arguments[0].unshift(_temp.pop());
	     }
	   }
	   else{
	     arguments[0].shift();
	   }
	 };
	 
	 
build_name = function()
{
	if ($('firstname') && $('lastname')) {
		$firstname = $('firstname').value.charAt(0).toUpperCase() + $('firstname').value.substring(1);
		$('name').value = $firstname + "|" + $('lastname').value.toUpperCase();
		return true;
	} else {
		return false;
	}
}
	
populeServices = function()
{
    wcmBizAjaxController.call("biz.permissions", {
	command: 'populeServices',
	divId: 'selectService',
	univers: $('univers').options[$('univers').selectedIndex].value,
	service: $('services').options[$('services').selectedIndex].value
	},
	null,{onComplete: setRubriqueController}
	);
}

setRubriqueController = function()
{
    wcmBizAjaxController.call("biz.permissions", {
	command: 'setRubriqueController',
	divId: 'selectRubrique',
	univers: $('univers').options[$('univers').selectedIndex].value,
	service: $('services').options[$('services').selectedIndex].value
    });
}

addAccountPermission = function()
{
	if ($('selectionZone').value != "2")
	{
		var univers = $('univers').options[$('univers').selectedIndex].value;
		var univers_label = $('univers').options[$('univers').selectedIndex].text;
		var service = $('services').options[$('services').selectedIndex].value;
		var service_label = $('services').options[$('services').selectedIndex].text;
		//var rubrique = $('rubrique').value;
		var  valeur = document.account_permissions["channelIds[]"];
		
		// remove existing permission from current univers and service
		if (univers != "" && service !="")
		{
			var  permissionsVals = "";
			var  trat = "";
			var  trat2 = "";
			var  finaltrat = "";
			permissionsVals = $('account_permissions_values').value;

			trat = permissionsVals.split("##");
			if (service == '*')
			{
				//alert('###trat1 : '+trat);	
				// check array value to delete
				for (var i=0;i < trat.length; i++)
			    {
					//alert(trat[i]);
					trat2 = trat[i].split("||");
					if (univers == trat2[0])
					{
						trat[i] = "";
					}
			    }		
				//alert('trat2 : '+trat);
			}
			else
			{		
				for (var i=0;i < trat.length; i++)
			    {
					trat2 = trat[i].split("||");
					//alert(univers + '|' + trat2[0] + ' / ' + service + '|' + trat2[1]);
					if (univers == trat2[0] && ((service == trat2[1]) || (trat2[1] == '*')) )
					{
						//removel(trat,i);
						trat[i] = "";
					}
			    }
			}
			
			//alert('trat : '+trat);			
			var finalString = trat.toString();
			finalString = finalString.replace(/,/g, "##");		
			//alert('finalString : '+finalString);		
			$('account_permissions_values').setValue(finalString);
		}
		
		var rubriqueId = '';
		var rubrique_label = '';
		var parameters = {
		    	prefix: valeur
	    	};
		
		var value = "";
		var label = "";
		var permissionsValues = "";
		
		if ( $('allChannelIds') == null || ((typeof($('allChannelIds')) != 'undefined') && $('allChannelIds').checked==true) )
		{
			rubriqueId = '*';
			rubrique_label = '*';
			//alert($('allChannelIds').value);
		}
		
		if (typeof(valeur)=='undefined' || service == '*')
		{
			rubriqueId = '*';
			rubrique_label = '*';
			value = univers+'||'+service+'||'+rubriqueId;
			label = univers_label+' \\ '+service_label+' \\ '+rubrique_label;
			permissionsValues = $('account_permissions_values').value;
			if (permissionsValues != '') permissionsValues += '##';
				permissionsValues += value;
			$('account_permissions_values').setValue(permissionsValues);
			$('permissions_list').insert('<li style="clear:both" ><b><em>nouvelles permissions :</em></b></li>');	
			//$('permissions_list').insert('<li style="clear:both" id='+value+'><a href="#" onclick="delAccountPermission($(this).up()); return false;"><span>' + $I18N.DELETE + '</span></a> <em>'+label+'</em></li>');	
			$('permissions_list').insert('<li style="clear:both" id='+value+'> <em>'+label+'</em></li>');	
		}
		else if (valeur && valeur!='' && rubriqueId=='')
		{
			var tmp="";
			var idLabel = "";
			
			$('permissions_list').insert('<li style="clear:both" ><b><em>nouvelles permissions :</em></b></li>');	
			for (var i=0;i < valeur.length; i++)
		    {    
		    	if ( valeur[i].checked )
		        {    
		    		//tmp+=" "+valeur[i].value; 
		    		value = univers+'||'+service+'||'+valeur[i].value;  	    		
		    		idLabel = "0" + valeur[i].id;
		    		label = univers_label+' \\ '+service_label+' \\ '+ document.getElementById(idLabel).innerHTML;
		    		
		    		permissionsValues = $('account_permissions_values').value;
		    		if (permissionsValues != '') permissionsValues += '##';
		    			permissionsValues += value;
		    		$('account_permissions_values').setValue(permissionsValues);
		    		//$('permissions_list').insert('<li style="clear:both" id='+value+'><a href="#" onclick="delAccountPermission($(this).up()); return false;"><span>' + $I18N.DELETE + '</span></a> <em>'+label+'</em></li>');	
		    		$('permissions_list').insert('<li style="clear:both" id='+value+'> <em>'+label+'</em></li>');	
			    }
		    }
		    //alert("Vous avez choisi: "+tmp);
		}
		else
		{
			value = univers+'||'+service+'||'+rubriqueId;
			label = univers_label+' \\ '+service_label+' \\ '+rubrique_label;
			permissionsValues = $('account_permissions_values').value;
			if (permissionsValues != '') permissionsValues += '##';
				permissionsValues += value;
			$('account_permissions_values').setValue(permissionsValues);
			//$('permissions_list').insert('<li style="clear:both" id='+value+'><a href="#" onclick="delAccountPermission($(this).up()); return false;"><span>' + $I18N.DELETE + '</span></a> <em>'+label+'</em></li>');	
			$('permissions_list').insert('<li style="clear:both" id='+value+'> <em>'+label+'</em></li>');	
		}
	    
		/*
		var value = univers+'||'+service+'||'+rubriqueId;
		var label = univers_label+' \\ '+service_label+' \\ '+rubrique_label;
		var permissionsValues = $('account_permissions_values').value;
	    if (permissionsValues != '') permissionsValues += '##';
		permissionsValues += value;
		$('account_permissions_values').setValue(permissionsValues);
		$('permissions_list').insert('<li style="clear:both" id='+value+'><a href="#" onclick="delAccountPermission($(this).up()); return false;"><span>' + $I18N.DELETE + '</span></a> <em>'+label+'</em></li>');
		//alert('U:'+univers+'\nS:'+service+"\nR:"+rubrique+"\nvalue:"+value+"\nlabel:"+label+"\naccount_permissions_values:"+permissionsValues);
		*/
	}
}

delAccountPermission = function(obj)
{
	//alert(obj.id);
    // rebuild value
    var value = '';
    var siblings = obj.siblings();
    if (siblings) siblings.each(function(li) { if (value != '') value += '##'; value += li.id; });
    
    //alert(value);   
    $('account_permissions_values').setValue(value);
    
    // remove tab
    obj.remove();
}

saveAccountPermissions = function(accountId,permissionString)
{
	wcmBizAjaxController.call("biz.permissions", {
	command: 'setPermissions',
	accountId: accountId,
	permissionString: permissionString
    });
}


loadUserPermissions = function(userName)
{
    wcmBizAjaxController.call("biz.permissions", {
	command: 'loadUserPermissions',
	userName: userName
	},
	null,{onComplete: updateHiddenField}
	);
}

loadUserIdPermissions = function(userId)
{
    wcmBizAjaxController.call("biz.permissions", {
	command: 'loadUserIdPermissions',
	userId: userId
	},
	null
	);
}
updateHiddenField = function()
{
    var value = '';
    var li_list = $('permissions_list').childElements();
    if (li_list) li_list.each(function(li) { if (value != '') value += '##'; value += li.id; });
    $('account_permissions_values').setValue(value);
}

saveBulkPermissions = function(accountId,permissionString,overWrite)
{
    wcmBizAjaxController.call("biz.permissions", {
	command: 'saveBulkPermissions',
	accountId: accountId,
	permissionString: permissionString,
	overWrite: overWrite
    });
}

checkBeforeChangingUniverse = function()
{
	var univers = $('univers').options[$('univers').selectedIndex].value;
	var service = $('services').options[$('services').selectedIndex].value;
	var checkCheckboxes = $('checkCheckboxes').value;
	
	if (service != '' && checkCheckboxes == 1)
    {
		if (confirm($I18N.CHECKBOX_CONTROL))
    	{
    		$('olduniverse').value = $('univers').selectedIndex;
    		populeServices();
    		$('checkCheckboxes').value = 0;
    	}
    	else
    	{
    		$('univers').selectedIndex = $('olduniverse').value;
    	}
    }
    else
    {
    	$('olduniverse').value = $('univers').selectedIndex;
    	populeServices();
    }
}

checkBeforeChangingService = function()
{
	var service = $('services').options[$('services').selectedIndex].value;
	var checkCheckboxes = $('checkCheckboxes').value;
	
	if (service != '' && checkCheckboxes == 1)
    {
		if (confirm($I18N.CHECKBOX_CONTROL))
    	{
    		$('oldservice').value = $('services').selectedIndex;
    		setRubriqueController();
    		$('checkCheckboxes').value = 0;
        }
    	else
    	{
    		$('services').selectedIndex = $('oldservice').value;
    	}
    }
    else
    {
    	$('oldservice').value = $('services').selectedIndex;
    	setRubriqueController();
    }
}


</script>
