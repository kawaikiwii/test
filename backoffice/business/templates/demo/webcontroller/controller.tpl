{literal}<?php
/*
 * Project:     WCM
 * File:        controller.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This controller is used to check if a specific
 * web page is available (pre-generated) or not.
 *
 * If needed, the controller will generate the page on-demand
 * and will also handle the ICE (in-context editing) mode.
 *
 * IMPORTANT: this controller is strongly coupled with the .htaccess file
 */

// Initialize api
require_once dirname(__FILE__).'/../init.php';

// get parameters
$className  = getArrayParameter($_REQUEST, 'className', 'channel');
$title      = getArrayParameter($_REQUEST, 'title', null);
$id         = getArrayParameter($_REQUEST, 'id', 1);
$subId      = getArrayParameter($_REQUEST, 'subId', 1);
$preview    = getArrayParameter($_REQUEST, 'preview', null);

/*
 * Retrieving bizobject associated to page
 *
 * Rules: 
 * - for channel, we expect to receive title of the channel as $title or a valid $id
 * - for chapter, we expect to receive the rank as $subId and the article ID as $id
 */
if ($className == 'channel')
{
    $bizobject = new channel;
    if ($title != null)
    {
        $bizobject->refreshByTitle($title);
    }
    else
    {
        $bizobject->refresh($id);
    }
}
elseif ($className == 'article' && $subId > 1)
{
    $bizobject = new chapter;
    $bizobject->beginEnum("articleId=$id AND rank=$subId");
    $bizobject->nextEnum();
}
else
{
    $bizobject = new $className($project, $id);
}

/**
 * Cached page
 *
 * Rule is: cache/{className}/{id}/{subId}.php
 *
 * Where subId is a chapter rank of else always 1 for any other bizobjects
 */
$fileName = dirname(__FILE__) . '/cache/' . $className . '/' . $bizobject->id . '/' . $subId . '.php';

// Check for ICE mode (In-Context Editing)
$ice = getArrayParameter($_SESSION, 'ice', null);

// Generate page when in ICE mode or when page is not found in cache
if ($ice != null || !file_exists($fileName))
{
    set_time_limit(7200);
    $mode = ($ice != null) ? wcmWidget::VIEW_ALL : wcmWidget::VIEW_CONTENT;
    $html = $bizobject->preview($mode);
    @saveToFile($fileName, $html);
}
include($fileName);

// Delete generated page when in ICE mode
if ($ice != null || $preview)
    unlink($fileName);
{/literal}