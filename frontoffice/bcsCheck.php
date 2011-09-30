<?php 
require_once (dirname(__FILE__).'/inc/wcmInit.php');

echo("APACHE: OK<br/>");

$mysql = parse_url($config['wcm.businessDB.connectionString']);
if (!mysql_connect($mysql["host"], $mysql["user"], $mysql["pass"])) {
    die("CONNECT TO BDD: FAILED<br/>");
}
echo("CONNECT TO BDD: OK<br/>");

if (!$session->startSession(wcmMembership::ROOT_USER_ID)) {
    die("CONNECT TO WCM: FAILED<br/>");
}
echo("CONNECT TO WCM: OK<br/>");
$session->logout();
?>
