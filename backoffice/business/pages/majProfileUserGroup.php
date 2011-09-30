<?php
/**
 * Project:     WCM
 * File:        logicImmo_export.php
 *
 * @copyright   (c)2010 Nstein Technologies
 * @version     4.x
 *
 */

include(WCM_DIR . '/pages/includes/header.php');
wcmGUI::renderAssetBar('Maintenance', 'Profile to user/group Synchronization');

$config = wcmConfig::getInstance();
$project = wcmProject::getInstance();

// tableau entre les types de profils et les groupes correspondants
$accountToUserGroup = array(
'_ACCOUNT_PROFILE_SUPERVISOR' => '18',
'_ACCOUNT_PROFILE_MANAGER' => '17',
'_ACCOUNT_PROFILE_CLIENT' => '11',
'_ACCOUNT_PROFILE_DEMO' => '14');

$defaultAccountProfile = "_ACCOUNT_PROFILE_CLIENT";
?>
	
    <div class="genericForm">
    <form name='formSelect' id='formSelect'>
    <fieldset>
    <legend>Maintenance Action</legend>
    <br/>
    <table width="50%">
    <?php
    wcmGUI::renderHiddenField('todo', 'synchronize');
    ?>
    <tr><td align='right'>Exécuter la tâche de synchronisation</td><td><a href="javascript:$('formSelect').submit();" class="action" onClick="return confirm('Merci de confirmer cette action.')">VALIDER</a></td></tr>
    </table>
    </form>
    <br><br>
    <?php
	    if (getArrayParameter($_REQUEST, 'todo') == 'synchronize')
	    {
		    $account = new account();
		    
		    $erreurs = "";
		    
		    $account->beginEnum();
			while ($account->nextEnum())
			{
				if (!empty($account->wcmUserId))
				{
					$user = new wcmUser($project, $account->wcmUserId);
					
					// si le account profile n'existe pas, on en met u par défaut
					if (!isset($accountToUserGroup[$account->profile]))
					{
						$erreurs .= "<br/><span style='margin-left:10px'><b> erreur : ".$account->wcmUserId." ( ".$user->name." ) account profil inconnu : ".$account->profile." | mise à jour du profile avec la valeur ".$defaultAccountProfile." ! </b></span>";			
						// on update le account profile avec la valeur par défaut ($defaultAccountProfile) et on l'affecte au groupe correspondant !
						$account->refresh($account->id);
						$account->profile = $defaultAccountProfile;
						$account->save;
					}
					
					//$user->addToGroup($accountToUserGroup['9999']);
					//$user->addToGroup($accountToUserGroup[$account->profile]);
					//$user->save();
					
					echo "<span style='margin-left:10px'> User : ".$account->wcmUserId." ( ".$user->name." ) affecté au groupe : ".$accountToUserGroup[$account->profile]."</span><br>";				
				}
			}
			$account->endEnum();
			
			if (!empty($erreurs)) echo $erreurs;
	    }	    
    ?>
    </fieldset>  
</div>

<?php
include(WCM_DIR . '/pages/includes/footer.php');