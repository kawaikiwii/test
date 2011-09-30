{literal}
<?php
    if (!$session->userId || !isset($_SESSION['ice']))
    {
        $iceTitle = 'login to use in-context editing features';
        $iceLink = 'nstein-ice/login';
    }
    else
    {
        $iceMode  = getArrayParameter($_SESSION, 'ice', 0);
        $iceTitle = ($iceMode) ? 'quit ice mode' : 'enter ice mode';
        $iceLink  = ($iceMode) ? 'nstein-ice/0' : 'nstein-ice/1';
    }
?>
{/literal}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $pageTitle;?></title>
    <link href="{$config.wcm.webSite.url}css/main.css" rel="stylesheet" type="text/css" />
    <link href="{$config.wcm.webSite.url}css/demo-widgets.css" rel="stylesheet" type="text/css" />
    <link rel="alternate" type="application/rss+xml" title="Most popular articles" href="{$config.wcm.webSite.url}rss/most-popular.xml" />
    <link rel="alternate" type="application/rss+xml" title="Most recent articles" href="{$config.wcm.webSite.url}rss/most-recent.xml" />
    <link rel="alternate" type="application/rss+xml" title="Most discussed articles" href="{$config.wcm.webSite.url}rss/most-discussed.xml" />
    <?php include($config['wcm.webSite.path'] . 'js/main.js.php'); ?>
  </head>
<body>
<div id="wrapper">

    <div id="header">
        <a class="off" href="{$config.wcm.webSite.url}site{$context.site}/nstein-ice/logout" title="back to final mode"><img src="{$config.wcm.webSite.url}img/nstein-off.gif" alt="back to final view" /></a>
        <a class="ice" href="{$config.wcm.webSite.url}site{$context.site}/<?php echo $iceLink;?>" title="<?php echo $iceTitle;?>"><img src="{$config.wcm.webSite.url}img/nserver.gif" alt="nstein in-Context editing" /></a>
        <img class="banner" src="{$config.wcm.webSite.url}img/nstein_daily.gif" width="217" height="41" alt="" /><br/>
        <form id="site_search" name="site_search" method="post" action="{$config.wcm.webSite.url}site{$context.site}/search/">
            <div>
                <input type="hidden" id="search_publicationData" name="search_publicationData" value="<?php echo $_SESSION["siteId"]; ?>" />
                <input type="hidden" id="search_siteId" name="search_siteId" value="<?php echo $_SESSION["siteId"]; ?>" />
                <input type="text" class="full_text" size="25" value="Enter keywords" id="search_query_string" name="searchQueryString" onfocus="this.value='';" onblur="if (this.value=='') {ldelim} this.value='Enter keywords';{rdelim}" />
                <input type="submit" value="<?php echo _WEB_SEARCH; ?>" class="form_submit" />
            </div>
        </form>
        <p id="last_updated">Last Updated: <?php echo $lastUpdated;?></p>
        <ul id="channel_navigation">
            <?php include($config['wcm.webSite.path'] . 'site{$context.site}/cache/navigation.php'); ?>
        </ul>
    </div>
