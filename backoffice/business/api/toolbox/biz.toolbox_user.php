<?php
/**
 * Generic functions to authenticate against the webuser database or otherwise
 * 
 */


/**
 * Simple function to create a user with the least amount of information possible.
 * 
 * This requires a username, password and email. If $argConfirm is set to true
 * then the user status is seting to webuser::STATUS_WAITING automatically. If a
 * username is the same as an email address, just supply the email address for
 * both username and email.
 *
 * @param string $argUser Username
 * @param string $argPass Password
 * @param string $argEmail Email Address
 * @param bool $argConfirm Whether or not user needs to be validated first.
 * @return int The new ID created.
 */
function basicCreateUser($argUser, $argPass, $argEmail, $argConfirm = false)
{
    $project    = wcmProject::getInstance();
    $connector  = $project->datalayer->getConnectorByReference("biz");
    $businessDB = $connector->getBusinessDatabase();

    $wu = new webuser;
    $wu->username = $argUser;
    $wu->email = $argEmail;
    $wu->setPassword($argPass);
    
    if ($argConfirm)
    {
        $wu->workflowState = webuser::STATUS_WAITING;
    } else {
        $wu->workflowState = webuser::STATUS_VALID;
    }
    $wu->store();
    
    return $wu->id;
}

/**
 * Logs in a user of a specific ID, setting all necessary session variables.
 *
 * @param int $argId User ID
 */
function loginUser($argId)
{
    $project = wcmProject::getInstance();
    $_SESSION['wcm']['auth'] = true;
    $_SESSION['wcm']['user'] = new webuser($project, $argId);
    $_SESSION['wcm']['user']->loggedIn();
}

/**
 * Log out the currently logged in user by resetting the appropriate session variables
 *
 */
function logoutUser()
{
    unset($_SESSION['wcm']['user']);
    unset($_SESSION['wcm']['auth']);
}

/**
 * This will email a user with a specific newsletter template
 *
 * The newsletter will be prepopulated with a webuser bizobject created
 * from $argUser, and it will be prepopulated with the newsletter bizobject
 * created from $newsletterCode. You can put in your own parameters by
 * populating the $argParams array.
 * 
 * This function will handle creating a MIME email with both plain text
 * and HTML messages, as well as creating all appropriate headers.
 * 
 * Returned is a message array in the following format:
 * $msg['subject'] = Newsletter Subject
 * $msg['to'] = Webuser email address
 * $msg['toName'] = Webuser username
 * $msg['headers'] = A string containing all the necessary email headers
 * $msg['message'] = The body of the entire message, divided into plain text and HTML emails
 * $msg['from'] = The from email address from the newsletter
 * $msg['fromName'] = The name of the from email address from the newsletter
 * $msg['htmlMsg'] = Just the HTML msg
 * $msg['textMsg'] = just the plain text msg
 * 
 * @param mixed  $argUser Either a webuser id or a webuser bizobject
 * @param string $newsletterCode Code of newsletter to use
 * @param array $argParams Extra parameters given to the template
 * 
 * @return array An assoc array
 */
function emailUser($argUser, $newsletterCode, array $argParams = array())
{
    
    $wu = (is_numeric($argUser))? new webuser(null, $argUser) : $argUser; 
    
    $newsletter = new newsletter();
    $newsletter->refreshByCode($newsletterCode);
    
    $params['user'] =& $wu;
    $params['newsletter'] =& $newsletter;
    
    $params = array_merge($params,$argParams);
    
    $gen = new wcmTemplateGenerator(null, false);
    $textEmail = $gen->executeTemplate($newsletter->textTemplate, $params);
    $htmlEmail = $gen->executeTemplate($newsletter->htmlTemplate, $params);
    
    $boundary = "=WCM_".md5(mt_rand()).".WCM0";
    $headers = "From: ".$newsletter->sender." <".$newsletter->from.">\r\n";
    $headers .= "Reply-To: ".$newsletter->replyTo."\r\n";
    $headers .= "Return-Path: ".$newsletter->from."\r\n";
    $headers .= "Message-ID: <".time().rand()."@".$_SERVER['SERVER_NAME'].">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: multipart/alternative; boundary=\"{$boundary}\"\r\n\r\n";
    
    $message = "This is a multipart mime email address.\r\n";
    $message .= "--".$boundary."\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $textEmail."\r\n";
    $message .= "--".$boundary."\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $htmlEmail."\r\n";
    $message .= "--".$boundary."\r\n";
    
    $subject = $newsletter->title;
    
    $msg['subject'] = $subject;
    $msg['to'] = $wu->email;
    $msg['toName'] = $wu->username;
    $msg['headers'] = $headers;
    $msg['message'] = $message;
    $msg['from'] = $newsletter->from;
    $msg['fromName'] = $newsletter->sender;
    $msg['htmlMsg'] = $htmlEmail;
    $msg['textMsg'] = $textEmail;
    
    return $msg;
}

/**
 * Checks to see if a username/password is valid in the biz_webuser table.
 *
 * @param string $argUser Username
 * @param string $argPass Password
 * @return int The webuser ID OR false if failed to authenticate.
 */
function basicAuthenticate($argUser, $argPass)
{
    if ($id = webuser::checkCredentials($argUser, $argPass))
    {
        return $id;
    } else {
        return false;
    }
}

/**
 * Determins if a user is logged in right now
 *
 * @return bool True if logged in, false if not.
 */
function isLoggedIn()
{
    return (isset($_SESSION['wcm']['auth']))? true : false;
}

/**
 * Returns a webuser bizobject of the currently logged in user
 *
 * @return object Webuser bizobject
 */
function getCurrentUser()
{
    return (isset($_SESSION['wcm']['user']))? $_SESSION['wcm']['user'] : false;
}

/**
 * Checks to see if a username already exists in the database.
 *
 * @param string $argUsername Username to check
 * @return int ID of the user who has that username or false if username doesn't exist
 */
function usernameExists($argUsername)
{
    $project    = wcmProject::getInstance();
    $connector  = $project->datalayer->getConnectorByReference("biz");
    $db = $connector->getBusinessDatabase();
    
    $query = 'SELECT id FROM biz_webuser WHERE username=?';
    $id = (int) $db->executeScalar($query, array($argUsername));
    return ($id !== false)? $id : false;
}
?>