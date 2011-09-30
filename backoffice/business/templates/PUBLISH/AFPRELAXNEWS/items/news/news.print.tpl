<br>
<font size="2">
    {$news.sourceLabel} | {$news.publicationDateFormatted.displayFormat}
</font>
<h1 style="margin-top:0;">{$content.title}</h1>
{if $medias|@count gt 0}
<div style="float:left;width:250px;padding:10px;">
{foreach from=$news.relateds item=related}
{if $related.relation.destinationClass == 'photo'}
    <div>
        <img src="{$related.object->getPhotoUrlByFormat('w250')}" width="250" alt="{$related.relation.title|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars}"/><h4>{$related.relation.title}</h4>
        <h5>©{$related.object->credits}</h5>
{if $related.relation.media_description}
        <br>
        {$related.relation.media_description}{/if}
    </div>
{/if}
{/foreach}
</div>
{/if}
<div style="margin:10px;">
    {$content.description}
    {$content.text}
</div>
<br>
<hr>
<br>
