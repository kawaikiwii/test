{assign var="illustrated" value=""}
{if $news.properties.illustration|@count gt 0}
{assign var="illustrated" value="ari-illustrated-photo"}
{/if}


<div id="list-{$className}_{$news.id}" class="ari-item ari-not-allowed" onclick="ARe.pview('{$className}', {$news.id});" ondblclick="ARe.popview('{$className}', {$news.id});">
    <span class="ari-publishDate {$illustrated}">{$news.publicationDateFormatted.displayFormat}</span>
    <span class="ari-category">[{$news.categorization.mainChannel_title}]</span>
    <span class="ari-format">News</span>
    <h2 class="ari-title">{$content.title}</h2>
</div>
