<?php
$connect = new PDO("mysql:host=10.23.65.201; dbname=RELAX_BIZ", "relaxweb", "kzq!2007");
foreach($connect->query("SELECT managerPathId FROM biz_account where (profile = '_ACCOUNT_PROFILE_SUPERVISOR' OR profile = '_ACCOUNT_PROFILE_MANAGER')") as $row) {
	$managers = explode('-',$row["managerPathId"]);
	foreach($managers as $manager){
		
		//ManagerPathId contient les id WCM !!! On recherche donc L'id BIZ à partir de l'id WCM, car la table biz_accountPermission se base sur cet id (BIZ)
		foreach($connect->query("SELECT id FROM biz_account WHERE wcmUserId = ".$manager) as $id){
			$accountId = $id["id"];
		}
		//On check si le user a les droits d'accès à NowFashion EN
		$test = $connect->query("SELECT univers, service, rubrique FROM biz_accountPermission WHERE accountId = ".$accountId." AND service = 'slideshow' AND univers = 16 AND rubrique = 291" );
		//compte du nb de résultat
		$result = $test->fetchAll();
		//si on ne trouve pas le compte associé aux droits nowfashion dans biz_accoutPermission, on ajoute ces droits 
		if(count($result) == 0){
			$sth = $connect->prepare("INSERT INTO biz_accountPermission(accountId,service,univers,rubrique) VALUES(:accountid,'slideshow',16,291)");
			$sth->execute(array(':accountid' => $accountId));		

		}
		//On fait la meme pour NowFashion FR
		$test = $connect->query("SELECT univers, service, rubrique FROM biz_accountPermission WHERE accountId = ".$accountId." AND service = 'slideshow' AND univers = 17 AND rubrique = 290" );
		//compte du nb de résultat
		$result = $test->fetchAll();
		//si on ne trouve pas le compte associé aux droits nowfashion dans biz_accoutPermission, on ajoute ces droits 
		if(count($result) == 0){
			$sth = $connect->prepare("INSERT INTO biz_accountPermission(accountId,service,univers,rubrique) VALUES(:accountid,'slideshow',17,290)");
			$sth->execute(array(':accountid' => $accountId));		

		}

		echo "<br /><br /><br />";	
	}
}
?>
