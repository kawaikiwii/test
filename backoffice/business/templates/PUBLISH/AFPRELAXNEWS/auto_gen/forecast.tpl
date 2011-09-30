{load class="forecast" object="oforecast" where="id = `$context.id`"}

{assign var="className" value="forecast"}
{assign var="filename" value="`$config.wcm.webSite.repository``$forecast.permalinks`"}
{assign var=obj value=$forecast.getCurrentObj}
{assign var=content value=$obj.object->getContentByFormat('default')}

{capture name="detail"}
	{if $forecast.source == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.archive.detail.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.detail.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{/if}
{/capture}
{dump file=$filename|replace:'%format%':'detail' content=$smarty.capture.detail utf8=true}

{capture name="list"}
	{if $forecast.source == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.archive.list.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.list.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{/if}
{/capture}
{dump file=$filename|replace:'%format%':'list' content=$smarty.capture.list utf8=true}

{capture name="print"}
	{if $forecast.source == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.archive.print.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.print.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{/if}
{/capture}
{dump file=$filename|replace:'%format%':'print' content=$smarty.capture.print utf8=true}

{capture name="media"}
	{if $forecast.source == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.archive.media.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/forecast/forecast.media.tpl" bizobject="$forecast" obizobject="$oforecast"}
	{/if}
{/capture}
{dump file=$filename|replace:'%format%':'media' content=$smarty.capture.media utf8=true}



