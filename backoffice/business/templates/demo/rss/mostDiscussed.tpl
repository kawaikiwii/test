{include file="demo/rss/header.tpl"}
{loop class="article" of="site" order="commentCount DESC" limit="5"}
    {include file="demo/rss/article.tpl"}
{/loop}
{include file="demo/rss/footer.tpl"}
