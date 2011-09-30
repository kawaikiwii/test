<?php

/**
 * Project:     WCM
 * File:        biz.getquerycontent.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by an Ajax call, It returns the results of the given query
 * with the forced content embedded that is passed as parameter
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

/**
 * The following function will return whether an object is present in the relations list
 * @param $objectid: id of the object in question
 * @param $objectclass: class of the object in question
 *
 * @param $relations: relation array in the form:
 * classname => id, classname2 => id2 etc ...
 *
 */

function scanlist($objectid, $objectclass, $relations)
{
    foreach($relations as $key => $value)
    {
        $rel = explode('-', $value);

        if($objectid == $rel[1] && $objectclass == $rel[0])
            return true;
    }
    return false;
}

// Get current project
$project = wcmProject::getInstance();
$config = wcmConfig::getInstance();

// Retrieve (some) parameters
$channel_id = getArrayParameter($_REQUEST, "channel_id", null);
$query = getArrayParameter($_REQUEST, "query", null);
$orderBy = getArrayParameter($_REQUEST, "orderBy", null);
$limit = getArrayParameter($_REQUEST, "limit", 10);
$limit = is_numeric($limit)?$limit:10;
$forcedcontent = getArrayParameter($_REQUEST, "forcedcontent", null);

//replace smart tag @channelId if any
$query = str_replace("@channelId", $channel_id, $query); 


//decode the forced content
$forcedcontent = json_decode($forcedcontent);

//We can do the following processing in a method getdynamiccontent in bizchannel
//restore the forced content
$forcedcontentids = array();
$relations = array();
foreach($forcedcontent as $fcontent)
{
    $rel = explode('-', $fcontent->value);
    $destclassName = $rel[1];
    $destid = $rel[2];
    $relation = new wcmBizrelation();
    $relation->destinationClass = $destclassName;
    $relation->destinationId = $destid;
    $relation->rank = $fcontent->rank;
    $relation->sourceClass = "channel";
    $relation->sourceId = $channel_id;
    $relation->kind = wcmBizrelation::IS_COMPOSED_OF;
    $relations[] = $relation->getAssocArray(false);
    $forcedcontentids[] = $destclassName."-".$destid;
}

$prefix = getArrayParameter($_REQUEST, "prefix", null);
//restore the channel from the session
$bizobject =  $_SESSION['current_channel'][$channel_id];

//execute the search
$engine = $config['wcm.search.engine'];
$search = wcmBizsearch::getInstance($engine);
$total = $search->initSearch('quickSearch', $query, $orderBy);
$results = $search->getDocumentRange(0, $limit, 'quickSearch', false);

//merge the results of the query with the forced content
$mixedresults = array();
foreach($relations as $rel)
{
    $mixedresults[$rel['rank']] = $rel;
}

//counter is for looping through the search results
//counter2 is for looping through the output results (mixedresults)
$counter = 0;
$counter2 = 1;
for($i=1; $i<=$limit;$i++)
{
    //if there is no forced content and the result is already present in the list of forced content, do nothing
    if(isset($results[$counter]) && scanlist($results[$counter]->id, get_class($results[$counter]), $forcedcontentids))
    {
        $counter++;
    }
    else
    {
        //if there is no forced content and there is a search result, insert it
        if(!isset($mixedresults[$counter2]) && isset($results[$counter]))
        {
            $mixedresults[$counter2] = $results[$counter];
            $counter++;
        }
        //if there are no forced content and no result, insert a ghost
        else if(!isset($mixedresults[$counter2]))
        {
            $mixedresults[$counter2] = null;
        }

        $counter2++;
    }
}
   ksort($mixedresults);

   if (!defined('SMARTY_DIR'))
   {
    define('SMARTY_DIR', dirname(__FILE__) . '/../includes/Smarty/');
   }
   require_once(SMARTY_DIR . 'Smarty.class.php');
   require_once(SMARTY_DIR . 'Smarty_Compiler.class.php');


   $params = array(
    'pk' => getArrayParameter($_REQUEST, 'pk', '_br_2')
   );

   $kind =  wcmBizrelation::IS_COMPOSED_OF;
   $pk = '_br_'.$kind;

   $tpl = new wcmTemplateGenerator();
   $tpl->assign('prefix',$prefix);
   $tpl->assign('mixedresults',$mixedresults);
   $tpl->assign('pk',$pk);
   $tplFile = $config['wcm.templates.path'] . '/relations/mainchannel.tpl';
   $html = $tpl->fetch($tplFile);


   echo $html;
