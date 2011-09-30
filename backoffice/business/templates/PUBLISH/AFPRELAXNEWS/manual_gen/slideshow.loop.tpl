{*********************************************************
	@class = slideshow
	@type = loop
	@generation : manual
**********************************************************}

{php}
	$session 	= wcmSession::getInstance();
	$this->assign('siteId', $session->getSiteId());
{/php}

{loop class="slideshow" object="oslideshow" where="workflowState = 'published' AND channelId != 'NULL' AND publicationDate != 'NULL' AND siteId = '`$siteId`'" order="publicationDate desc"}
	
	{assign var="className" value="slideshow"}
	{assign var="medias" value=$slideshow.medias}
	
	{assign var="filename" value="`$config.wcm.webSite.repository``$slideshow.permalinks`"}
	{assign var="xmlfilename" value="`$config.wcm.webSite.repository``$slideshow.xmlslideshow`"}
	{assign var=obj value=$slideshow.getCurrentObj}
	{assign var=content value=$obj.object->getContentByFormat('default')}

	{capture name="detail"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/slideshow/slideshow.detail.tpl" bizobject="$slideshow" obizobject="$oslideshow"}
	{/capture}
	{dump file=$filename|replace:'%format%':'detail' content=$smarty.capture.detail utf8=true}
	
	{capture name="xml"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/slideshow/slideshow.xml.tpl" bizobject="$slideshow" obizobject="$oslideshow"}
	{/capture}
	{dump file=$xmlfilename|replace:'%format%':'detail' content=$smarty.capture.xml utf8=true}

	{capture name="list"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/slideshow/slideshow.list.tpl" bizobject="$slideshow" obizobject="$oslideshow"}
	{/capture}
	{dump file=$filename|replace:'%format%':'list' content=$smarty.capture.list utf8=true}

	{capture name="print"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/slideshow/slideshow.print.tpl" bizobject="$slideshow" obizobject="$oslideshow"}
	{/capture}
	{dump file=$filename|replace:'%format%':'print' content=$smarty.capture.print utf8=true}

	{capture name="media"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/slideshow/slideshow.media.tpl" bizobject="$slideshow" obizobject="$oslideshow"}
	{/capture}
	{dump file=$filename|replace:'%format%':'media' content=$smarty.capture.media utf8=true}

{/loop}