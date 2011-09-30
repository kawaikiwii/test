RewriteEngine on
RewriteBase {$config.wcm.webSite.url|wcm:parseUrl:path}site{$context.site}/
{literal}
# controller and ICE URLs are never rewritten
RewriteRule controller.php controller.php [QSA,L]
RewriteRule ice.php ice.php [QSA,L]

# ICE mode
RewriteRule ^nstein-ice/([0-9a-zA-Z]+)*$ ice.php?mode=$1 [QSA,L]

# Search page
RewriteRule ^search/(.*)$ cache/search.php?search_fulltext=$1 [QSA,L]
RewriteRule ^search\..*?([a-zA-Z]+)$ cache/search.php?$1 [QSA,L]

# RSS feeds
RewriteRule ^rss/([0-9]+)/([a-zA-Z]+)\..*$ cache/rss/$1.php [L]
RewriteRule ^rss/([a-zA-Z-]+)\..*$ cache/rss/$1.php [L]

# channels and home page
# if no file name or index.* is entered, redirect to home page and exit
RewriteRule ^$ controller.php?className=channel&id=1 [L]
RewriteRule ^index\..*$ controller.php?className=channel&id=1 [L]
RewriteRule ^([a-zA-Z0-9]+)\..*$ controller.php?className=channel&title=$1 [L]

# Sub bizobjects (chapters of article, photos or slideshow, etc.)
# Expected format is {className}/{id}c{subId}.* (matches any extension)
RewriteRule ^([a-zA-Z]+)/([0-9]+)c([0-9]+)/.*$ controller.php?className=$1&id=$2&subId=$3 [L]

# Bizobjects (articles, slideshows, ...)
# will redirect to cache/bizobjectId/bizsubobjectId/1.php and exit
RewriteRule ^([a-zA-Z]+)/([0-9]+)/.*$ controller.php?className=$1&id=$2 [QSA,L]
{/literal}
