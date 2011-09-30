{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}


    <div class="preview participative">

        {assign var="pollKinds" value=$object->getKindList() }
        {assign var="kind" value=$object->kind }

        <h1 class="info {$objectClass}" title="{$object->title}: {$object->text|strip_tags}">{$object->text|strip_tags|truncate:20:"..."}</h1>

        {assign var="choices" value=$object->getPollChoices()}
        <ul class="graph">
            {foreach item="choice" from="$choices"}
            <li style="background-position: {if $object->getVoteCount() > 0} {math equation="((x / y) * 100) * 1.5" x=$choice->voteCount y=$object->getVoteCount() format="%d"}px 0{/if};">
                <strong>{$choice->text|truncate:10:"..."}</strong> {$choice->voteCount} {if $object->getVoteCount() > 0}({math equation="(x / y) * 100" x=$choice->voteCount y=$object->getVoteCount() format="%d"}%){/if}
            </li>
            {/foreach}
        </ul>

        <p>{$object->getVoteCount()} {'_BIZ_VOTES'|constant|lower}</p>

    </div>
    
