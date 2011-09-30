{load class="prevision" object="oprevision" where="id = `$context.id`"}

{assign var="className" value="prevision"}
{assign var="medias" value=$prevision.medias}
{assign var="filename" value="`$config.wcm.webSite.repository``$prevision.permalinks`"}
{assign var=obj value=$prevision.getCurrentObj}
{assign var=content value=$obj.object->getContentByFormat('default')}

{capture name="detail"}
	{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/prevision/prevision.detail.tpl" bizobject="$prevision" obizobject="$oprevision"}
{/capture}
{dump file=$filename|replace:'%format%':'detail' content=$smarty.capture.detail utf8=true}

{capture name="print"}
	{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/prevision/prevision.print.tpl" bizobject="$prevision" obizobject="$oprevision"}
{/capture}
{dump file=$filename|replace:'%format%':'print' content=$smarty.capture.print utf8=true}

{capture name="media"}
	{include file="`$config.wcm.templates.path`PUBLISH/AFPRELAXNEWS/items/prevision/prevision.media.tpl" bizobject="$prevision" obizobject="$oprevision"}
{/capture}
{dump file=$filename|replace:'%format%':'media' content=$smarty.capture.media utf8=true}


