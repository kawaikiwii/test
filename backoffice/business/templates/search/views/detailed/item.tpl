{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}

<div class="row">
    
    <div class="toolbar">
        {* include the toolbar *}
        {wcm name="include_template" file="search/views/detailed/toolbar.tpl"}
    </div>

    <div class="details {$objectClass}">
        <ul>
            <li class="detail title">{$object->title} ({$object->type|capitalize})</li>
            <li class="detail"><span class="label">{'_BIZ_LOCATION'|constant}</span> {$object->location}</li>
            <li class="detail"><span class="label">{'_BIZ_ACCESS_TITLE'|constant}</span>
                {if 'item::ACCESS_PUBLIC'|constant == $object->access}
                    {'_BIZ_ACCESS_PUBLIC'|constant}
                {elseif 'item::ACCESS_PROTECTED'|constant == $object->access}
                    {'_BIZ_ACCESS_PROTECTED'|constant}
                {elseif 'item::ACCESS_PRIVATE'|constant == $object->access}
                    {'_BIZ_ACCESS_PRIVATE'|constant}
                {else}
                    {'_BIZ_UNDETERMINED'|constant}
                {/if}
            </li>
            <li class="detail"><span class="label">{'_BIZ_DESCRIPTION'|constant}</span> {$object->description}</li>
            <li class="detail"><span class="label">{'_BIZ_TEXT'|constant}</span> {$object->text}</li>
            
            {wcm name="include_template" file="search/views/detailed/details/comments.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/permalinks.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/photos.tpl"}
        </ul>

    </div>
    
    <div class="metadata">
        {* include generic metadata *}
        {wcm name="include_template" file="search/views/detailed/metadata.tpl"}
    </div>
</div>
