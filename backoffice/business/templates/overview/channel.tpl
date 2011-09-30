<div id="_mainContent">
    <h3>{$bizobject.title}</h3>
    {$bizobject.description}
     
    <div class="info">
        <h4>{'_BIZ_CONTENT'|constant}</h4>
    
        <ul>
        {foreach from=$bizobject|@wcm:content:10 item=item key=k name=loop}
            <li>
                {* bizrelation? *}
                {if $item.className eq 'wcmBizrelation'}
                    {assign var="item" value=$item.destination}
                {/if}
                <span class="{$item.className}" >&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <a href="?_wcmAction=business/{$item.className}&id={$item.id}">{$item.title}</a>
                <br/> &nbsp;
            </li>
        {/foreach}
        </ul>
    </div>
</div>
