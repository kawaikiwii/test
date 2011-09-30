{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}

<div class="row">
    
    <div class="toolbar">
        {* include the toolbar *}
        {wcm name="include_template" file="search/views/detailed/toolbar.tpl"}
    </div>

    <div class="details {$objectClass}">
        <ul>
            <li class="detail title">{$object->title}</li>
            <li class="detail"><span class="label">{'_BIZ_DESCRIPTION'|constant}</span> {$object->description}</li>
            <li class="detail"><span class="label">{'_BIZ_NEWSLETTER_MSG_TEMPLATE_USED'|constant}</span>
                <ul>
                    <li><span class="label">{'_BIZ_HTML_TEMPLATE'|constant}</span> {$object->htmlTemplate}</li>
                    <li><span class="label">{'_BIZ_TEXT_TEMPLATE'|constant}</span> {$object->textTemplate}</li>
                </ul>
            </li>

            {assign var="subscriptions" value=$object->getSubscriptions()}
            {assign var="subscriptionsCount" value=$subscriptions|@count}
            {if $subscriptionsCount > 1}
                {assign var="postfix" value='_BIZ_SUBSCRIPTIONS'|constant'}
            {else}
                {assign var="postfix" value='_BIZ_SUBSCRIPTION'|constant'}
            {/if}
            <li class="detail">{$subscriptionsCount} {$postfix|lower}</li>


            {assign var="documents" value=$object->getAssoc_composedOf(false)}
            {assign var="documentCount" value=$documents|@count}
            {assign var="documentCountMax"  value=5}
        
            {assign var="postfix" value='_BIZ_DOCUMENT'|constant}
            {* Add the 's' at the end of the postifx if necessary *}
            {if $documentCount > 1}
                {assign var="postfix" value="`$postfix`s"}
            {/if}

            <li class="detail">{$documentCount} {$postfix}</li>
            <li class="detail">
                <ul>
                    {foreach name="loop" item="document" from="$documents" }     
                        {if $smarty.foreach.loop.index < $documentCountMax} 
                            {* @todo JFG Add the object type (as with the collection). Can I get the class from the assoc_array? *}
                            <li><em>{$document.title|truncate:45:"..."}</em></li>
                        {elseif $smarty.foreach.loop.index == $documentCountMax}
                            <li>...</li>
                        {else}
                            {* Do nothing... *}
                        {/if}
                    {/foreach}
                </ul>   
            </li>
        </ul>
    </div>
    
    <div class="metadata">
        {* include generic metadata *}
        {wcm name="include_template" file="search/views/detailed/metadata.tpl"}
    </div>
</div>
