    {assign var="objectClass" value=$object->getClass()}
    {assign var="itemSelector" value="`$objectClass`_`$object->id`"}
    
    {assign var="prefix" value='_WEB_COMMENT_ON'|constant }
    {assign var="referent" value=$object->getReferent() }
    
    {if $referent}
        {assign var="referentTitle" value=$referent->title }
        {assign var="referentClass" value=$referent->getClass() }
    {else}
        {assign var="referentTitle" value='_BIZ_CONTRIBUTION_ORPHAN'|constant }
        {assign var="referentClass" value='_BIZ_CONTRIBUTION_NO_REFERENT'|constant }
    {/if}
    
    {assign var="fullText" value="`$object->title` `$object->text`"}
    
        <div class="preview textual">
        
            <h1 class="{$objectClass} info" title="{$fullText}">&laquo; {$fullText|truncate:120:"..."} &raquo;</h1>
            <p>{'_BIZ_BY'|constant} {$object->nickname}</p>
            <p>[{$referentClass|capitalize}] <strong>{$referentTitle|truncate:45:"..."}</strong><p>
    
        </div>
