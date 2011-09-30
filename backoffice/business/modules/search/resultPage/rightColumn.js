<script type="text/javascript">
/* 2009.03.25 : ExportRules - BEGIN */
_wcmExportExportRule = function() {

    if (_wcmGetItem())
    {
        _wcmNoItemsSelected();
        return;
    }

    wcmModal.showOkCancel(
                $I18N.EXPORT_EXPORTRULE,
                {
                    url: wcmBaseURL + 'business/ajax/export/exportRule.php',
                    parameters : null
                },
                function(response) {
                    if (response == 'CANCEL') return;
                    // if no exportRule was chosen, do not allow save
                    if ($F('_wcmExportRuleId') == 0) 
                    {
                        alert($I18N.EXPORT_EXPORTRULE_PLEASE_CHOOSE);
                        _wcmExportExportRule();
                        return;
                    }
                    wcmModal.showOk(
                        $I18N.EXPORT_EXPORTRULE,
                        {
                            url: wcmBaseURL + 'business/ajax/export/exportRule.php',
                            parameters: {
                                response: response,
                                id: $F('_wcmExportRuleId')
                            }
                        });
                });

}
/* 2009.03.25 : ExportRules - END */

_wcmExportCollection = function() {

    if (_wcmGetItem())
    {
        _wcmNoItemsSelected();
        return;
    }

    wcmModal.showAddReplaceCancel(
                $I18N.EXPORT_COLLECTION,
                {
                    url: wcmBaseURL + 'business/ajax/export/collection.php',
                    parameters : null
                },
                function(response) {
                    if (response == 'CANCEL') return;
                    // if no collection was chosen, do not allow save
                    if (($F('_wcmCollectionName') == '' && $F('_wcmCollectionId') == 'new') || $F('_wcmCollectionId') == 0) 
                    {
                        alert($I18N.EXPORT_COLLECTION_PLEASE_CHOOSE);
                        _wcmExportCollection();
                        return;
                    }
                    wcmModal.showOk(
                        $I18N.EXPORT_COLLECTION,
                        {
                            url: wcmBaseURL + 'business/ajax/export/collection.php',
                            parameters: {
                                response: response,
                                name: $F('_wcmCollectionName'),
                                description: $F('_wcmCollectionDescription'),
                                id: $F('_wcmCollectionId')
                            }
                        });
                });

}

_wcmGetItem = function ()
{
    var inputOptions;
    inputOptions=document.getElementsByTagName("input");
    for(i=0; i < inputOptions.length;i++)
    {
        if(inputOptions[i].type=="checkbox")
        {
	        if (inputOptions[i].checked == true)
	            return false;
        }
    }
    return true;
}

_wcmUpdateExport = function(value) {
    if (value == 'new')
    {
        $('_wcmExportDiv').show();
    }
    else
    {
        $('_wcmExportDiv').hide();
    }
}

_wcmNoItemsSelected = function() {
    wcmModal.showOk($I18N.NO_ITEMS_SELECTED, $I18N.NO_ITEMS_SELECTED);
    return;
}

_wcmCreateBinSession = function(title){
	wcmModal.showAjaxButtons(title,'business/modules/modalbox/create_bin_session.php',
    	null,
    	function(response) {
    		if (response == 'SAVE') {
    			manageBin('createBinFromSession',document.getElementById('bin_name').value, 
    				document.getElementById('bin_description').value, 
    				'', '', 'bins', false);
    		}
    	},
    	[wcmModal.getButtonByName("CANCEL"), 
  		 wcmModal.getButtonByName("SAVE")]
    	);
}

_wcmUpdateBin = function(title,id){
    wcmModal.showAjaxButtons(title,'business/modules/modalbox/update_bin.php',
    	{id:id},
    	function(response) {
    		if (response == 'SAVE') {
    			manageBin('updateBin',document.getElementById('bin_name').value, 
    				document.getElementById('bin_description').value, '', 
    				document.getElementById('idBin').value, 
    				'bins', false);
    		}
    	},
    	[wcmModal.getButtonByName("CANCEL"), 
  		 wcmModal.getButtonByName("SAVE")]
    	);
}

_wcmCreateBin = function(title){
	wcmModal.showAjaxButtons(title,'business/modules/modalbox/create_empty_bin.php',
    	null,
    	function(response) {
    		if (response == 'SAVE') {
    			manageBin('createEmpty',document.getElementById('bin_name').value,
    				document.getElementById('bin_description').value, 
    				'', '', 'bins', false);
    			
    		}
    	},
    	[wcmModal.getButtonByName("CANCEL"), 
  		 wcmModal.getButtonByName("SAVE")]
    	 );
}

_wcmSaveSearch = function(title){
	wcmModal.showAjaxButtons(title,'business/modules/modalbox/create_saved_search.php',
    	null,
    	function(response) {
    		if (response == 'SAVE') {
    			var query = '';
    			if ($('search_baseQuery') != null)
    				query = $('search_baseQuery').value;
    			if ($('search_query') != null){
    				if (query != '') query = query += ' ';
    				query+=$('search_query').value;
    			}
    			 
    			manageSaveSearch('create',$('saved_name').value, 
    				$('saved_description').value, 
    				query, 
    				getSearchForm().serialize(),
    				 '', 'searches', $('dashboard').checked, $('shared').checked);
    		}
    	},
  		[wcmModal.getButtonByName("CANCEL"), 
  		 wcmModal.getButtonByName("SAVE")]
    	);
}

</script>