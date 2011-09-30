{*********************************************************
	@class = news
	@type = loop
	@generation : manual
**********************************************************}

{php}
	$session 	= wcmSession::getInstance();
	$this->assign('siteId', $session->getSiteId());
{/php}

{loop class="news" object="onews" where="workflowState = 'published' AND channelId != 'NULL' AND publicationDate != 'NULL' AND siteId = '`$siteId`'" order="publicationDate desc"}

	{assign var="className" value="news"}
	{assign var="medias" value=$news.medias}
	
	{assign var="filename" value="`$config.wcm.webSite.repository``$news.permalinks`"}
	{assign var=obj value=$news.getCurrentObj}
	{assign var=content value=$obj.object->getContentByFormat('default')}

	{capture name="detail"}
	{if $news.import_feed == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/news/news.archive.detail.tpl" bizobject="$news" obizobject="$onews"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/news/news.detail.tpl" bizobject="$news" obizobject="$onews"}
	{/if}
	{/capture}
	{dump file=$filename|replace:'%format%':'detail' content=$smarty.capture.detail utf8=true}

	{capture name="list"}
	{if $news.import_feed == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/news/news.archive.list.tpl" bizobject="$news" obizobject="$onews"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/news/news.list.tpl" bizobject="$news" obizobject="$onews"}
	{/if}
	{/capture}
	{dump file=$filename|replace:'%format%':'list' content=$smarty.capture.list utf8=true}

	{capture name="print"}
	{if $news.import_feed == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/news/news.archive.print.tpl" bizobject="$news" obizobject="$onews"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/news/news.print.tpl" bizobject="$news" obizobject="$onews"}
	{/if}
	{/capture}
	{dump file=$filename|replace:'%format%':'print' content=$smarty.capture.print utf8=true}

	{capture name="media"}
	{if $news.import_feed == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/news/news.archive.media.tpl" bizobject="$news" obizobject="$onews"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/news/news.media.tpl" bizobject="$news" obizobject="$onews"}
	{/if}
	{/capture}
	{dump file=$filename|replace:'%format%':'media' content=$smarty.capture.media utf8=true}

{/loop}