{assign var="illustrated" value=""}
{if $forecast.illustration|@count gt 0}
{assign var="illustrated" value="ari-illustrated-photo"}
{/if}
{assign var="breaking" value=""}
{if in_array('breaking-news', $forecast.notifications)}
{assign var="breaking" value="ari-breaking-news"}
{/if}
<div id="list-{$className}_{$forecast.id}" class="ari-item ari-forecast ari-not-allowed" onclick="ARe.pview('{$className}', {$forecast.id});" ondblclick="ARe.popview('{$className}', {$forecast.id});">
    <span class="ari-category" style="maring-left:0.2em">[{$forecast.categorization.mainChannel_title}]</span>
    <span class="ari-format">News</span>
	<div>
	    <h2 class="ari-title">{$content.title}</h2>
		<h2 class="ari-title"><br/>{$forecast.startDate|date_format:"%d-%m-%Y"}</h2>
	</div>
</div>
