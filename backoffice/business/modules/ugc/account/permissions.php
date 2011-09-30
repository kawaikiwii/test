<?php

require_once dirname(__FILE__).'/../../../../initWebApp.php';

$config = wcmConfig::getInstance();
$id     = getArrayParameter($_REQUEST, "id", 0);
//echo $id;
/*$account = new account();
$account->refresh($id);
print_r($account->getArrayPermissions());*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config['wcm.backOffice.url'];?>skins/default/css/treeview.css" />
<?php include(WCM_DIR . '/js/main.js.php'); ?>
</head>
<body>
<script language='JavaScript' type='text/javascript' src='<?php echo $config['wcm.backOffice.url'];?>includes/js/dhtmltreenode.js'></script>
	
	<div id='treeboxbox_tree2' style='width:390; height:520;background-color:#ffffff;overflow:auto;position:relative; float:left'></div>
	<div id='treeboxbox_action' style='margin-left:20px;width:230; height:520;background-color:#ffffff;position:relative; float:left;font-family : Verdana, Geneva, Arial, Helvetica, sans-serif; font-size : 11px;'>
	<b>ACTIONS</b><br /><hr><br />
	<a href="javascript:void(0);" onclick="tree2.openAllItems(0);"><img src="/img/treeImgs/folder_down.png" border=0 align="middle"> <?php echo _BIZ_EXPAND_ALL;?></a><br><br>
	<a href="javascript:void(0);" onclick="tree2.closeAllItems(0);"><img src="/img/treeImgs/folder_up.png" border=0 align="middle"> <?php echo _BIZ_COLLAPSE_ALL;?></a><br><br>
	<a href="javascript:void(0);" onclick="tree2.openAllItems(tree2.getSelectedItemId());"><img src="/img/treeImgs/folder_add.png" border=0 align="middle"> <?php echo _BIZ_EXPAND_SELECTION;?></a><br><br>
	<a href="javascript:void(0);" onclick="tree2.closeAllItems(tree2.getSelectedItemId());"><img src="/img/treeImgs/folder_remove.png" border=0 align="middle"> <?php echo _BIZ_COLLAPSE_SELECTION;?></a><br><br>
	<!-- <a href="javascript:void(0);" onclick="alert(tree2.getAllChecked());">[Show values]</a><br><br> -->
	<hr><br />
	<ul class="toolbar">
	<li><a class="cancel" onclick="window.parent.closemodal();return false;" href="#"><?php echo _BIZ_CLOSE;?></a></li>
	<li><a class="save" onclick="window.parent.saveTreeAccountPermissions(<?php echo $id;?>, tree2.getAllChecked());window.parent.closemodal();return false;" href="#"><?php echo _BIZ_SAVE;?></a></li>
	</ul>
		
	</div>
	<script>
			tree2=new dhtmlXTreeObject('treeboxbox_tree2','100%','100%',0);
			tree2.setImagePath('/img/treeImgs/');
			//tree2.enableTreeImages(false); 
			tree2.enableCheckBoxes(1);
			tree2.enableThreeStateCheckboxes(true);
			
			tree2.loadXML('/business/modules/ugc/account/xmlTree.php?id=<?php echo $id;?>');
	</script>

</body>
</html>
