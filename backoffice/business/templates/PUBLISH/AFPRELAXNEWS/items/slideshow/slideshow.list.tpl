{assign var="channelIds" value=$slideshow.channelIds|unserialize}
{assign var="channelIds" value="-"|implode:$channelIds}
{assign var="channelId" value=$slideshow.channelId}
<div cid="{$slideshow.id}" classname="{$className}" channelId="{$channelId}" channelIds="-{$channelIds}-" id="list-{$className}_{$slideshow.id}" class="ari-item ari-not-allowed" onclick="ARe.pview('{$className}', {$slideshow.id});" ondblclick="ARe.popview('{$className}', {$slideshow.id});">
	<div class="ari-illustration slideshow">
		<div class="ari-illustration-item">
{foreach from=$slideshow.relateds item=related name=foo}
{if $smarty.foreach.foo.first}
					<img src="{$related.object->getPhotoUrlByFormat('w50')}" alt="{$content.title|htmlspecialchars}" title="{$content.title|htmlspecialchars}" width="50" />
{/if}
{/foreach}
		</div>
	</div>
	<div class="ari-slideshow-content">
		<div class="ari-category"> [{$slideshow.categorization.mainChannel_title}]</div>
		<div class="ari-publishDate ari-illustrated-slideshow">{$slideshow.publicationDateFormatted.displayFormat}</div>
		<h2 class="ari-title">{$content.title}</h2>
		<h4 class="ari-illustration-count">{$medias|@count} photos</h4>
	</div>
</div>
	