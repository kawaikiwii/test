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

            {assign var="documents" value=$object->getDocuments()}
            {assign var="documentCount"     value=$documents|@count}
            {assign var="documentCountMax"  value=5}
            {assign var="postfix" value='_BIZ_DOCUMENT'|constant}

            {* Add the 's' at the end of the postifx if necessary *}
            {if $documentCount > 1}
                {assign var="postfix" value="`$postfix`s"}
            {/if}

            <li class="detail">{$documentCount} {$postfix}</li>
            <li class="detail">
                <ul>
                    {foreach name="loop" item="document" from="$documents"}     
                        {if $smarty.foreach.loop.index < $documentCountMax} 
                            <li>({$document.destinationClass|capitalize}) <em>{$document.title|truncate:45:"..."}</em></li>
                        {elseif $smarty.foreach.loop.index == $documentCountMax}
                            <li>[...]</li>
                        {else}
                            {* Do nothing... *}
                        {/if}
                    {/foreach}
                </ul>   
            </li>

            {wcm name="include_template" file="search/views/detailed/details/comments.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/permalinks.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/photos.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/iptcCategories.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/tags.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/tme.tpl"}
        </ul>

    </div>
    
    <div class="metadata">
        {* include generic metadata *}
        {wcm name="include_template" file="search/views/detailed/metadata.tpl"}
    </div>
</div>
