{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}

<div class="row">
    
    <div class="toolbar">
        {* include the toolbar *}
        {wcm name="include_template" file="search/views/detailed/toolbar.tpl"}
    </div>

    <div class="details {$objectClass}">
        <ul>
            {assign var="pollKinds" value=$object->getKindList() }
            {assign var="kind" value=$object->kind }
            <li class="detail title" >{$object->title} ({$pollKinds.$kind})</li>
            <li class="detail">{$object->text}</li>
            <li class="detail">{$object->getVoteCount()} {'_BIZ_VOTES'|constant|lower}</li>
            <li class="detail">
                {* @todo JFG Render graph of results @see dashboard code... *}
                <ul>
                {assign var="choices" value=$object->getPollChoices()}
                {foreach item="choice" from="$choices"}
                    {* @todo Add some kind of rollover to see the a bigger picture*}
                    <li>
                        {$choice->text|truncate:45:"..."}: {$choice->voteCount} {if $object->getVoteCount() > 0}({math equation="(x / y) * 100" x=$choice->voteCount y=$object->getVoteCount() format="%d"}%){/if}
                    </li>
                {/foreach}
                <ul>
            </li>
            
        </ul>
    </div>
    
    <div class="metadata">
        {* include generic metadata *}
        {wcm name="include_template" file="search/views/detailed/metadata.tpl"}
    </div>
</div>
