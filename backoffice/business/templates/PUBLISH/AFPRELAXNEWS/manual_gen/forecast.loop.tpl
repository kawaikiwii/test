{*********************************************************
	@class = forecast
	@type = loop
	@generation : manual
**********************************************************}

{php}
	$session 	= wcmSession::getInstance();
	$this->assign('siteId', $session->getSiteId());
$this->error_reporting=E_ERROR; 
{/php}

{loop class="forecast" object="oforecast" where="workflowState = 'published' AND channelId != 'NULL' AND publicationDate != 'NULL' AND siteId = '`$siteId`'" order="publicationDate desc"}

	{assign var="className" value="forecast"}
	
	{assign var="filename" value="`$config.wcm.webSite.repository``$forecast.permalinks`"}
	{assign var=obj value=$forecast.getCurrentObj}
	{assign var=content value=$obj.object->getContentByFormat('default')}

	{capture name="detail"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.archive.detail.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{/capture}
	{dump file=$filename|replace:'%format%':'detail' content=$smarty.capture.detail utf8=true}

	{capture name="list"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.archive.list.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{/capture}
	{dump file=$filename|replace:'%format%':'list' content=$smarty.capture.list utf8=true}

	{capture name="print"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.archive.print.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{/capture}
	{dump file=$filename|replace:'%format%':'print' content=$smarty.capture.print utf8=true}

	{capture name="media"}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.archive.media.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{/capture}
	{dump file=$filename|replace:'%format%':'media' content=$smarty.capture.media utf8=true}

{/loop}