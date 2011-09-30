{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}

    <div class="preview bundle">

        <h1 class="{$objectClass}">{$object->title}</h1>

        {assign var="documents" value=$object->getAssoc_related(false)}
        {assign var="documentCount" value=$documents|@count}
        {assign var="documentCountMax"  value=5}
		
        {assign var="postfix" value='_BIZ_DOCUMENT'|constant}
        {* Add the 's' at the end of the postifx if necessary *}
        {if $documentCount > 1}
            {assign var="postfix" value="`$postfix`s"}
        {/if}

        <p class="info" title="{$object->description|strip_tags}">{$object->description|strip_tags|truncate:160:"..."}</p>

        <ul>
            {assign var="subscriptions" value=$object->getSubscriptions()}
            {assign var="subscriptionsCount" value=$subscriptions|@count}
            {if $subscriptionsCount > 1}
            {assign var="postfix" value='_BIZ_SUBSCRIPTIONS'|constant'}
            {else}
            {assign var="postfix" value='_BIZ_SUBSCRIPTION'|constant'}
            {/if}
            <li class="details">{$subscriptionsCount} {$postfix|lower}</li>
            {assign var="documents" value=$object->getAssoc_composedOf(false)}
            {assign var="documentCount" value=$documents|@count}
            {assign var="documentCountMax"  value=5}
            {assign var="postfix" value='_BIZ_DOCUMENT'|constant}
            {* Add the 's' at the end of the postifx if necessary *}
            {if $documentCount > 1}
            {assign var="postfix" value="`$postfix`s"}
            {/if}
            <li class="details">{$documentCount} {$postfix}</li>
        </ul>
    </div>
    
