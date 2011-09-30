<?php
include(WCM_DIR.'/initWebApp.php');
include(WCM_DIR.'/pages/includes/head.php');
$config = wcmConfig::getInstance();
$session = wcmSession::getInstance();
$command = getArrayParameter($params, 'command', null);

wcmMVC_Action::execute('business/contact', array('class' => 'contact'));
$contact = wcmMVC_Action::getContext();

$saveAction = wcmModuleURL('business/subForms/createContact',array('command' => 'saveContact', 'uid' => $params['uid']));
?>
<script type="text/javascript">

<?php if (isset($params['contactId'])): ?>
lmgr = parent.relationSearch.linkMgr._object.<?php echo $params['uid']; ?>;
lmgr.addRelationManual('<?php echo $params['contactId']; ?>','contact');
<?php endif; ?>

</script>
<div id="subform">
<?php

	wcmGUI::openFieldset(strtoupper(_BIZ_CONTACT)." FORM");
	wcmGUI::openObjectForm($contact);
		
    wcmGUI::renderHiddenField('cancel',null);
    wcmGUI::renderHiddenField('_wcmClass',$contact->getClass());
    wcmGUI::renderHiddenField('wcm_Todo','Save');
    wcmGUI::renderHiddenField('_redirect', $params['uid']);
        
    wcmGUI::renderTextField('title', 	null, 	_BIZ_CONTACT, 	array('style'=>'width:250px'));
	wcmGUI::renderTextArea('address', 	null, 	_BIZ_ADDRESS, 	array('rows'=>3, 'style'=>'width:250px'));
	wcmGUI::renderTextField('phone', 	null, 	_BIZ_PHONE, 	array('style'=>'width:250px'));
	wcmGUI::renderTextField('email', 	null, 	_BIZ_EMAIL, 	array('style'=>'width:250px'));
	wcmGUI::renderTextField('website', 	null, 	_BIZ_WEBSITE, 	array('style'=>'width:250px'));
	wcmGUI::renderTextField('facebook', null, 	"Facebook", 	array('style'=>'width:250px'));
	?>
    <input class="btSearch" type="button" onclick="window.location=window.location" value="<?php echo _CANCEL;?>">
    <input class="btSearch" type="button" onclick="if (document.getElementById('title').value  == ''){alert('<?php echo _BIZ_EMPTY_CONTACT;?>')}else{wcmActionController.triggerEvent('save',{})};" value="<?php echo _SAVE;?>">
    <?php
    wcmGUI::closeFieldset();
	wcmGUI::closeForm();
	
?>
</div>
