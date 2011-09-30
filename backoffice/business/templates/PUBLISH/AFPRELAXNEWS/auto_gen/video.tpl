{load class="video" object="ovideo" where="id = `$context.id`"}

{assign var="className" value="video"}
{assign var="medias" value=$video.medias}

{assign var="filename" value="`$config.wcm.webSite.repository``$video.permalinks`"}
{assign var=obj value=$video.getCurrentObj}
{assign var=content value=$obj.object->getContentByFormat('default')}

{capture name="detail"}
	{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/video/video.detail.tpl" bizobject="$video" obizobject="$ovideo"}
{/capture}
{dump file=$filename|replace:'%format%':'detail' content=$smarty.capture.detail utf8=true}

{capture name="list"}
	{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/video/video.list.tpl" bizobject="$video" obizobject="$ovideo"}
{/capture}
{dump file=$filename|replace:'%format%':'list' content=$smarty.capture.list utf8=true}

{capture name="print"}
	{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/video/video.print.tpl" bizobject="$video" obizobject="$ovideo"}
{/capture}
{dump file=$filename|replace:'%format%':'print' content=$smarty.capture.print utf8=true}

{capture name="media"}
	{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/video/video.media.tpl" bizobject="$video" obizobject="$ovideo"}
{/capture}
{dump file=$filename|replace:'%format%':'media' content=$smarty.capture.media utf8=true}







