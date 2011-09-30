{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}
{assign var="dateTimeFormat" value='_DATE_TIME_FORMAT'|constant}

<div class="row">
    
    <div class="toolbar">
        {* include the toolbar *}
        {wcm name="include_template" file="search/views/detailed/toolbar.tpl"}
    </div>

    <div class="details {$objectClass}">
        <ul>
            <li class="detail title">{$object->title}</li>
            <li class="detail"><span class="label">{'_BIZ_SOURCE'|constant}</span> {$object->source}</li>
            <li class="detail"><span class="label">{'_BIZ_RELEASEDATETIME'|constant|capitalize}</span> {$object->releaseDateTime|date_format:$dateTimeFormat}</li>

            <li class="detail">{$object->text|truncate:1024:"..."}</li>

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
