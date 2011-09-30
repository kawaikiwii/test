<?php
/**
 * Project:     WCM
 * File:        modules/search/manaagelist.php
 *	
 * displays list of saved searches and bins
 * 	
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 $session = wcmSession::getInstance(); 
 $config = wcmConfig::getInstance();
 
 $classname	= getArrayParameter($params, 'classname', null);
 
 $object = new $classname;
 $object->beginEnum(" userId = '".$session->userId."'", null, null, null, null);
 ?> 
 <div class="zone saved-searches">
 <?php wcmGUI::openCollapsablePane(_MENU_USER_SAVED_SEARCHES);?>
 <div id="mylist" class="tabular-presentation">
  <table>
    <tr id="head">
        <th class="actions"></th>
        <th><?php echo _BIZ_NAME;?></th>
        <th><?php echo _BIZ_SHARED;?></th>
        <th><?php echo _BIZ_SHOW_UI;?></th>
    </tr>
 
 <?php
 while ($object->nextEnum())
 {
 ?>
 	<tr id="_nlsub_<?php echo $object->id;?>">
    	<td class="actions">
            <ul class="two-buttons">
                <li><a class="edit" title="<?php echo _EDIT; ?>" href="#" onclick="edit('<?php echo $classname;?>','<?php echo $object->id;?>')"><span><?php echo _BIZ_EDIT; ?></span></a></li>
                <li><a class="delete" title="<?php echo _DELETE; ?>" href="#" onclick="deletesearch('<?php echo $classname;?>','<?php echo $object->id;?>');"><span><?php echo _BIZ_DELETE; ?></span></a></li>
            </ul>
        </td>
        <td>
        	<p id="name_<?php echo $object->id; ?>"><a href="<?php echo $config['wcm.backOffice.url']; ?>?_wcmAction=business/search&_wcmTodo=initSearch&search_query=<?php echo $object->queryString; ?>"><?php echo $object->name;?></a></p>
        	<p id="description_<?php echo $object->id; ?>"><?php echo $object->description;?></p>        
        </td>
        <td class="checkbox">
            <ul>
                <?php wcmformGUI::renderBooleanField("shared_".$object->id, $object->shared,"", array("onchange"=>"javascript:manage('updateshared','".$classname."','".$object->id."', '{\"params\": {\"value\": '+$('shared_".$object->id."').value+'}}' );"));?>
            </ul>
        </td>
        <td class="checkbox">
            <ul>
                <?php wcmformGUI::renderBooleanField("showui_".$object->id, in_array($session->userId,($object->showui == "")?array():json_decode($object->showui)),"", array("onchange"=>"javascript:manage('updateshowui','".$classname."','".$object->id."', '{\"params\": {\"value\": '+$('showui_".$object->id."').value+'}}' );"));?>
            </ul>    
        </td>
    </tr>
 <?php
 }
 
 ?>
 </table> 
 <?php   wcmGUI::closeCollapsablePane();?>
 </div>
 <?php
 	$db = wcmProject::getInstance()->database;
 	$config = wcmConfig::getInstance();
 	$tprefix = $config['wcm.systemDB.tablePrefix'];
 	$classnametable = "user_savedsearch";
 	$query = "SELECT ".$tprefix.$classnametable.".*, ".$tprefix."user.name as authorname FROM ".$tprefix."user, ".$tprefix.$classnametable." WHERE ".$tprefix.$classnametable.".userId = ".$tprefix."user.id AND ".$tprefix.$classnametable.".shared = '1' AND ".$tprefix.$classnametable.".userId != '$session->userId'";

 	$rs = $db->executeQuery($query);

 ?>
 <?php wcmGUI::openCollapsablePane("shared");?>
 <div id = "sharedlist" class="tabular-presentation">
  <table>
    <tr id="head">
        <th class=""><?php echo _BIZ_NAME;?></th>
        <th class=""><?php echo _WEB_AUTHOR;?></th>
        <th class=""><?php echo _BIZ_SHOW_UI;?></th>
    </tr>
 
 <?php
 foreach($rs as $result)
 {
 ?>
 	<tr id="_nlsub_<?php echo $result['id'];?>">

        <td>
            <p><a href="<?php echo $config['wcm.backOffice.url']; ?>?_wcmAction=business/search&_wcmTodo=initSearch&search_query="<?php echo $result['queryString']; ?>"><?php echo $result['name'];?></a></p>
        	<p><?php echo $result['description'];?></p>        
        </td>
        <td><p><?php echo $result['authorname'];?></p></td>
        <td class="checkbox">
            <ul>
            <?php wcmformGUI::renderBooleanField("showui_".$result['id'], in_array($session->userId,($result['showui'] == "")?array():json_decode($result['showui'])),"", array("onchange"=>"javascript:manage('updateshowui','".$classname."','".$result['id']."', '{\"params\": {\"value\": '+$('showui_".$result['id']."').value+'}}' );"));?>
            </ul>
        </td>
    </tr>
 <?php
 }
 ?>
 </table>
 </div>
 <?php   wcmGUI::closeCollapsablePane();?>
 
 </div>
 