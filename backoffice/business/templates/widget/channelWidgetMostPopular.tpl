<div class="site_links">
<h2>
    Most Popular
    <a href="{$config.wcm.webSite.url}rss/most-popular.xml"><img src="{$config.wcm.webSite.url}img/icon_feed.gif" width="16" height="16" alt="" class="icon_rss" /></a>
</h2>
<ul>
    {loop class="article" orderby="hitCount DESC" limit=$widget.settings.nb}
        <li><a href="{$article|@wcm:url}">{$article.title}</a></li>
    {/loop}
</ul>
</div>
