{include file="demo/rss/header.tpl"}
{loop class="article" of="site" order="modifiedAt DESC" limit="5"}
    {include file="demo/rss/article.tpl"}
{/loop}
{include file="demo/rss/footer.tpl"}