<?php
/**
 * Project:     M
 * File:        modules/ugc/webuser/subscriptions.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $sysobject = wcmMVC_Action::getContext();
  	$config = wcmConfig::getInstance();
  
  	echo '<div class="zone">';
  	wcmGUI::openCollapsablePane(_BIZ_SUBSCRIPTIONS);
    wcmGUI::openFieldset(_BIZ_NEWSLETTER);
    
  	?>
  	<div id = "subscriptions" class="backup list">
    <table>
    <tr id="head">
        <th class="actions"></th>
        <th class=""><?php echo _BIZ_NEWSLETTER;?></th>
        <th class=""><?php echo _BIZ_SUBSCRIPTION_START;?></th>
        <th class=""><?php echo _BIZ_SUBSCRIPTION_END;?></th>
    </tr>
    
    <?php
        
    $subscription = new subscription;
    
    $where = 'webuserId='.$sysobject->id;
    $subscription->beginEnum($where, "subscriptionStart", null, null, null);
    while ($subscription->nextEnum())
    {
    	$newsletter = $subscription->getSource();
    ?>
    
    <tr id="_nlsub_<?php echo $subscription->id;?>">
    	<td class="actions">
    		<ul>
                <li><a href="#" onclick="new Ajax.Request('<?php echo $config['wcm.backOffice.url'];?>/business/ajax/biz.delsubscription.php', { metdod:'get', parameters: { newsletter_id: <?php echo $newsletter->id;?>, subscription_id: <?php echo $subscription->id;?>}, onSuccess: function(transport){ if(transport.responseText == 1){$('_nlsub_<?php echo $subscription->id;?>').hide()} }} );"><?php echo _BIZ_DELETE; ?></a></li>
            </ul>
    	</td>
        <td class=""><?php echo '<a href="'.$config['wcm.backOffice.url'].'?_wcmAction=business/newsletter&id='.$newsletter->id.'">'.$newsletter->title.'</a>';?></td>
        <td class=""><?php echo $subscription->subscriptionStart;?></td>
        <td class=""><?php echo $subscription->subscriptionEnd;?></td>
    </tr>
    
    <?php
    	
    }
    
    echo '</table></div>';
  	wcmGUI::closeCollapsablePane();
  	
    
    wcmGUI::openCollapsablePane(_GENERAL);
    wcmGUI::openFieldset(_PROPERTIES);
    
	wcmGUI::renderDateField('subscriptionStart', null, _BIZ_SUBSCRIPTION_START, array('class' => 'type-date'));
	wcmGUI::renderDateField('subscriptionEnd', null, _BIZ_SUBSCRIPTION_END, array('class' => 'type-date'));
	// @todo = subscription types wcmGUI::renderDropDownField('subscriptionType', $subscription_type, null, _BIZ_SUBSCRIPTION_TYPE);
	wcmGUI::renderHiddenField('subscribedClass', 'subscribedClass');
	wcmGUI::renderHiddenField('webuser', $sysobject->id);
	/* TODO wcmGUI::renderButton('searchForBizclass', _BIZ_SEARCH_OBJECTS); */
	/* TODO wcmGUI::renderButton('saveSubscription', _BIZ_SUBSCRIPTION_BUTTON_ADD); */
	//wcmGUI::renderTextField('subscribedId', null, _BIZ_SUBSCRIPTION_VALUE);
	
	$subscription = new subscription;
    
    $newsletters_obj = newsletter::getBizobjects("newsletter"); 
	$newsletters = array();
	foreach($newsletters_obj as $newsletter)
		$newsletters[$newsletter->id] = $newsletter->title;
    
	wcmGUI::renderDropdownField("newsletter_field_id", $newsletters, null, _BIZ_SUBSCRIPTION_VALUE, array("id"=>"newsletter_field_id"));
	?>
    <ul>
        <li><a href="#" onclick="wcmCheckProperties()"><?php echo _BIZ_SUBSCRIPTION_ADD;?></a></li>
    </ul>
	
	<?php
	/* TODO wcmGUI::renderButton('save', _BIZ_SUBSCRIPTION_VALUE); */
	wcmGUI::closeCollapsablePane();
	echo '</div>';