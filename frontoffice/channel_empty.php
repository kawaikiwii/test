<?php
require_once (dirname(__FILE__).'/inc/wcmInit.php');
if (!(isset($session->userId) && $session->userId)) {
    exit();
}
try {
	$connect = new PDO("mysql:host=10.23.65.201; dbname=RELAX_BIZ", "relaxweb", "kzq!2007");
	$cpt = 0;
	$afpen = false;
	$afpfr = false;
	$fil = false;
	foreach($connect->query("SELECT id,title,siteId,publicationDate FROM biz_news WHERE workflowState='published' AND (channelId IS NULL OR channelId='') AND (import_feed<>'icm' OR import_feed IS NULL) ORDER BY siteId,publicationDate DESC") as $row) {
        $cpt++;
		if($cpt == 1)
        	echo "<h3>News sans cat&eacute;gorie principale</h3>";
		if(!$afpen && $row["siteId"] == 4) {
			echo "<h5>AFPRelax EN</h5>";
			$afpen = true;
		}
		if(!$afpfr && $row["siteId"] == 5) {
			echo "<h5>AFPRelax FR</h5>";
			$afpfr = true;
		}
		if(!$fil && $row["siteId"] == 6) {
			echo "<h5>Relaxfil</h5>";
			$fil = true;
		}
		echo "<a href='http://bo.relaxnews.net/index.php?_wcmAction=business/news&id=".$row["id"]."' target='_blank'>".$row["title"]."</a> ".$row["publicationDate"]."<br />";
    }
	$cpt = 0;
	$afpen = false;
	$afpfr = false;
	$fil = false;
	foreach($connect->query("SELECT id,title,siteId,publicationDate FROM biz_notice WHERE workflowState='published' AND (channelId IS NULL OR channelId='') AND (import_feed<>'icm' OR import_feed IS NULL) ORDER BY siteId,publicationDate DESC") as $row) {
        $cpt++;
		if($cpt == 1)
        	echo "<h3>Notices sans cat&eacute;gorie principale</h3>";
		if(!$afpen && $row["siteId"] == 4) {
			echo "<h5>AFPRelax EN</h5>";
			$afpen = true;
		}
		if(!$afpfr && $row["siteId"] == 5) {
			echo "<h5>AFPRelax FR</h5>";
			$afpfr = true;
		}
		if(!$fil && $row["siteId"] == 6) {
			echo "<h5>Relaxfil</h5>";
			$fil = true;
		}
		echo "<a href='http://bo.relaxnews.net/index.php?_wcmAction=business/notice&id=".$row["id"]."' target='_blank'>".$row["title"]."</a> ".$row["publicationDate"]."<br />";
    }
    $cpt = 0;
	$afpen = false;
	$afpfr = false;
	$fil = false;
	foreach($connect->query("SELECT id,title,siteId,publicationDate FROM biz_event WHERE workflowState='published' AND (channelId IS NULL OR channelId='') ORDER BY siteId,publicationDate DESC") as $row) {
        $cpt++;
		if($cpt == 1)
        	echo "<h3>Ev&eacute;nements sans cat&eacute;gorie principale</h3>";
		if(!$afpen && $row["siteId"] == 4) {
			echo "<h5>AFPRelax EN</h5>";
			$afpen = true;
		}
		if(!$afpfr && $row["siteId"] == 5) {
			echo "<h5>AFPRelax FR</h5>";
			$afpfr = true;
		}
		if(!$fil && $row["siteId"] == 6) {
			echo "<h5>Relaxfil</h5>";
			$fil = true;
		}
		echo "<a href='http://bo.relaxnews.net/index.php?_wcmAction=business/event&id=".$row["id"]."' target='_blank'>".$row["title"]."</a> ".$row["publicationDate"]."<br />";
    }
    $cpt = 0;
	$afpen = false;
	$afpfr = false;
	$fil = false;
	foreach($connect->query("SELECT id,title,siteId,publicationDate FROM biz_forecast WHERE workflowState='published' AND (channelId IS NULL OR channelId='') ORDER BY siteId,publicationDate DESC") as $row) {
        $cpt++;
		if($cpt == 1)
        	echo "<h3>Prévisions sans cat&eacute;gorie principale</h3>";
		if(!$afpen && $row["siteId"] == 4) {
			echo "<h5>AFPRelax EN</h5>";
			$afpen = true;
		}
		if(!$afpfr && $row["siteId"] == 5) {
			echo "<h5>AFPRelax FR</h5>";
			$afpfr = true;
		}
		if(!$fil && $row["siteId"] == 6) {
			echo "<h5>Relaxfil</h5>";
			$fil = true;
		}
		echo "<a href='http://bo.relaxnews.net/index.php?_wcmAction=business/forecast&id=".$row["id"]."' target='_blank'>".$row["title"]."</a> ".$row["publicationDate"]."<br />";
    }
    $cpt = 0;
	$afpen = false;
	$afpfr = false;
	$fil = false;
	foreach($connect->query("SELECT id,title,siteId,publicationDate FROM biz_slideshow WHERE workflowState='published' AND (channelId IS NULL OR channelId='') ORDER BY siteId,publicationDate DESC") as $row) {
        $cpt++;
		if($cpt == 1)
        	echo "<h3>Diaporamas sans cat&eacute;gorie principale</h3>";
		if(!$afpen && $row["siteId"] == 4) {
			echo "<h5>AFPRelax EN</h5>";
			$afpen = true;
		}
		if(!$afpfr && $row["siteId"] == 5) {
			echo "<h5>AFPRelax FR</h5>";
			$afpfr = true;
		}
		if(!$fil && $row["siteId"] == 6) {
			echo "<h5>Relaxfil</h5>";
			$fil = true;
		}
		echo "<a href='http://bo.relaxnews.net/index.php?_wcmAction=business/slideshow&id=".$row["id"]."' target='_blank'>".$row["title"]."</a> ".$row["publicationDate"]."<br />";
    }
    $connect = null;
}
catch(PDOException $e) {
	echo("Connexion à la base de données RELAX_BIZ impossible : ".$e->getMessage()."\r\n");
	$connect = null;
}
?>