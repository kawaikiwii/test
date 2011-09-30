<?php

/**
 * Project:     WCM
 * File:        wcmBusinessObjectManagementWebService.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM Business Object Management Web Service
 *
 * Enables the client application to manage the lifetime of business
 * objects.
 *
 * NOTE: All service methods take a session token as the first
 *       parameter. If an invalid session token is passed
 *       to a method, the latter will throw an exception.
 */
class wcmBusinessObjectManagementWebService
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
     * Creates and checks in a new object given its class name and XML
     * representation.
     *
     * If the object class name or XML representation is invalid, throws
     * a WCM Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $objectXml    The XML representation of the object to create
     *
     * @return int The identifier of the new object
     */
    public function createObject($sessionToken, $objectClass, $objectXml)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::createObject($session, $objectClass, $objectXml);
    }

    /**
     * Checks in an object given its class name, identifier, and XML
     * representation.
     *
     * If the object class name, identifier, or XML representation is
     * invalid, throws a WCM Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $objectId     The object's identifier (must not be zero)
     * @param string $objectXml    The XML representation of the object to check in
     *
     * @return boolean True on success
     */
    public function checkinObject($sessionToken, $objectClass, $objectId, $objectXml)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::checkinObject($session, $objectClass, $objectId, $objectXml);
    }

    /**
     * Checks out an object given its class name and identifier, returning
     * its XML representation.
     *
     * If the object class name or identifier is invalid, throws a WCM
     * Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $objectId     The object's identifier
     *
     * @return string The XML representation of the object
     */
    public function checkoutObject($sessionToken, $objectClass, $objectId)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::checkoutObject($session, $objectClass, $objectId);
    }

    /**
     * Undoes the effects of a previous object check-out operation.
     *
     * If the object class name or identifier is invalid, or an error
     * occurs, throws a WCM Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $objectId     The object's identifier
     *
     * @return boolean True on success
     */
    public function undoCheckoutObject($sessionToken, $objectClass, $objectId)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::undoCheckoutObject($session, $objectClass, $objectId);
    }

    /**
     * Locks an object given its class name and identifier.
     *
     * If the object class name or identifier is invalid, throws a WCM
     * Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $objectId     The object's identifier
     *
     * @return boolean True on success
     */
    public function lockObject($sessionToken, $objectClass, $objectId)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::lockObject($session, $objectClass, $objectId);
    }

    /**
     * Unlocks an object given its class name and identifier.
     *
     * If the object class name or identifier is invalid, throws a WCM
     * Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $objectId     The object's identifier
     *
     * @return boolean True on success
     */
    public function unlockObject($sessionToken, $objectClass, $objectId)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::unlockObject($session, $objectClass, $objectId);
    }

    /**
     * Gets the XML representation of an object given its class name and
     * identifier.
     *
     * If the object class name or identifier is invalid, throws a WCM
     * Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $objectId     The object's identifier
     *
     * @return string The XML representation of the object
     */
    public function getObject($sessionToken, $objectClass, $objectId)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::getObject($session, $objectClass, $objectId);
    }

    /**
     * Gets a list of XML object representations given their class name,
     * a SELECT 'where' clause, and the name of an attribute to order by.
     *
     * If the object class name is invalid, throws a WCM Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The objects' class name
     * @param string $where        The SELECT 'where' clause
     * @param string $orderBy      The attribute to order by
     *
     * @return string[] The list of XML object representations
     */
    public function getObjects($sessionToken, $objectClass, $where, $orderBy)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::getObjects($session, $objectClass, $where, $orderBy);
    }

    /**
     * Deletes an object from the system given its class name and
     * identifier.
     *
     * If the object class name or identifier is invalid, or an error
     * occurs, throws a WCM Web Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $objectId     The object's identifier
     *
     * @return boolean True on success
     */
    public function deleteObject($sessionToken, $objectClass, $objectId)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::deleteObject($session, $objectClass, $objectId);
    }

    /**
     * Deletes objects from the system given their class name and
     * a SELECT 'where' clause.
     *
     * If the class name is invalid or an error occurs, throws a WCM Web
     * Service exception.
     *
     * @param string $sessionToken The session token
     * @param string $objectClass  The object's class name
     * @param string $where        The SELECT 'where' clause
     *
     * @return boolean True on success
     */
    public function deleteObjects($sessionToken, $objectClass, $where)
    {
        $session = wcmWebServiceSession::getSession($sessionToken);
        wcmWebServiceObject::checkObjectClass($objectClass, 'bizobject');
        return wcmWebServiceObject::deleteObjects($session, $objectClass, $where);
    }
}

?>