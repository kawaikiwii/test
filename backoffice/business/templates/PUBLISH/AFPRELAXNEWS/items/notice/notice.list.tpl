{assign var="illustrated" value=""}
{if $medias|@count gt 0}
	{assign var="illustrated" value="ari-illustrated-photo"}
{/if} 
{assign var="theSourceObject" value=$notice.source} 
{assign var="theLangue" value=$notice.language}
{assign var="channelIds" value=$notice.channelIds|unserialize}
{assign var="channelIds" value=","|implode:$channelIds}
{assign var="channelId" value=$notice.channelId}
{if $notice.referentClass == 'video' || $notice.referentClass == 'slideshow' || $notice.referentClass == 'event'}
{if $notice.referentClass == 'event'}
{assign var="preTitle" value="_NOTICE_EVENT_PUBLISH"|constant}
{/if}
{if $notice.referentClass == 'slideshow'}
{assign var="preTitle" value="_NOTICE_SLIDESHOW_PUBLISH"|constant}
{/if}
{if $notice.referentClass == 'video'}
{assign var="preTitle" value="_NOTICE_VIDEO_PUBLISH"|constant}
{/if}	
<div cid="{$notice.referentId}" classname="{$notice.referentClass}" channelId="{$channelId}" channelIds="{$channelIds}" id="list-notice_{$notice.id}" class="ari-item ari-notice ari-not-allowed" onclick="ARe.pview('{$notice.referentClass}', {$notice.referentId});" ondblclick="ARe.popview('{$notice.referentClass}', {$notice.referentId});">
    <span class="ari-publishDate {$illustrated}">{$notice.publicationDateFormatted.displayFormat}</span>
    <span class="ari-category">[NOTICE{if $notice.categorization.mainChannel_title != ""} - {$notice.categorization.mainChannel_title}{/if}]</span>
    <span class="ari-format">Notice</span>
    <h2 class="ari-title">{$preTitle} : {$content.title}</h2>
</div>
{else}
<div cid="{$notice.id}" classname="{$className}" channelId="{$channelId}" channelIds="{$channelIds}" id="list-{$className}_{$notice.id}" class="ari-item ari-notice ari-not-allowed" onclick="ARe.pview('notice', {$notice.id});" ondblclick="ARe.popview('notice', {$notice.id});">
    <span class="ari-publishDate {$illustrated}">{$notice.publicationDateFormatted.displayFormat}</span>
    <span class="ari-category">[NOTICE{if $notice.categorization.mainChannel_title != ""} - {$notice.categorization.mainChannel_title}{/if}]</span>
    <span class="ari-format">Notice</span>
    <h2 class="ari-title">{$content.title}</h2>
</div>
{/if}