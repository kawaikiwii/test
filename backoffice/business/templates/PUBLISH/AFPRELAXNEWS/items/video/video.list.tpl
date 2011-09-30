{assign var="channelIds" value=$video.channelIds|unserialize}
{assign var="channelIds" value="-"|implode:$channelIds}
{assign var="channelId" value=$video.channelId}
<div cid="{$video.id}" classname="{$className}" channelId="{$channelId}" channelIds="-{$channelIds}-" id="list-{$className}_{$video.id}" class="ari-item ari-not-allowed" onclick="ARe.pview('{$className}', {$video.id});" ondblclick="ARe.popview('{$className}', {$video.id});">
{assign var="embedOriginal" value=$video.embed}
{php}
$embedOriginal = $this->get_template_vars('embedOriginal');
if(strpos($embedOriginal,"http://video.relaxnews.com"))
$css = "ari-illustrated-video";
else
$css = "ari-illustrated-video-news";
$this->assign('css', $css);
{/php}
{if $medias|@count gt 0}
{foreach from=$video.relateds item=related name=foo}
{if $smarty.foreach.foo.first}
	<div class="ari-illustration video">
		<div class="ari-illustration-item">
{assign var="imageUrl" value=$related.object->getPhotoUrlByFormat('w50')}
			<img src="{$imageUrl}" alt="{$related.relation.title|htmlspecialchars} - ©{$related.object->credits|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars} - ©{$related.object->credits|htmlspecialchars}"/>
		</div>
	</div>
{/if}
{/foreach}
{else}
	<div class="ari-illustration video">
		<div class="ari-illustration-item">
{assign var="imageUrl" value=''}
{php}
$nb = rand(1,4);
$imageUrl = "http://repository.relaxnews.net/images/default/Alternate".$nb.".jpg";
$this->assign('imageUrl', $imageUrl);
{/php}
			<img src="{$imageUrl}" alt="" title=""/>
		</div>
	</div>
{/if}
	<div class="ari-video-content">
		<div class="ari-category">[{$video.categorization.mainChannel_title}]</div>
		<div class="ari-publishDate {$css}">{$video.publicationDateFormatted.displayFormat}</div>
{php}
$embed = "";
$embedOriginal = $this->get_template_vars('embedOriginal');
if(strpos($embedOriginal,"http://video.relaxnews.com") === false)
$embed = "<span style='font-weight:bold;color:#64247C;'>[Embed]</span>";
$this->assign('embed', $embed);
{/php}
		{$embed}
		<h2 class="ari-title">{$content.title}</h2>
	</div>
</div>
	