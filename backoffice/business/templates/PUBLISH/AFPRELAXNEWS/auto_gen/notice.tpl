{load class="notice" object="onotice" where="id = `$context.id`"}

{assign var="className" value="notice"}
{assign var="medias" value=$notice.medias}

{assign var="filename" value="`$config.wcm.webSite.repository``$notice.permalinks`"}
{assign var=obj value=$notice.getCurrentObj}
{assign var=content value=$obj.object->getContentByFormat('default')}

{capture name="detail"}
	{if $notice.import_feed == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/notice/notice.archive.detail.tpl" bizobject="$notice" obizobject="$onotice"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/notice/notice.detail.tpl" bizobject="$notice" obizobject="$onotice"}
	{/if}
{/capture}
{dump file=$filename|replace:'%format%':'detail' content=$smarty.capture.detail utf8=true}

{capture name="list"}
	{if $notice.import_feed == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/notice/notice.archive.list.tpl" bizobject="$notice" obizobject="$onotice"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/notice/notice.list.tpl" bizobject="$notice" obizobject="$onotice"}
	{/if}
{/capture}
{dump file=$filename|replace:'%format%':'list' content=$smarty.capture.list utf8=true}

{capture name="print"}
	{if $notice.import_feed == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/notice/notice.archive.print.tpl" bizobject="$notice" obizobject="$onotice"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/notice/notice.print.tpl" bizobject="$notice" obizobject="$onotice"}
	{/if}
{/capture}
{dump file=$filename|replace:'%format%':'print' content=$smarty.capture.print utf8=true}

{capture name="media"}
	{if $notice.import_feed == "icm" }
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/notice/notice.archive.media.tpl" bizobject="$notice" obizobject="$onotice"}
	{else}
		{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/notice/notice.media.tpl" bizobject="$notice" obizobject="$onotice"}
	{/if}
{/capture}
{dump file=$filename|replace:'%format%':'media' content=$smarty.capture.media utf8=true}