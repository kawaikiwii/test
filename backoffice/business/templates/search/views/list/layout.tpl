{wcm name="include_template" file="search/views/resultset-info.tpl"}


{if $searchContext->numItemsFound gt 0}
	
	{assign var="searchContextQuery" value=$searchContext->query}
	{php}
		$contextQuery = $this->get_template_vars('searchContextQuery');

		if (!preg_match('`classname:(.)+$`i', $contextQuery)) { $this->assign('globalSearch', true); }
		else { $this->assign('globalSearch', false); }
	{/php}

	
    <div id="resultset">
		
		{if $globalSearch eq true}
			
			{include file="search/views/list/layoutGlobalSearch.tpl"}
		
		{else}
		
	        {include file="search/views/list/layoutDefault.tpl"}
	       
		{/if}
	       
    </div>

{wcm name="include_template" file="search/views/resultset-navigation.tpl"}
{/if}
