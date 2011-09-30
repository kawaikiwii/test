<?php

/**
 * Project:     WCM
 * File:        wcmUserAuthenticationWebService.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM User Authentication Web Service
 * 
 * Enables the client application to authenticate itself to the system
 * in a secure manner prior to any other service calls,
 * 
 * NOTE: All service methods take a session token as the first
 *       parameter. If an invalid session token is passed
 *       to a method, the latter will throw an exception.
 */
class wcmUserAuthenticationWebService
{
    /**
     * Logs a given user into the system, creating a new session.
     * 
     * If the given user is already logged in, reuses the existing
     * session.
     * 
     * If the given user identifier or password is invalid, throws
     * a WCM Web Service exception.
     *
     * NOTE: The returned session token must be passed as the first
     *       parameter to all other service calls.
     * 
     * @param string $userId   The identifier of the user to log in
     * @param string $password The user's password
     * @param string $language The session language (default is 'en' for english)
     *
     * @return string The session token
     */
    public function login($userId, $password, $language = 'en')
    {
        return wcmWebServiceSession::login($userId, $password, $language);
    }

    /**
     * Logs the user associated with a given session token out of
     * the system.
     * 
     * If the session token is invalid, throws a WCM Web Service
     * exception.
     *
     * NOTE: The session token must have been obtained from a previous
     *       login service call or a call to the wcmSession::getToken
     *       method.
     * 
     * @param string $sessionToken The session token
     * 
     * @return boolean True on success
     */
    public function logout($sessionToken)
    {
        wcmWebServiceSession::logout($sessionToken);
        return true;
    }
}

?>