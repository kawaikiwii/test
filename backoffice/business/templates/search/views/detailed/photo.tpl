{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}

{assign var="originalInfos" value=$object->getInfosByFormat('original')}

<div class="row">
    
    <div class="toolbar">
        {* include the toolbar *}
        {wcm name="include_template" file="search/views/detailed/toolbar.tpl"}
    </div>

    <div class="details {$objectClass}">
        <ul>
            <li class="detail photo"><img src="{$object->thumbnail}"></img></li>
            <li class="detail title">{$object->title}</li>
            <li class="detail"><span class="label">{'_BIZ_CAPTION'|constant}</span> {$object->caption}</li>
            <li class="detail"><span class="label">{'_BIZ_CREDITS'|constant}</span> {$object->credits}</li>
            <li class="detail"><span class="label">{'_BIZ_ORIGINAL'|constant|capitalize} {'_BIZ_FILE'|constant|lower}</span> {$object->original}</li>
            <li class="detail"><span class="label">{'_BIZ_SIZE'|constant|capitalize}</span> {$originalInfos.height}x{$originalInfos.width}</li>


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
