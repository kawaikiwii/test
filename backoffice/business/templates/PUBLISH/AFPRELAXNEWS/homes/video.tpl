{capture name="RUBRIC"}
{assign var=rootChanIds value=$rootChannel->id}
{foreach from=$channels item=channel}
{assign var=rootChanIds value="`$rootChanIds`,`$channel->id`"}
{/foreach}
	<div class="ari-desk-header ari-desk-{$rootChannel->css}">
		<h2 class="ari-desk-header-title"><a href="#" onclick="ARe.search.rubric(this, 'video', '{$rootChanIds}')">{$rootChannel->title}</a></h2>
		<div class="ari-desk-header-category">
{foreach from=$channels item=channel name=foo}
{assign var=dex value=$channel->id}
				<a href="#" onclick="ARe.search.rubric(this, 'video', '{','|implode:$bChannels.$dex}')">{$channel->title}</a>{if !$smarty.foreach.foo.last},{/if} 
{/foreach}
    	</div>
	</div>
	<div class="ari-desk-content">
{assign var="tDate" value=$smarty.now|date_format:"%Y-%m-%d %H:%M:00"}
{assign var="cDate" value=""}
{foreach from=$results item=bizObject}
{assign var="filename" value="`$config.wcm.webSite.repository``$bizObject->permalinks`"}
{assign var="filename" value=$filename|replace:'%format%':'list'}
{if $filename|file_exists}
{assign var="nDate" value=$bizObject->publicationDate|date_format:"%d-%m-%Y 12:00:00"}
{if $cDate != $nDate || $cDate == ''}
{if $cDate == ''}
						<h1 id="ari-separator-video-{$rootChannel->css}" class="ari-desk-separator-date">{$nDate}</h1>
{else}
						<h1 class="ari-desk-separator-date">{$nDate}</h1>
{/if}
{assign var="cDate" value=$nDate}
{/if}
{include file=$filename}
{/if}
{/foreach}
	</div>
{/capture}
{assign var="filename" value="`$config.wcm.webSite.repository`sites/`$site->code`/homes/video.`$rootChannel->css`.html"}
{dump file=$filename content=$smarty.capture.RUBRIC utf8=true}
