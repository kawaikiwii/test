<font size="2">
    {$forecast.sourceLabel} | {$forecast.publicationDateFormatted.displayFormat}
</font>
<h1 style="margin-top:0;">{$content.title}</h1>

{if $forecast.properties.illustration|@count gt 0}
<div style="float:left;width:250px;padding:10px;">
    {foreach from=$forecast.properties.illustration item=illustration}
    <div>
        <img src="{$config.wcm.webSite.urlRepository}illustration/photo/archives/{$illustration.quicklook}" alt="{$illustration.legend|base64_decode|trim}" title="{$illustration.legend|base64_decode|trim}"/><h4>{$illustration.legend|base64_decode|trim}</h4>
        <h5>©{$illustration.rights|base64_decode|trim}</h5>
    </div>
    {/foreach}
</div>
{/if}

<div style="margin:10px;">
    {$content.description}
    {$content.text}
</div>
