<?php 
// Initialize WCM API
require_once (dirname(__FILE__).'/../inc/wcmInit.php');
if (!($session->userId)) {
    header("location: /");
    exit();
}
$site = $session->getSite();
$session->setLanguage($site->language);
// Retrieve FO Language Pack
require_once (dirname(__FILE__)."/../sites/".$site->code."/conf/lang.php");

$Err = "";

if (trim($_POST['first'] == "")) {
    $Err = 'first:"Your must supply a first name",';
}

if (trim($_POST['last'] == "")) {
    $Err = 'last:"Your must supply a last name",';
}

if (trim($_POST['email'] == "")) {
    $Err = 'first:"Your must supply an email",';
}

$password = "";

if (isset($_POST['passwordOld']) && trim($_POST['passwordOld'] != "")) {

    if (md5(trim($_POST['passwordOld'])) != $CURRENT_USER->password) {
        $Err = 'passwordOld:"Your password is not valid",';
    } else {
        if (trim($_POST['passwordNew']) != trim($_POST['passwordReNew'])) {
            $Err = 'passwordReNew:"Your password is not valid",';
        } else {
            $password = trim($_POST['passwordNew']);
        }
    }
    
}

if ($Err == "") {

    $CURRENT_USER->refresh();
    $CURRENT_USER->name = trim($_POST['first'])."|".strtoupper(trim($_POST['last']));
    $CURRENT_USER->email = trim($_POST['email']);
    if ($password != "") {
        $CURRENT_USER->password = md5($password);
    }
    $CURRENT_USER->save();
    $result = "{success: true}";
    
} else {

    $result = '{success: false, errors:{'.$Err.'} }'; // Return the error message(s)
}
echo $result;
?>
