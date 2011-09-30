<?php
/*
 * Project:     WCM
 * File:        relaxTask.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('business/relaxTask', array('class' => 'relaxTask'));
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    // Include header and menu
    include(WCM_DIR . '/pages/includes/header.php');

    echo '<div id="content" style="margin-left:0px">';
	$tabs = new wcmAjaxTabs('relaxTask', true);
	$tabs->addTab('t1', 'Taches', true, null, wcmModuleURL('business/export/relaxTask/relaxTask'));
        $tabs->render();
    echo '</div>';

?>
<script type='text/javascript' defer='defer'>
	
	increment = function()
	{
	var id = $('counter');
		if (id)
		{
			id.innerHTML = parseInt(id.innerHTML) + 1;
		}
		setTimeout("increment()",1000);
	}
	
	setTimeout("increment()",1000);
</script>
<?php
    include(WCM_DIR . '/pages/includes/footer.php');
    
