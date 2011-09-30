<?php
include(WCM_DIR.'/initWebApp.php');
include(WCM_DIR.'/pages/includes/head.php');
$config = wcmConfig::getInstance();
$session = wcmSession::getInstance();
$command = getArrayParameter($params, 'command', null);

wcmMVC_Action::execute('business/personality', array('class' => 'personality'));
$personality = wcmMVC_Action::getContext();

$saveAction = wcmModuleURL('business/subForms/createPersonality',array('command' => 'savePersonality', 'uid' => $params['uid']));
?>
<script type="text/javascript">

<?php if (isset($params['personalityId'])): ?>
lmgr = parent.relationSearch.linkMgr._object.<?php echo $params['uid']; ?>;
lmgr.addRelationManual('<?php echo $params['personalityId']; ?>','personality');
<?php endif; ?>

</script>
<div id="subform">
<?php
  
	wcmGUI::openFieldset(strtoupper(_BIZ_PLACE)." FORM");
	wcmGUI::openObjectForm($personality);
		
    wcmGUI::renderHiddenField('cancel',null);
    wcmGUI::renderHiddenField('_wcmClass',$personality->getClass());
    wcmGUI::renderHiddenField('wcm_Todo','Save');
    wcmGUI::renderHiddenField('_redirect', $params['uid']);
        
    wcmGUI::renderTextField('firstName', 	null, 	_BIZ_LOCATION_FIRSTNAME, 	array('style'=>'width:250px'));
	wcmGUI::renderTextField('lastName', 	null, 	_BIZ_LOCATION_LASTNAME, 	array('style'=>'width:250px'));
	wcmGUI::renderTextField('job', 			null, 	_BIZ_LOCATION_JOBTITLE, 	array('style'=>'width:250px'));
	?>
    <input class="btSearch" type="button" onclick="window.location=window.location" value="<?php echo _CANCEL;?>">
    <input class="btSearch" type="button" onclick="if (document.getElementById('lastName').value  == ''){alert('<?php echo _BIZ_EMPTY_LASTNAME;?>')}else{wcmActionController.triggerEvent('save',{})};" value="<?php echo _SAVE;?>">
    <?php
    wcmGUI::closeFieldset();
	wcmGUI::closeForm();
	   
?>
</div>
