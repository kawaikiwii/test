{assign var="objectClass" value=$object->getClass()}
{assign var="exportRule" value=$object->getAssocArray(false)}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}

<div class="preview media">
    <h1 class="{$exportRule.className} info" title="{$exportRule.title}">{$exportRule.title|truncate:20:"..."}</h1>
    <p class="photographer">
        {$exportRule.name} - {$exportRule.code}
    </p>
    
</div>