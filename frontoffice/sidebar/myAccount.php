<h2><?php echo _SIDEBAR_MYACCOUNT_MYIDENTITY ?></h2>
<p><b><?php echo _SIDEBAR_MYACCOUNT_NAME ?> :</b> <?php echo $CURRENT_USER->name ?></p>
<p><b><?php echo _SIDEBAR_MYACCOUNT_LOGIN ?> :</b> <?php echo $CURRENT_USER->login ?></p>
<p><b><?php echo _SIDEBAR_MYACCOUNT_EMAIL ?> :</b> <?php echo $CURRENT_USER->email ?></p>

<h2><?php echo _SIDEBAR_MYACCOUNT_MYSUPERVISOR ?></h2>
<p><b><?php echo _SIDEBAR_MYACCOUNT_NAME ?> :</b> <?php echo getConst($CURRENT_ACCOUNT_MANAGER->name) ?></p>
<p><b><?php echo _SIDEBAR_MYACCOUNT_EMAIL ?> :</b> <a href="mailto:<?php echo $CURRENT_ACCOUNT_MANAGER->email ?>"><?php echo $CURRENT_ACCOUNT_MANAGER->email ?></a></p>

<?php
if ($CURRENT_USER->isAdministrator||(($CURRENT_ACCOUNT->isManager() || $CURRENT_ACCOUNT->isChiefManager())&&($CURRENT_ACCOUNT->expirationDate >= date('Y-m-d') || $CURRENT_ACCOUNT->expirationDate == '')))
	{?>
	<h2><?php echo _SIDEBAR_MYACCOUNT_ACCESS_TO_BO ?></h2>
		<p><b><a href="http://bo.afprelax.net" target="_blank" title="<?php echo _SIDEBAR_MYACCOUNT_ACCESS_TO_BO ?>">http://bo.afprelax.net</a></p>
	<?php
		
	}
?>

<h2>&nbsp;</h2>
<form id="logoutform" action="/log/out/" style="margin-top:12px;" method="POST"><input type="hidden" name="lang" value="<?php echo $session->getLanguage(); ?>" /><input type="hidden" name="action" value="logout" /><input type="submit" value="Logout" /></form>