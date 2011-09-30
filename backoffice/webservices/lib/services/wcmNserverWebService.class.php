<?php
/**
 * Project:     WCM
 * File:        wcmNserverWebService.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM NServer Web Service
 *
 * Enables the client application to retrieve content from Nserver.
 *
 * NOTE: BETA
 */
class wcmNserverWebService
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
     * @param string $language The session language
     *
     * @return string The session token
     */
    public function login($userId, $password, $language)
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

    /**
     * Retrieve NSentiment data
     *
     * @param string $sessionToken session token
     * @param string $argText Text info to look over
     *
     * @return string NSentiment data json encoded
     */
    public function nsentiment($sessionToken, $argText)
    {
        wcmWebServiceSession::checkSession($sessionToken);

        $nServer = new wcmSemanticServer();
        $semanticData = $nServer->mineText($argText, 1, 'en', array('NSentiment'));

        return json_encode(array('subjectivity' => $semanticData->subjectivity, 'tone' => $semanticData->tone));
    }

    /**
     * Retrieve NFinder Data
     *
     * @param string $sessionToken session token
     * @param string $argText Text info to look over
     *
     * @return string NSentiment data serialized and base64 encoded
     */
    public function nfinder($sessionToken, $argText)
    {
        wcmWebServiceSession::checkSession($sessionToken);

        $nServer = new wcmSemanticServer();
        $semanticData = $nServer->mineText($argText, 1, 'ENGLISH', array('NFinder'));

        $results['on'] = $semanticData->ON;
        $results['pn'] = $semanticData->PN;
        $results['gl'] = $semanticData->GL;

        return base64_encode(serialize($results));
    }

    /**
     * Retrieve NCategorizer Data
     *
     * @param string $sessionToken session token
     * @param string $argText Text info to look over
     *
     * @return string NSentiment data serialized and base64 encoded
     */
    public function ncategorizer($sessionToken, $argText)
    {
        wcmWebServiceSession::checkSession($sessionToken);
        $nServer = new wcmSemanticServer();
        $semanticData = $nServer->mineText($argText, 1, 'en', array('NCategorizer'));
        return base64_encode(serialize($semanticData->categories));
    }

    /**
     * Retrieve NConceptExtractor Data
     *
     * @param string $sessionToken session token
     * @param string $argText Text info to look over
     *
     * @return string NSentiment data serialized and base64 encoded
     */
    public function nconcepts($sessionToken, $argText)
    {
        wcmWebServiceSession::checkSession($sessionToken);

        $nServer = new wcmSemanticServer();
        $semanticData = $nServer->mineText($argText, 1, 'en', array('NConceptExtractor'));
        return base64_encode(serialize($semanticData->concepts));
    }
}

?>