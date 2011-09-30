<?php

/**
 * Project:     WCM
 * File:        biz.delsubscription.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by an Ajax call, It returns a the update content for the channel select
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve (some) parameters
$newsletter_id = getArrayParameter($_REQUEST, "newsletter_id", null);
$subscriptionStart = getArrayParameter($_REQUEST, "subscriptionStart", null);
$subscriptionEnd = getArrayParameter($_REQUEST, "subscriptionEnd", null);
$webuser_id = getArrayParameter($_REQUEST, "webuser_id", null);

    
$subscription = new subscription;
$subscription->webuserId = $webuser_id;
$subscription->subscribedId = $newsletter_id;
$subscription->subscribedClass = "newsletter";
$subscription->subscriptionEnd = $subscriptionEnd;
$subscription->subscriptionStart = $subscriptionStart;
$subscription->save();
    
// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// XML output
header("Content-Type: text/html");

?>
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
    $subscription->beginEnum(null, "subscriptionStart", null, null, array('newsletter' => $newsletter_id));
    while ($subscription->nextEnum())
    {
        $subscriber = $subscription->getWebuser();
?>
    <tr id="_nlsub_<?php echo $subscription->id;?>">
        <td class="actions">
            <ul>
                <li><a href="#" onclick="new Ajax.Request('<?php echo $config['wcm.backOffice.url'];?>/business/ajax/biz.delsubscription.php', { metdod:'get', parameters: { newsletter_id: <?php echo $newsletter_id;?>, subscription_id: <?php echo $subscription->id;?>}, onSuccess: function(transport){ if(transport.responseText == 1){$('_nlsub_<?php echo $subscription->id;?>').hide()} }} );">Delete</a></li>
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
