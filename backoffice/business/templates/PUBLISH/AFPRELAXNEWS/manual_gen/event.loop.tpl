{*********************************************************
	@class = event
	@type = loop
	@generation : manual
**********************************************************}

{php}
	$session 	= wcmSession::getInstance();
	$this->assign('siteId', $session->getSiteId());
{/php}

{loop class="event" object="oevent" where="workflowState = 'published' AND startDate!='NULL' AND DATE(startDate)>=DATE(NOW()) AND channelId != 'NULL' AND siteId = '`$siteId`'" order="startDate asc"}
	
	{assign var="className" value="event"}
	{assign var="medias" value=$event.medias}
	
	{assign var="filename" value="`$config.wcm.webSite.repository``$event.permalinks`"}
	{assign var=obj value=$event.getCurrentObj}
	{assign var=content value=$obj.object->getContentByFormat('default')}

	{capture name="detail"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/event/event.detail.tpl" bizobject="$event" obizobject="$oevent"}
	{/capture}
	{dump file=$filename|replace:'%format%':'detail' content=$smarty.capture.detail utf8=true}

	{capture name="list"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/event/event.list.tpl" bizobject="$event" obizobject="$oevent"}
	{/capture}
	{dump file=$filename|replace:'%format%':'list' content=$smarty.capture.list utf8=true}

	{capture name="print"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/event/event.print.tpl" bizobject="$event" obizobject="$oevent"}
	{/capture}
	{dump file=$filename|replace:'%format%':'print' content=$smarty.capture.print utf8=true}

	{capture name="media"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/event/event.media.tpl" bizobject="$event" obizobject="$oevent"}
	{/capture}
	{dump file=$filename|replace:'%format%':'media' content=$smarty.capture.media utf8=true}
	
	
{/loop}