<?php

require_once dirname(__FILE__).'/../../../../initWebApp.php';

$config = wcmConfig::getInstance();
$id     = getArrayParameter($_REQUEST, "id", 0);
$taskId     = getArrayParameter($_REQUEST, "taskId", 0);
//echo $id;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $config['wcm.backOffice.url'];?>skins/default/css/treeview.css" />
</head>
<body>
<script language='JavaScript' type='text/javascript' src='<?php echo $config['wcm.backOffice.url'];?>includes/js/dhtmltreenode.js'></script>
	<!--  <a href="javascript:void(0);" onclick="alert(tree2.getAllChecked());">[Show values]</a><br />-->
	<div id='treeboxbox_tree2' style='width:340px;background-color:#ffffff;overflow:auto;position:relative; float:left'></div>
	<script>
			tree2=new dhtmlXTreeObject('treeboxbox_tree2','100%','100%',0);
			tree2.setImagePath('/img/treeImgs/');
			//tree2.enableTreeImages(false); 
			tree2.enableCheckBoxes(1);
			tree2.enableThreeStateCheckboxes(true);
			
			tree2.loadXML('/business/modules/ugc/account/xmlTreeAlerts.php?id=<?php echo $id;?>&taskId=<?php echo $taskId;?>');			
	</script>
</body>
</html>
