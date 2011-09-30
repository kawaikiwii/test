<?php 
require_once (dirname(__FILE__).'/inc/wcmInit.php');
$session->logout();
header('Location: /');
?>
