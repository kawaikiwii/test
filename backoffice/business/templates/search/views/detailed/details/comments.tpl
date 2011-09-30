{assign var="dateTimeFormat" value='_DATE_TIME_FORMAT'|constant}

{assign var="comments" value=$object->getAssoc_comments(false) }

{if $comments}
    {assign var=commentCount value=$comments|@count}
        <li class="detail">
            {$commentCount} {if $commentCount gt 1}{'_BIZ_CONTRIBUTIONS'|constant|lower}{else}{'_BIZ_CONTRIBUTION'|constant|lower}{/if}
            {foreach from=$comments item=comment name=loop}
                {if $smarty.foreach.loop.last}
                    - {'_BIZ_LAST_COMMENT_TIME'|constant} {$comment.createdAt|date_format:$dateTimeFormat} {'_BIZ_BY'|constant|lower} <em>{$comment.nickname}</em>
                {/if}
            {/foreach}
        </li>    
{else}
    <li class="details">{'_BIZ_NO_CONTRIBUTION'|constant}</li>
{/if}
