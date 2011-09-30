{assign var="dateTimeFormat" value='_DATE_TIME_FORMAT'|constant}

<div id="_mainContent">
    <h3>{if $bizobject.title != ''}{$bizobject.title}{else}&lt;{'_BIZ_NEW'|constant}&gt;{/if}</h3>
    
    <div class="info">
        <h4>{'_BIZ_TEXT'|constant}</h4>
        {$bizobject.text}
    </div>
</div>

<div id="_infoContent">
    <ul class="info">
        <li>
        <span class="label">{'_BIZ_SECTION'|constant}:</span>
        {if $bizobject.title != ''}
            {$bizobject.channel.title}
        {else}
            {'_BIZ_NO_DETAIL'|constant}
        {/if}
        </li>

        <li><span class="label">{'_BIZ_SOURCE'|constant}:</span>
        {if $bizobject.source != ''}
            {$bizobject.source}
        {else}
            {'_BIZ_NO_DETAIL'|constant}
        {/if}
        </li>

        <li><span class="label">{'_BIZ_RELEASEDATETIME'|constant}:</span>
        {if $bizobject.releaseDateTime != ''}
             {$bizobject.releaseDateTime|date_format:$dateTimeFormat}
        {else}
            {'_BIZ_NO_DETAIL'|constant}
        {/if}
        </li>
    </ul>
    

    {wcm name="include_template" file="overview/blocks/medias.tpl"}
    {wcm name="include_template" file="overview/blocks/iptc.tpl"}
    {wcm name="include_template" file="overview/blocks/tme.tpl"}
    {wcm name="include_template" file="overview/blocks/tags.tpl"}
    {wcm name="include_template" file="overview/blocks/related.tpl"}


</div>
