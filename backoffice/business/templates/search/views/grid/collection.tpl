{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}


	<div class="preview bundle">

        <h1 class="{$objectClass} info" title="{$object->title}">{$object->title|truncate:50:"..."}</h1>

        {assign var="documents" value=$object->getDocuments()}
        {assign var="documentCount" 	value=$documents|@count}
        {assign var="documentCountMax" 	value=7}

        {assign var="postfix" value='_BIZ_DOCUMENT'|constant}
        {* Add the 's' at the end of the postifx if necessary *}
        {if $documentCount > 1}
            {assign var="postfix" value="`$postfix`s"}
        {/if}

        <ul>
        {foreach name="loop" item="document" from="$documents"}		
            {if $smarty.foreach.loop.index < $documentCountMax} 
            <li class="{$document.class}">{$document.title|truncate:45:"..."}</li>
            {elseif $smarty.foreach.loop.index == $documentCountMax}
            {assign var="moreDocuments" value="[...]"}
            {else}
            {* Do nothing... *}
            {/if}
        {/foreach}
        </ul>	
        <p>{$documentCount} {$postfix} {$moreDocuments}</p>

    </div>

