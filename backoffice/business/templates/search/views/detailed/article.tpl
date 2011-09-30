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
            <li class="detail">{$object->abstract}</li>
            <li class="detail">{'_BIZ_BY'|constant|capitalize} {$object->author}</li>
            
            {assign var="pageCount" value=$object->getAssoc_chapters(false)|@count}
            {if $pageCount > 1}
                {assign var="postfix" value='_BIZ_CHAPTERS'|constant|lower}
            {else}
                {assign var="postfix" value='_BIZ_CHAPTER'|constant|lower}
            {/if}
            <li class="detail">{$pageCount} {$postfix}</li>
            
            {wcm name="include_template" file="search/views/detailed/details/comments.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/permalinks.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/photos.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/iptcCategories.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/tags.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/printPublication.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/tme.tpl"}
        </ul>
    </div>
    
    <div class="metadata">
        {* include generic metadata *}
        {wcm name="include_template" file="search/views/detailed/metadata.tpl"}
    </div>
</div>
