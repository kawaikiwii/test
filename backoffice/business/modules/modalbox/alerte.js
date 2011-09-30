<script type="text/javascript">

manageAlerte = function(command,loginAs,alerteId,formDatas, perimeter)
{
	if (command == "add") {
		formDatas = "";
		tabElem = document.forms.alerteForm.getElementsByTagName("*");
		for (var i=0; i<tabElem.length; i++) {
			if (tabElem[i].type == "text" || tabElem[i].type == "hidden") {
				if (formDatas != "") 
					formDatas += "&";
				formDatas += tabElem[i].id + "=" + encodeURIComponent(tabElem[i].value);
			}
			if (tabElem[i].type == "select-one") {
				if (formDatas != "") 
					formDatas += "&";
				formDatas += tabElem[i].id + "=" + encodeURIComponent(tabElem[i].options[tabElem[i].selectedIndex].value);
			}
			if (tabElem[i].type == "radio") {
				if (tabElem[i].checked == true) {
					if (formDatas != "") 
						formDatas += "&";
					formDatas += tabElem[i].id + "=" + encodeURIComponent(tabElem[i].value);
				}
			}
		}
		var tab_perimeter = perimeter.split(',');
		var nb_univers = 0;
		var tab_univers = new Array();
		var car = '';
		var trouve = false;
		for (var i = 0; i < tab_perimeter.length; i++) {
			trouve = false;
			car = tab_perimeter[i].charAt(0);
			if (i == 0) {
				trouve = true;
				tab_univers.push(car);
				nb_univers++;
			}
			
			for (var j = 0; j < tab_univers.length; j++) {
				if (car == tab_univers[j]) 
					trouve = true;
			}
			if (!trouve) {
				nb_univers++;
				tab_univers.push(car);
			}
		}
		if (nb_univers > 1) {
			alert($I18N.RESTRICT_NB_UNIVERS);
			command = "refresh";
		}
		wcmBizAjaxController.call("biz.alerte", {
			command: command,
			loginAs: loginAs,
			alerteId: alerteId,
			formDatas: formDatas,
			perimeter: perimeter
		//},null,{onComplete: function() { populeServices(); }});
		});
	}
	else {
		wcmBizAjaxController.call("biz.alerte", {
			command: command,
			loginAs: loginAs,
			alerteId: alerteId,
			formDatas: formDatas,
			perimeter: perimeter
		//},null,{onComplete: function() { populeServices(); }});
		});
	}
	return command;
}

populeServices = function()
{
    wcmBizAjaxController.call("biz.permissions", {
	command: 'populeAlertServices',
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
	command: 'setAlertRubriqueController',
	divId: 'selectRubrique',
	univers: $('univers').options[$('univers').selectedIndex].value,
	service: $('services').options[$('services').selectedIndex].value
    });
}

addQuery_sr = function()
{
	var univers = $('univers').options[$('univers').selectedIndex].value;
	var service = $('services').options[$('services').selectedIndex].value;
	var service_label = $('services').options[$('services').selectedIndex].text;
	var rubrique = $('rubrique').value;
	var rubriqueId = '';
	var rubrique_label = '';
	
	var parameters = {
	    	prefix: rubrique,
	    	univers: univers,
	    };
    
	//alert(rubrique);
	//if (typeof(rubrique)=='undefined' && (rubrique != '*'))
	if (rubrique != "" && rubrique != '*')
    {
		wcmBizAjaxController.callWithoutUpdate('autocomplete/wcm.channels', parameters, null,
		    {onComplete: function(myAjaxResponse) 
			{
				if (myAjaxResponse.responseText == 'invalid')
				{
					alert('Rubrique invalide');
					return;
				}
				else
				{
					//alert(myAjaxResponse.responseText);
					var separator = myAjaxResponse.responseText.indexOf('#');
					var bizobjectId    = myAjaxResponse.responseText.substring(0,separator);
					var bizobjectLabel = myAjaxResponse.responseText.substring(separator+1);
					rubriqueId = bizobjectId;
					rubrique_label = bizobjectLabel;
				}
			}	    
		    });	
	}
	else
	{	
		rubriqueId = '*';
		rubrique_label = '*';
	}
	
	var value = service+'||'+rubriqueId;
	var label = service_label+' \\ '+rubrique_label;
	var srValues = $('query_sr').value;
    	if (srValues != '') srValues += '##';
	srValues += value;
	$('query_sr').setValue(srValues);
	$('query_sr_list').insert('<li style="clear:both" id='+value+'><a href="#" onclick="delQuery_sr($(this).up()); return false;"><span>' + $I18N.DELETE + '</span></a> <em>'+label+'</em></li>');
}

delQuery_sr = function(obj)
{
    // rebuild value
    var value = '';
    var siblings = obj.siblings();
    if (siblings) siblings.each(function(li) { if (value != '') value += '##'; value += li.id; });
    $('query_sr').setValue(value);
    
    // remove tab
    obj.remove();
}


delAllQuery_sr = function()
{
	$('query_sr').setValue('');
	var elems = $('query_sr_list').childElements();
	for(var i=0; i<elems.length; i++)
		elems[i].remove();
}

putAlertMessage = function(message)
{
	$('alertSaveMsg').innerHTML='';
	$('alertSaveMsg').innerHTML=message;
	$('alertSaveMsg').setOpacity(0);
	$('alertSaveMsg').setStyle({visibility: 'visible'});
	new Effect.Opacity('alertSaveMsg', {from: 0.0, to: 1.0, duration: 3.0});
	new Effect.Opacity('alertSaveMsg', {from: 1.0, to: 0.0, duration: 3.0});
	/*$('alertSaveMsg').appear(); 
	$('alertSaveMsg').fade({ duration: 3.0 });*/
}

previewTask = function(alerteId,loginAs)
{
	wcmBizAjaxController.call("biz.previewAlerte", {
		loginAs: loginAs,
		alerteId: alerteId
	});
}

manageAlerte('refresh',$('initAccountId').value,'','','');

</script>