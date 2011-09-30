<?php

/**
 * Project:     WCM
 * File:        wcmContentGenerationWebService.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM Content Generation Web Service
 * 
 * Enables the client application to generate Web content based on the
 * business objects defined in the system.
 *
 * NOTE: All service methods take a session token as the first
 *       parameter. If an invalid session token is passed
 *       to a method, the latter will throw an exception.
 */
class wcmContentGenerationWebService
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
     * Generates content given a generation identifier and an array of
     * extra context (name-value pairs).
     * 
     * If parameter $generateFile is FALSE, returns the generated content
     * as an array of wcmWebServiceGeneratedContent objects.
     *
     * @param string                       $sessionToken The session token
     * @param int                          $generationId The generation identifier
     * @param wcmWebServiceNameValuePair[] $extraContext Any extra context
     * @param boolean                      $generateFile Whether to generate file(s)
     * 
     * @return wcmWebServiceGeneratedContent[] The generated content (empty if $generateFile is TRUE)
     */
    public function generate($sessionToken, $generationId, $extraContext, $generateFile)
    {
        wcmWebServiceSession::getSession($sessionToken);

        $generator = new wcmTemplateGenerator;
        $extraContext = wcmWebServiceUtil::pairs2assoc($extraContext);

        $results = $generator->executeGeneration($generationId, $extraContext, $generateFile);
        if ($results === null)
        {
            $errorMsg = ''; // TODO wcmTemplateGenerator should have a getErrorMsg method
            throw new wcmWebServiceException(WCM_WS_EXC_GENERATE_FAILED, $generationId, $errorMsg);
        }

        $generatedContents = array();
        if ($results)
        {
            foreach ($results as $result)
            {
                $generatedContent = new wcmWebServiceGeneratedContent();
                $generatedContent->name = $result['name'];
                $generatedContent->content = $result['content'];

                $generatedContents[] = $generatedContent;
            }
        }

        return $generatedContents;
    }

    /**
     * Generates content given a generation content identifier and an
     * array of extra context (name-value pairs).
     *
     * If parameter $generateFile is FALSE, returns the generated content
     * as a wcmWebServiceGeneratedContent object.
     * 
     * @param string                       $sessionToken        The session token
     * @param int                          $generationContentId The generation content identifier
     * @param wcmWebServiceNameValuePair[] $extraContext        Any extra context
     * @param boolean                      $generateFile        Whether to generate file(s)
     *
     * @return wcmWebServiceGeneratedContent The generated content (empty if $generateFile is TRUE)
     */
    public function generateContent($sessionToken, $generationContentId, $extraContext, $generateFile)
    {
        wcmWebServiceSession::getSession($sessionToken);

        $generator = new wcmTemplateGenerator;
        $extraContext = wcmWebServiceUtil::pairs2assoc($extraContext);

        $result = $generator->executeGenerationContent(
            $generationContentId, $extraContext, $generateFile, false);

        if ($result === null)
        {
            $errorMsg = ''; // TODO wcmTemplateGenerator should have a getErrorMsg method
            throw new wcmWebServiceException(
                WCM_WS_EXC_GENERATE_CONTENT_FAILED, $generationContentId, $errorMsg);
        }

        $generatedContent = new wcmWebServiceGeneratedContent();
        if ($result)
        {
            $generatedContent->name = $result['name'];
            $generatedContent->content = $result['content'];
        }

        return $generatedContent;
    }

    /**
     * Generates content associated with a template given its identifier
     * and an array of template parameters (name-value pairs).
     *
     * @param string                       $sessionToken The session token
     * @param string                       $templateId   The template identifier
     * @param wcmWebServiceNameValuePair[] $parameters   The template parameters
     * 
     * @return string The generated template content
     */
    public function generateTemplateContent($sessionToken, $templateId, $parameters)
    {
        wcmWebServiceSession::getSession($sessionToken);

        $generator = new wcmTemplateGenerator;
        $parameters = wcmWebServiceUtil::pairs2assoc($parameters);

        return $generator->executeTemplate($templateId, $parameters);
    }

    /**
     * Generates content associated with an object given the object's
     * class name and identifier.
     * 
     * Throws an exception if the generation fails.
     *
     * @param string   $sessionToken    The session token
     * @param string   $objectClass     The object's class name
     * @param string   $objectId        The object's identifier
     * @param boolean  $recursive       Whether to generate content recursively
     * @param boolean  $forceGeneration Whether to force content generation
     * 
     * @return boolean True on success
     */
    public function generateObjectContent($sessionToken, $objectClass, $objectId, $recursive, $forceGeneration)
    {
        wcmWebServiceSession::checkSession($sessionToken);        

        $object = wcmWebServiceObject::getObjectInstance($objectClass, $objectId);
        if (!$object->generate($recursive, null, $forceGeneration))
        {
            throw new wcmWebServiceException(WCM_WS_EXC_GENERATE_OBJECT_CONTENT_FAILED, $objectClass, $objectId, $object->getErrorMsg());
        }

        return true;
    }
}

?>
