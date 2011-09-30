<?php
/**
 * Project:     WCM
 * File:        modules/workflow/workflow/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $project = wcmProject::getInstance();
    $sysobject = wcmMVC_Action::getContext();
    $childList = wcmList::getFinalContent($sysobject->id,1,'id');
    $info = '';
    
    if ($sysobject->id)
    {
        $info .= '<ul class="actions">';
        $info .= '<li><a href="'. wcmMVC_SysAction::computeObjectURL('wcmList', 0, 'view', array('id' => $sysobject->id));
        $info .= '">' . _NEW_LIST . '</a></li>';
        $info .= '</ul>';
    }
    
    echo '<div class="zone">';

    wcmGUI::openCollapsablePane(_PROPERTIES, true, $info);

    wcmGUI::openFieldset( _GENERAL);
      wcmGUI::renderDropdownField('parentId', wcmList::getArborescenceList(), $sysobject->parentId, _PARENT_ITEM);
      wcmGUI::renderTextField('code', $sysobject->code, _CODE. ' *', array('class' => 'type-req'));
      wcmGUI::renderTextField('label', $sysobject->label, _NAME . ' *', array('class' => 'type-req'));
       

      echo "<ul class=\"chapter\">"; 
      if(!$sysobject->id)
        echo "<li><a href=\"javascript:addList($('parentId').value, $('label').value, $('code').value)\">" . _SAVE . "</a></li>";
      else
      {
        echo "<li><a style=\"float:right\" href=\"javascript:updateList($('parentId').value, ".$sysobject->id.", $('label').value, $('code').value)\">" . _UPDATE . "</a>";
        echo "<a style=\"float:right\" href=\"javascript:deleteList('".$sysobject->parentId."', ".$sysobject->id.")\">" . _DELETE . "</a></li>";
      }
      echo "</ul>"; 
   wcmGUI::closeFieldset();  
    
    if($sysobject->id)
    {
        wcmGUI::openFieldset( _CHILDS);
        echo "<table>";
        
        foreach($childList as $key => $label)
          if($key!=$sysobject->id)
          {
              $List = new wcmList();
              $List->refresh($key);
              
            echo "<tr>";
            echo "<td>Code : <input id=\"code_list_".$key."\" type=\"text\" value=\"".$List->code."\" style=\"width:100px\"/></td>";
            echo "<td>Label : <input id=\"label_list_".$key."\" type=\"text\" value=\"".$label."\" style=\"width:300px\"/></td>";
            echo "<td><a href=\"javascript:updateListChild(".$sysobject->id.", ".$key.", $('label_list_".$key."').value, $('code_list_".$key."').value)\"><img src=\"img/refresh.gif\"></a></td>";
            echo "<td><a href=\"javascript:deleteListChild(".$sysobject->id.", ".$key.")\"><img src=\"img/delete.gif\"></a></td>";
            echo "</tr>";
          }
          
        echo "<tr>";
        echo "<td>Code : <input id=\"code_newlist\" type=\"text\" value=\"\" style=\"width:100px\" /></td>";
        echo "<td>Label : <input id=\"label_newlist\" type=\"text\" value=\"\" style=\"width:300px\" /></td>";
        echo "<td><a href=\"javascript:addListChild(".$sysobject->id.", $('label_newlist').value, $('code_newlist').value)\"><img src=\"img/add.gif\"></a></td>";
        echo "</tr>";
        
        echo "</table>";
        wcmGUI::closeFieldset();
    }
    
    wcmGUI::closeCollapsablePane();
    echo '</div>';