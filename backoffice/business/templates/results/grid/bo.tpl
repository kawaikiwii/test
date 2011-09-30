<div class="options">
        <div class="pagination">
        		{if !empty($pages)}
				<a href="javascript:relationSearch.selectPage('{$uid}', 1)"><<</a>
                {/if}
                
                {if $currentPage neq $previousPage}
                  <a href="#" onclick="relationSearch.selectPage('{$uid}', {$previousPage}); return false;"><</a>
                {else}
                  <span><</span>
                {/if}

            {foreach from=$pages item="page" name=foo}
                {if $page eq $currentPage}
                  <a href="javascript:relationSearch.selectPage('{$uid}', {$page})" class="selected">{$page}</a>
                {else}
                  <a href="javascript:relationSearch.selectPage('{$uid}', {$page})">{$page}</a>
                {/if}
        	{/foreach}

                {if $currentPage neq $nextPage}
                  <a href="javascript:relationSearch.selectPage('{$uid}',{$nextPage})">></a>
                {else}
                  <span>></span><span>
                {/if}

				{if !empty($totalPages)}
				<a href="javascript:relationSearch.selectPage('{$uid}', {$totalPages})">>></a>
				{/if}
				
                ({$totalResults})

        </div>
</div>

<div id="{$idPrefix}resultset" class="resultset">

<ul>
{foreach from=$resultSet item=bo}
{assign var='className' value=$bo->getClass()}
{wcm name='include_template' file="results/grid/$className.tpl" fallback='results/grid/main.tpl'}
{/foreach}
</ul>
</div>
<div class="options">
	<div class="pagination">
		{if !empty($pages)}
			<a href="javascript:relationSearch.selectPage('{$uid}', 1)"><<</a>
	    {/if}

		{if $currentPage neq $previousPage}
		  <a href="#" onclick="relationSearch.selectPage('{$uid}', {$previousPage}); return false;"><</a>
		{else}
		  <span><</span>
		{/if}
		
	    {foreach from=$pages item="page"}
    		{if $page eq $currentPage}
    		  <a href="javascript:relationSearch.selectPage('{$uid}', {$page})" class="selected">{$page}</a>
    		{else}
    		  <a href="javascript:relationSearch.selectPage('{$uid}', {$page})">{$page}</a>
    		{/if}
        {/foreach}

		{if $currentPage neq $nextPage}
		  <a href="javascript:relationSearch.selectPage('{$uid}',{$nextPage})">></a>
		{else}
		  <span>></span><span>
		{/if}
		
		{if !empty($totalPages)}
			<a href="javascript:relationSearch.selectPage('{$uid}', {$totalPages})">>></a>
		{/if}
		
		({$totalResults})
		
	</div>
</div>
