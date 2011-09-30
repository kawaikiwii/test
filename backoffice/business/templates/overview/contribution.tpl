{assign var="dateTimeFormat" value='_DATE_TIME_FORMAT'|constant}

<div id="_mainContent">
    <h3>{$bizobject.text|truncate:45:"..."}</h3>

    <h4>
        {if $bizobject.referent.title}
            {'_WEB_COMMENT_ON'|constant} <em>{$bizobject.referent.title|truncate:45:"..."}</em>
        {else}
            <strong>{'_BIZ_CONTRIBUTION_ORPHAN'|constant }</strong>
        {/if}
        <br/>
        {'_BIZ_BY'|constant} {$bizobject.nickname}
    </h4>
        
    <div class="info">
        <h4>{'_BIZ_TEXT'|constant}</h4>
        {$bizobject.text}
    </div>

    <div class="info">
        <h4>{'_BIZ_CONTRIBUTIONS'|constant}</h4>
        <ul>
        {foreach item=comment from=$bizobject.comments}
            <li> {$comment.title} {$comment.text} </li>
        {/foreach}
        </ul>
    </div>

</div>
<div id="_infoContent">
    
    {wcm name="include_template" file="overview/blocks/tme.tpl"}
    {wcm name="include_template" file="overview/blocks/tags.tpl"}
    {wcm name="include_template" file="overview/blocks/related.tpl"}


</div>
