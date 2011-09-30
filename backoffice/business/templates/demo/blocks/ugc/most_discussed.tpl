<div class="site_links">
<h2>
    Most Discussed
    <a href="{$config.wcm.webSite.url}site{$channel.siteId}/rss/most-discussed.xml"><img src="{$config.wcm.webSite.url}img/icon_feed.gif" width="16" height="16" alt="" class="icon_rss" /></a>
</h2>
<ul>
    {loop class="article" orderby="commentCount DESC" limit="5"}
        <li><a href="{$article|@wcm:url}">{$article.title}</a></li>
    {/loop}
</ul>
</div>