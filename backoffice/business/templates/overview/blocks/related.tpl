    <h4>{'_BIZ_OUTBOUND_LINKS'|constant}</h4>


    {if $bizobject.related|@count > 0}
	    <ul class="related">
	    {foreach from=$bizobject.related item=object}
	        <li><a href="?_wcmAction=business/{$object.className}&id={$object.id}">{$object.title}</a></li>
	    {/foreach}
	    </ul>
    {else}
        {'_BIZ_NO_DETAIL'|constant}    
    {/if}