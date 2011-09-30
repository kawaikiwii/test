<?php

/**
 * Project:     WCM
 * File:        wcm.ajaxTable.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * @see         wcm.ajaxTable.js
 */

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Open current session
$session = wcmSession::getInstance();
$html='';
$response='';

// Retrieve query parameters
$command = getArrayParameter($_REQUEST, "command", null);

$id = getArrayParameter($_REQUEST, "id", null);
$depth = getArrayParameter($_REQUEST, "depth", null);
$key = getArrayParameter($_REQUEST, "key", null);

$code = getArrayParameter($_REQUEST, "code", null);
$label = getArrayParameter($_REQUEST, "label", null);
$parentId = getArrayParameter($_REQUEST, "parentId", null);

function lectureListe($item,$selectId=0,$cpt=0,&$html='')
{
  if($cpt==0)
  {
    $html = '<select id="listeselect" onchange="getFinalContent(this.value,1,\'path\')">';
    $html .= '<option value="">Listes</option>';
  }
  
  foreach($item as $subitem)
  {
    if($selectId == $subitem['id']) $chSel = "selected='selected'";
    else  $chSel = "";
  
    $html .= '<option '.$chSel.' value="'.$subitem['id'].'">'.str_repeat("-- ",$cpt+1).$subitem['label'].'</option>';
    if(count($subitem['subLists'])>0) lectureListe($subitem['subLists'],$selectId,$cpt+1, $html);
  }
  
  if($cpt==0) $html .= '</select>';
  return $html;
}

      
switch($command)
{

    
  
  case 'addList':

    $divId = 'divlisteselect';
    
    $result = wcmList::addList($code, $label, $parentId);
    $newList = new wcmList();
    $newList->refresh($result);
    $parentId = $newList->parentId;
    
    $arr = wcmList::getRootContent();
    $html = lectureListe($arr,$parentId);
    
    $response .= '<response type="item" id="' . $divId . '"><![CDATA[' . $html . ']]></response>'. "\n";
   
  break;
  
  case 'updateList':
    $divId = 'divlisteselect';
  
    $List = new wcmList();
    $List->refresh($id);
    $List->code = $code;
    $List->label = $label;
    $List->save();
    
    $parentId = $List->parentId;
    $arr = wcmList::getRootContent();
    $html = lectureListe($arr,$parentId);
    
    $response .= '<response type="item" id="' . $divId . '"><![CDATA[' . $html . ']]></response>'. "\n";
    
  break;
  
  case 'deleteList':
    $divId = 'divlisteselect';
  
    $List = new wcmList();
    $List->refresh($id);
    $parentId = $List->parentId;
    wcmList::dropList($id);
    
    $arr = wcmList::getRootContent();
    $html = lectureListe($arr,$parentId);
    
    $response .= '<response type="item" id="' . $divId . '"><![CDATA[' . $html . ']]></response>'. "\n";
    
  break;
  
  case 'getFinalContent':

    $divId = 'content_liste';
    $result = wcmList::getFinalContent($id,$depth,'id');
    foreach($result as $key => $val)
    {
      $item = new wcmList();
      $item->refresh($key);

      $html .= '<div>';
      $html .= '<div class="toolbar" style="float: left; margin-right: 10px;">';
      $html .= '<a class="delete" title="Edit" style="float: left;" href="javascript:deleteList('.addslashes($item->id).')"> </a>';
      $html .= '<a class="edit" title="Edit" style="float: left;" href="javascript:clickEdit('.addslashes($item->id).', \''.addslashes($item->code).'\',\''.addslashes($val).'\')"> </a>';
      $html .= '</div>';
      $html .= '<div style="float: left;">'.$val.'</div>';
      $html .= '</div>';
      $html .= '<hr style="margin: 0pt; padding: 0pt; clear: both; visibility: hidden; height: 0px;"/>';
    }

    $response .= '<response type="item" id="' . $divId . '"><![CDATA[' . $html . ']]></response>'. "\n";
    
  break;
}

  // No browser cache
  header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
  header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
  header( 'Cache-Control: no-store, no-cache, must-revalidate' );
  header( 'Cache-Control: post-check=0, pre-check=0', false );
  header( 'Pragma: no-cache' );
  
  // Xml output
  header("Content-Type: text/xml");
  echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
  
  // Write ajax response
  echo '<ajax-response>' . "\n";
  echo $response;
  echo '</ajax-response>'. "\n";

