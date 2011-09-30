<div id="{$idPrefix}resultset" class="resultset">
<ul>
{foreach from=$resultSet item=bo}
{assign var='className' value=$bo->getClass()}
{wcm name='include_template' file="results/quickSearch/$className.tpl" fallback='results/quickSearch/main.tpl'}
{/foreach}
</ul>
</div>
<div class="options">
    <div class="pagination">
    
       
        {if $currentPage neq $previousPage}
          <a href="#" onclick="relationSearch.selectPage('{$uid}', {$previousPage}); return false;">{'_PREVIOUS'|constant}</a>
        {else}
          <span>{'_PREVIOUS'|constant}</span>
        {/if}
        
        {foreach from=$pages item=page}
            {if $page eq $currentPage}
              <a href="javascript:relationSearch.selectPage('{$uid}', {$page})" class="selected">{$page}</a>
            {else}
              <a href="javascript:relationSearch.selectPage('{$uid}', {$page})">{$page}</a>
            {/if}
        {/foreach}
        
        
        {if $currentPage neq $nextPage}
          <a href="javascript:relationSearch.selectPage('{$uid}',{$nextPage})">{'_NEXT'|constant}</a>
        {else}
          <span>{'_NEXT'|constant}</span>
        {/if}
        
        <span class="results">({$totalResults} {'_BIZ_RESULTS'|constant})</span>
        
        
    </div>
</div>
