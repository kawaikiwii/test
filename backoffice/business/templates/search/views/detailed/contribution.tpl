{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}

<div class="row">
    
    <div class="toolbar">
        {* include the toolbar *}
        {wcm name="include_template" file="search/views/detailed/toolbar.tpl"}
    </div>

    <div class="details {$objectClass}">
        <ul>
			{assign var="prefix" value='_WEB_COMMENT_ON'|constant }
			{assign var="referent" value=$object->getReferent() }
			{assign var="referentTitle" value=$referent->title|truncate:45:"..." }
			{assign var="referentClass" value=$referent->getClass() }

            <li class="detail">{$object->text|truncate:1024:"..."}</li>
            <li class="detail">{$prefix}{$referentClass} <em>{$referentTitle}</em></li>
            <li class="detail">{'_BIZ_BY'|constant} {$object->nickname}</li>
            
            {wcm name="include_template" file="search/views/detailed/details/photos.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/tme.tpl"}
        </ul>

    </div>
    
    <div class="metadata">
        {* include generic metadata *}
        {wcm name="include_template" file="search/views/detailed/metadata.tpl"}
    </div>
</div>
