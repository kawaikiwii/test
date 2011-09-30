<?php

/**
 * Project:     WCM
 * File:        list.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

    // Execute action
    wcmMVC_Action::execute('list', array('class' => 'wcmList', 'tree' => 'list'));
    $sysobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    
    $todo  = getArrayParameter($_REQUEST, 'todo', null);
    $idchild  = getArrayParameter($_REQUEST, 'idchild', null);
    $labelchild  = getArrayParameter($_REQUEST, 'labelchild', null);
    $codechild  = getArrayParameter($_REQUEST, 'codechild', null);
    $id  = getArrayParameter($_REQUEST, 'id', null);
    $parentId  = getArrayParameter($_REQUEST, 'parentId', null);
    $label  = getArrayParameter($_REQUEST, 'label', null);
    $code  = getArrayParameter($_REQUEST, 'code', null);
    

    switch($todo)
    {
      case 'deletechild':
          wcmList::dropList($idchild);
      break;
      
      case 'updatechild':
          $List = new wcmList();
          $List->refresh($idchild);
          $List->code = $codechild;
          $List->label = $labelchild;
          $List->save();
      break;

      case 'addchild':
         wcmList::addList($codechild, $labelchild, $sysobject->id);
      break;
      
      case 'deleteList':
          wcmList::dropList($sysobject->id);
          $sysobject->id=null;
          $sysobject->code = '';
          $sysobject->label = '';
      break;
      
      case 'updateList':
          $sysobject->parentId = $parentId;
          $sysobject->code = $code;
          $sysobject->label = $label;
          $sysobject->save();
      break;

      case 'addList':
         $id = wcmList::addList($code, $label, $parentId);
         $sysobject->refresh($id);
      break;
      
    }

    
        

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');
   // wcmGUI::renderObjectMenu();
    
    echo "<script type=\"text/javascript\">function myConfirm(){ return confirm(\""._DELETE_CONFIRM."\"); }</script>";
        
    echo '<div id="treeview">';
    $tabs = new wcmAjaxTabs('navigation', false);
    $tabs->addTab('tree', _BROWSE, true, wcmMVC_Action::getTree()->renderHTML());
    $tabs->addTab('history', _HISTORY, false, wcmGUI::renderObjectHistory());
    $tabs->render();
    echo '</div>';

    echo '<div id="content">';
    wcmGUI::openObjectForm($sysobject);

    $tabs = new wcmAjaxTabs('list', true);
    $tabs->addTab('t1', _PROPERTIES, true, null, wcmModuleURL('system/liste/properties'));
    $tabs->render();

    wcmGUI::closeForm();
    echo '</div>';
    

    include(WCM_DIR . '/pages/includes/footer.php');
?>
