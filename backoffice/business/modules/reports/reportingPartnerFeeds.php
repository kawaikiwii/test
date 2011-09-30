<?php
set_time_limit(0);
$date = date("Y-m-d");

$project = wcmProject::getInstance();

$config = wcmConfig::getInstance();
$enumSite = new site();
$nb_site = 0;
if ($enumSite->beginEnum("partnerfeeds=1")) {
	while ($enumSite->nextEnum()) {
		$nb_site++;
		if($nb_site == 1)
			$where = "univers=".$enumSite->id;
		else
			$where .= " OR univers=".$enumSite->id;
	}
	$enumSite->endEnum();
}
unset($enumSite);

$db_wcm = wcmProject::getInstance()->database;

$connector = $project->datalayer->getConnectorByReference("biz");
$db_biz = $connector->getBusinessDatabase();

$query = "SELECT DISTINCT accountid FROM #__accountPermission WHERE ".$where;
$result = $db_biz->ExecuteQuery($query);
$result->first();
$tab_user = array();
while($row = $result->getRow()) {
	$account_id = $row['accountid'];
	$query2 = "SELECT wcmuserid FROM #__account WHERE id=? AND (expirationdate IS NULL or expirationdate>?)";
	$params2 = array($account_id, $date);
	$result2 = $db_biz->ExecuteQuery($query2,$params2);
	$result2->first();
	if($row2 = $result2->getRow()) {
		$wcmuserid = $row2['wcmuserid'];
		$query3 = "SELECT name FROM wcm_user WHERE id=? AND isadministrator=0";
		$params3 = array($wcmuserid);
		$result3 = $db_wcm->ExecuteQuery($query3,$params3);
		$result3->first();
		if($row3 = $result3->getRow()) {
			$name = $row3['name'];
			$pos = strpos($name,"|");
			if($pos !== false)
				$name = substr($name,$pos+1)." ".substr($name,0,$pos);
			$tab_user[$wcmuserid] = trim($name);
		}
	}
	$continue = $result->next();
	if(!$continue)
		break;
}

asort($tab_user);
echo "<br><table cellspacing='1' cellpadding='3' border='0' bgcolor='#c0c0c0'>";
echo "<tr bgcolor='#f4f4f4'>";
echo "<td><b>"._BIZ_ACCOUNT_USER."</b></td>";
echo "<td><b>"._BIZ_CHAPTER_COMPANY."</b></td>";
echo "<td><b>"._BIZ_CREATION_DATE_ACCOUNT_USER."</b></td>";
echo "<td><b>"._BIZ_PARENT_ACCOUNT_USER."</b></td>";
echo "<td><b>"._MENU_SYSTEM_REPORTING_PARTNERFEEDS."</b></td>";
echo "</tr>";
foreach($tab_user as $wcmuserid=>$name) {
	echo "<tr bgcolor='#ffffff'>";
	echo "<td>".$name."</td>";
	$query = "SELECT id,companyname FROM #__account WHERE wcmuserid=?";
	$params = array($wcmuserid);
	$result = $db_biz->ExecuteQuery($query,$params);
	$result->first();
	if($row = $result->getRow()) {
		$id = $row['id'];
		$companyname = $row['companyname'];
		echo "<td>".$companyname."</td>";
	}
	$query = "SELECT createdat,createdby FROM wcm_user WHERE id=?";
	$params = array($wcmuserid);
	$result = $db_wcm->ExecuteQuery($query,$params);
	$result->first();
	if($row = $result->getRow()) {
		$createdat = $row['createdat'];
		$dateCreation = new DateTime($createdat);
		$createdat = $dateCreation->format('d/m/Y');
		
		$createdby = $row['createdby'];
		$query2 = "SELECT name FROM wcm_user WHERE id=?";
		$params2 = array($createdby);
		$result2 = $db_wcm->ExecuteQuery($query2,$params2);
		$result2->first();
		if($row2 = $result2->getRow()) {
			$name = $row2['name'];
			$pos = strpos($name,"|");
			if($pos !== false)
				$name = substr($name,$pos+1)." ".substr($name,0,$pos);
			$createdby = trim($name);
		}
		
		echo "<td>".$createdat."</td>";
		echo "<td>".$createdby."</td>";
	}
	$query = "SELECT DISTINCT title FROM #__accountPermission LEFT JOIN biz_site ON #__accountPermission.univers=#__site.id WHERE accountid=? AND partnerfeeds=1 ORDER BY title";
	$params = array($id);
	$result = $db_biz->ExecuteQuery($query,$params);
	$result->first();
	echo "<td>";
	while($row = $result->getRow()) {
		$title = $row['title'];
		echo $title."<br>";
		$continue = $result->next();
		if(!$continue)
			break;
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";