<?php
{if isset($channel)}
    $channelId = {$channel.id};
{else}
    $channelId = 0;
{/if}
    $pageTitle = '{$bizobject.title|escape}';
    $lastUpdated = '{$bizobject.modifiedAt|date_format:"%A, %B %e, %I:%M %p %T"}';

    include($config['wcm.webSite.path'].'site{$bizobject.siteId}/cache/header.php');
?>