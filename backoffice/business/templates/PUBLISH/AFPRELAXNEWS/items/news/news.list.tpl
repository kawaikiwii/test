{assign var="illustrated" value=""}
{if $medias|@count gt 0}
{assign var="illustrated" value="ari-illustrated-photo"}
{/if}
{if $news.embedVideo != 'NULL' && $news.embedVideo != NULL && $news.embedVideo != ""}
{assign var="illustrated" value="ari-illustrated-video"}
{/if}
{assign var="breaking" value=""}
{if @in_array('breaking-news', $news.notifications)}
{assign var="breaking" value="ari-breaking-news"}
{/if}
{assign var="channelIds" value=$news.channelIds|unserialize}
{assign var="channelIds" value="-"|implode:$channelIds}
{assign var="channelId" value=$news.channelId}
<div cid="{$news.id}" classname="{$className}" channelId="{$channelId}" channelIds="-{$channelIds}-" id="list-{$className}_{$news.id}" class="ari-item ari-not-allowed {$breaking}" onclick="ARe.pview('{$className}', {$news.id});" ondblclick="ARe.popview('{$className}', {$news.id});">
    <span class="ari-publishDate {$illustrated}">{$news.publicationDateFormatted.displayFormat}</span>
    <span class="ari-category">[{$news.categorization.mainChannel_title}{if $breaking != ""} - {'_BIZ_ALERT'|constant}{/if}]</span>
    <span class="ari-format">News</span>
    <h2 class="ari-title">{$content.title}</h2>
</div>
