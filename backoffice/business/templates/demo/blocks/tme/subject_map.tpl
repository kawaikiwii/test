{if not isset($article)}
    {if isset($channel)}
        {load class="article" where="channelId=`$channel.id`" limit="1"}
        {if not $article or not $article.id}
            {load class="article" limit="1"}
        {/if}
    {/if}
    {if not isset($article)}
        {load class="article" limit="1"}
    {/if}
{/if}
{load class="site" where="id=`$article.siteId`"}
<div id="subject_map">
    {include file="demo/blocks/tme/concepts.tpl" bizobject=$article}
    {include file="demo/blocks/tme/entities_pn.tpl" bizobject=$article}
    {include file="demo/blocks/tme/entities_on.tpl" bizobject=$article}
    {include file="demo/blocks/tme/entities_gl.tpl" bizobject=$article}
</div>