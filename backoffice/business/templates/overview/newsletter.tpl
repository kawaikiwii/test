<div id="_mainContent">
    <h3>{if $bizobject.title != ''}{$bizobject.title}{else}&lt;{'_BIZ_NEW'|constant}&gt;{/if}</h3>
    <h4>{$bizobject.description}</h4>
    
    <div class="info">
        <h4>{'_BIZ_NEWSLETTER_SUBSCRIBERS'|constant}</h4>
	        {assign var="subscriptions" value=$bizobject.subscriptions}
	        {assign var="subscriptionsCount" value=$subscriptions|@count}
	        {if $subscriptionsCount > 1}
	            {assign var="postfix" value='_BIZ_SUBSCRIPTIONS'|constant'}
	        {else}
	            {assign var="postfix" value='_BIZ_SUBSCRIPTION'|constant'}
	        {/if}

            {$subscriptionsCount} {$postfix|lower}
            <br/><br/>
        <h4>{'_BIZ_CONTENT'|constant}</h4>
	    <ul>
	    {foreach from=$bizobject.composedOf item=i }
	        <li>
	           <span class="{$i.classname} {if $i.type}{$i.type}{/if}" >&nbsp;&nbsp;&nbsp;&nbsp;</span>
	           <a href="?_wcmAction=business/{$i.classname}&id={$i.id}">{$i.title}</a>
	        </li>
	    {/foreach}
	    </ul>
    </div>
    
</div>
