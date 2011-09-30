<?php
/**
 * Project:     WCM
 * File:        modules/editorial/newsletter/subscribers.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();   
    $config = wcmConfig::getInstance();

    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_BIZ_NEWSLETTER_EXISTING_SUBSCRIBERS);
?>
<div id = "subscribers" class="backup list">
    <table>
    <tr id="head">
        <th class="actions"></th>
        <th class=""><?php echo _WEB_EMAIL;?></th>
        <th class=""><?php echo _BIZ_SUBSCRIPTION_START;?></th>
        <th class=""><?php echo _BIZ_SUBSCRIPTION_END;?></th>
        <th class=""><?php echo _BIZ_FIRSTNAME;?></th>
        <th class=""><?php echo _BIZ_LASTNAME;?></th>
    </tr>
<?php    
    $subscription = new subscription;
    $subscription->beginEnum(null, "subscriptionStart", null, null, array('newsletter' => $bizobject->id));
    while ($subscription->nextEnum())
    {
        $subscriber = $subscription->getWebuser();
?>
    <tr id="_nlsub_<?php echo $subscription->id;?>">
        <td class="actions">
            <ul>
                <li><a href="#" onclick="new Ajax.Request('<?php echo $config['wcm.backOffice.url'];?>/business/ajax/biz.delsubscription.php', { metdod:'get', parameters: { newsletter_id: <?php echo $bizobject->id;?>, subscription_id: <?php echo $subscription->id;?>}, onSuccess: function(transport){ if(transport.responseText == 1){$('_nlsub_<?php echo $subscription->id;?>').hide()} }} );"><?php echo _BIZ_DELETE; ?></a></li>
            </ul>
        </td>
        <td><?php echo '<a href="'.$config['wcm.backOffice.url'].'?_wcmAction=business/webuser&id='.$subscriber->id.'">'.$subscriber->email.'</a>';?></td>
        <td class=""><?php echo $subscription->subscriptionStart;?></td>
        <td class=""><?php echo $subscription->subscriptionEnd;?></td>
        <td class=""><?php echo $subscriber->firstname;?></td>
        <td class=""><?php echo $subscriber->lastname;?></td>
    </tr>
<?php
        
    }
    $subscription->endEnum();
?>
    </table>
</div>
<?php
    wcmGUI::closeCollapsablePane();
    echo '</div>';