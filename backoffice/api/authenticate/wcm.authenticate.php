<?php
/**
 * Project:     WCM
 * File:        wcmAuthenticate.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */


/**
 * Factory
 */
class wcmAuthenticate
{
    /**
     * wcmSession object
     */
    private $session;

    /**
     *
     */
    public function __construct(wcmSession $session)
    {
        $this->session = $session;
    }

    /**
     * 	@author modified by M.Bully for account expiration in wcm case
     *  @param String $username         Username of the user
     *  @param String $password         Password of the user
     *  @param String $protocol         login protocol
     *  @param Array  $protocol_extras  (optional) array with string to connection to protocol
     *
     *  @return string              false on success / error message on failed
     */
    public function login($protocol, $username, $password, $protocol_extras = null)
    {
        switch ($protocol)
        {
            case 'ldap':
                require_once('wcm.ldap.php');
                $ldap = new wcmLdap($protocol_extras['host'], $protocol_extras['port'], $protocol_extras['basedn']);

                list($success, $userInfo) = $ldap->login($username, $password);
                
                if ($success)
                {
                    // Verify if the user exist into wcm else, it create it.
                    $this->addWcmUser($protocol, $username, $password, $userInfo);

                    // Login into the wcm
                    $this->login('wcm', $username, $password);
                }
                else
                    wcmMVC_Action::setWarning($info);
                break;
                
            case 'openid':
            
                require_once('wcm.openId.php');
                $openId = new wcmOpenId();
                $url = (isset($protocol_extras['openIdUrl'])) ? $protocol_extras['openIdUrl'] : null;
                if(isset($protocol_extras['action']))
                {
                    list($success, $userInfo) = $openId->login($url, $protocol_extras['action']);
                    if ($success)
                    { 
                        // We generate a password cause we can't get the one from openId.
                        $password  = uniq_id();

                        // Verify if the user exist into wcm else, it create it.
                        $this->addWcmUser($protocol, $username, $password, $userInfo);

                        // Login into the wcm
                        $this->login('wcm', $userLogin, $password);
                    }
                    else
                    {
                        wcmMVC_Action::setWarning($userInfo);
                    }
                }
                break;
                
            case 'wcm': 
            default:
                // Perform login
                if ($username && $password)
                { 
					if ($username[0] == '@')
						$session_log = $this->session->loginAs($username, $password);
					else
						$session_log = $this->session->login($username, $password);
					
                    if ($session_log[0] == -1) 
					{
                        wcmMVC_Action::setError(_LOGIN_FAILED);
						wcmMVC_Action::setMessage(_LOGIN_FAILED);
					}
                    elseif ($session_log[0] == -2)
					{
						$project = wcmProject::getInstance();
						
        				$query = "SELECT id FROM #__user WHERE id=".$session_log[1];
        				$id = $project->database->executeScalar($query);
						$wcmUser = new wcmUser();
						$wcmUser->refresh($id);
						
                        wcmMVC_Action::setError(_EXPIRED_ACCOUNT.'<a href="mailto:'.$wcmUser->email.'">'.$wcmUser->name.'</a>');
						wcmMVC_Action::setMessage(_EXPIRED_ACCOUNT.'<a href="mailto:'.$wcmUser->email.'">'.$wcmUser->name.'</a>');
					}
                    else
                    {
                        wcmMVC_Action::setMessage('');
                        return false;
                    }
                }
                else
				{
                    wcmMVC_Action::setWarning(_PLEASE_ENTER_USERNAME_AND_PASSWORD);
					wcmMVC_Action::setMessage(_PLEASE_ENTER_USERNAME_AND_PASSWORD);
				}
        }
    }

    /**
     * Method used to verify if the user exist into wcm else, it create it.
     *
     *  @param String $protocol     The protocol used to login
     *  @param String $username     The username used to login
     *  @param String $password     The password used to login (else the generated one)
     *  @param Array  $userInfo     (optional) Additionnal informations
     *
     *  @return Integer             The id of the user
     */
    private function addWcmUser($protocol, $username, $password, $userInfo=array())
    {
        // check if the user already have an account
        //if not, create one
        $project = wcmProject::getInstance();

        /**
         * @Todo:   check the password too but not when the user login with the protocol openId cause
         *          we don't have the password (it's a generated one).
         *          Meaby we can query using the userType too and set the database UNIQUE field on userType and login fields.
         */
        $query = "SELECT id FROM #__user WHERE login='".$username."'";
        $id = $project->database->executeScalar($query);

        if ($id == null)
        {
            // Set user personnal information return by the login protocol
            $fullName  = $userInfo['name'];
            $email     = $userInfo['email'];

            // user does not exist, so we create one
            $sql = "INSERT INTO #__user (login, password, name, email, defaultLanguage, userType) 
                    VALUES ('".$username."', '".md5($password)."', '".$fullName."', '".$email."', 'en', '".$protocol."')";

            $project->database->executeStatement($sql);
            $id = mysql_insert_id();
        }

        return $id;
    }
}