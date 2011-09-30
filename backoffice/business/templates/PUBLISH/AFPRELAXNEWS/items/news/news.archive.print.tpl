<font size="2">
    {$news.sourceLabel} | {$news.publicationDateFormatted.displayFormat}
</font>
<h1 style="margin-top:0;">{$content.title}</h1>

{if $news.properties.illustration|@count gt 0}
<div style="float:left;width:250px;padding:10px;">
    {foreach from=$news.properties.illustration item=illustration}
    {if $illustration.quicklook}
    <div>
        <img src="{$config.wcm.webSite.urlRepository}illustration/photo/archives/{$illustration.quicklook}" alt="{$illustration.legend|base64_decode|trim}" title="{$illustration.legend|base64_decode|trim}"/><h4>{$illustration.legend|base64_decode|trim}</h4>
        <h5>©{$illustration.rights|base64_decode|trim}</h5>
    </div>
    {/if}
    {/foreach}
</div>
{/if}

<div style="margin:10px;">
    {$content.description}
    {$content.text}
</div>