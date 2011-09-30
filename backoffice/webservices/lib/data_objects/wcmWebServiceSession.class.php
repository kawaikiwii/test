<?php

/**
 * Project:     WCM
 * File:        wcmWebServiceSession.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM Web Service Session Methods
 */
class wcmWebServiceSession
{
    /**
     * Checks whether a given session token corresponds to an
     * existing session. If not, throws a WCM Web Service exception.
     *
     * @param string $sessionToken The session token to check
     *
     * @return void
     */
    public static function checkSession($sessionToken)
    {
        self::getSession($sessionToken);
    }

    /**
     * Gets the session corresponding to a given session token.
     *
     * If the token does not correspond to an existing session,
     * throws a WCM Web Service exception.
     *
     * @param string $sessionToken The session token
     *
     * @return wcmWebServiceSession The corresponding session
     */
    public static function getSession($sessionToken)
    {
        // Decrypt the session token
        $encryption = wcmEncryption::getInstance();
        $decryptedSessionToken = $encryption->decrypt($sessionToken);
        if (!$decryptedSessionToken)
        {
            throw new wcmWebServiceException(WCM_WS_EXC_INVALID_SESSION_TOKEN, $sessionToken);
        }

        // Session token == session ID + session creation time
        list($sessionId, $createdAt, $garbage) = explode('_', $decryptedSessionToken);
        if (!is_numeric($sessionId) || !date_parse($createdAt) || $garbage)
        {
            throw new wcmWebServiceException(WCM_WS_EXC_INVALID_SESSION_TOKEN, $sessionToken);
        }

        // Refresh the session
        $session = new wcmSession($sessionId);
        if (!$session->id)
        {
            throw new wcmWebServiceException(WCM_WS_EXC_INVALID_SESSION_TOKEN_ID, $sessionToken);
        }

        // Reload language files
        $session->setLanguage($session->getLanguage());

        // Set instance of session
        wcmSession::setInstance($session);

        return $session;
    }

    /**
     * Logs a given user into the system, creating a new session.
     *
     * If the given user is already logged in, reuses the existing
     * session.
     *
     * If the given user identifier or password is invalid, throws
     * a WCM Web Service exception.
     *
     * If the given language is invalid, uses the language specified in
     * the WCM configuration.
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
    public static function login($userId, $password, $language = 'en')
    {
        // TODO fix current encryption issues with non-PHP clients
        if (true)
        {
            $decryptedPassword = $password;
        }
        else
        {
            // Decrypt the password
            $encryption = wcmEncryption::getInstance();
            $decryptedPassword = $encryption->decrypt($password);
            if (!$decryptedPassword)
            {
                throw new wcmWebServiceException(WCM_WS_EXC_INVALID_USER_ID_OR_PASSWORD, $userId);
            }
        }

        // Open a WCM session and set its language for the localization
        // mechanism to work properly
        $session = wcmSession::getInstance();
        if (!$session->setLanguage($language))
        {
            throw new wcmWebServiceException(WCM_WS_EXC_INVALID_SESSION_LANGUAGE, $language);
        }

        // Get the user associated with the session, if any
        $user = $session->getUser();
        if ($user)
        {
            // Validate the session user against the given credentials
            if ($user->login != $userId || $user->password != md5($decryptedPassword))
            {
                throw new wcmWebServiceException(WCM_WS_EXC_SESSION_EXISTS_DIFFERENT_CREDENTIALS, $userId);
            }
        }
        else
        {
            // Log the user into the system
            if (!$session->login($userId, $decryptedPassword))
            {
                // wcmSession::login() unsets the system session, so we
                // must recreate it and reset its language
                // for the localization mechanism to work

                wcmSession::getInstance()->setLanguage($language);
                throw new wcmWebServiceException(WCM_WS_EXC_INVALID_USER_ID_OR_PASSWORD, $userId);
            }
        }

        // Get a session token
        return $session->getToken();
    }

    /**
     * Logs the user associated with a given session token out of
     * the system.
     *
     * If the session token is invalid, throws a WCM Web Service
     * exception.
     *
     * NOTE: The session token must have been obtained from
     *       a previous login service call.
     *
     * @param string $sessionToken The session token
     *
     * @return void
     */
    public static function logout($sessionToken)
    {
        $session = self::getSession($sessionToken);
        $session->logout();
        $session->delete();
    }
}

?>