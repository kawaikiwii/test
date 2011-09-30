<?php
/**
 * Project:     WCM
 * File:        wcmWebServiceObject.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM Web Service Object Methods
 */
class wcmWebServiceObject
{
    /**
     * Checks whether a class name corresponds to an existing class.
     * Throws a WCM Web Service exception if the check fails.
     *
     * If parameter $parentObjectClass is non-null, also checks whether
     * the given class is a subclass of the parent class.
     *
     * @param string $objectClass       The object's class name
     * @param string $parentObjectClass The parent object class (default is null)
     *
     * @return void
     */
    public static function checkObjectClass($objectClass, $parentObjectClass = null)
    {
        if (!class_exists($objectClass))
        {
            throw new wcmWebServiceException(WCM_WS_EXC_INVALID_OBJECT_CLASS, $objectClass);
        }
        if ($parentObjectClass && !is_subclass_of($objectClass, $parentObjectClass))
        {
            throw new wcmWebServiceException(WCM_WS_EXC_INVALID_OBJECT_CLASS, $objectClass);
        }
    }

    /**
     * Gets an instance of an object given its class name and identifier.
     *
     * If parameter $createObject is false, the identifier must
     * correspond to an existing object, otherwise a WCM Web Service
     * exception is thrown.
     *
     * If parameter $createObject is true, the identifier can be zero,
     * in which case a new object is returned.
     *
     * @param string $objectClass  The object's class name
     * @param string    $objectId     The object's identifier (may be zero)
     * @param bool   $createObject Whether a new object may be created
     *
     * @return wcmSysobject The object instance
     */
    public static function getObjectInstance($objectClass, $objectId, $createObject = false)
    {
        self::checkObjectClass($objectClass);

        // All IDs are numeric except for template and template category IDs
        if (!is_numeric($objectId) && $objectClass != "wcmTemplate" && $objectClass != "wcmTemplateCategory")
        {
            throw new wcmWebServiceException(WCM_WS_EXC_INVALID_OBJECT_ID, $objectId);
        }

        $object = new $objectClass;
        if ($objectId)
        {
            if (!$object->refresh($objectId))
            {
                throw new wcmWebServiceException(WCM_WS_EXC_INVALID_OBJECT_ID_OBJECT, $objectId, $object->getErrorMsg());
            }
        }
        else if (!$createObject)
        {
            throw new wcmWebServiceException(WCM_WS_EXC_INVALID_OBJECT_ID, $objectId);
        }

        return $object;
    }

    /**
     * Creates and checks in a new object given its class name and XML
     * representation.
     *
     * If the object class name or XML representation is invalid, throws
     * a WCM Web Service exception.
     *
     * @param wcmSession $session     The session
     * @param string     $objectClass The object's class name
     * @param string     $objectXml   The XML representation of the object to create
     *
     * @return int The identifier of the new object
     */
    public static function createObject($session, $objectClass, $objectXml)
    {
        $object = self::getObjectInstance($objectClass, 0, true);
        try
        {
            $object->initFromXml($objectXml);
        }
        catch (exception $e)
        {
            throw new wcmWebServiceException(
                WCM_WS_EXC_INVALID_OBJECT_XML, $objectXml, $e->getErrorMsg());
        }

        $succeeded = false;
        if ($objectClass == 'wcmBizrelation' || is_subclass_of($objectClass, 'wcmBizrelation'))
        {
            $succeeded = $object->save();
        }
        else
        {
            $succeeded = $object->checkin();
        }
        if (!$succeeded)
        {
            throw new wcmWebServiceException(
                WCM_WS_EXC_INITIAL_CHECK_IN_OBJECT_FAILED, $objectClass, $object->getErrorMsg());
        }

        return $object->id;
    }

    /**
     * Checks in an object given its class name, identifier, and XML
     * representation.
     *
     * If the object class name, identifier, or XML representation is
     * invalid, throws a WCM Web Service exception.
     *
     * @param wcmSession $session     The session
     * @param string     $objectClass The object's class name
     * @param string     $objectId    The object's identifier (must not be zero)
     * @param string     $objectXml   The XML representation of the object to check in
     *
     * @return boolean True on success
     */
    public static function checkinObject($session, $objectClass, $objectId, $objectXml)
    {
        $object = self::getObjectInstance($objectClass, $objectId, false);
        try
        {
            $object->initFromXml($objectXml);
        }
        catch (exception $e)
        {
            throw new wcmWebServiceException(
                WCM_WS_EXC_INVALID_OBJECT_XML_OBJECT, $objectXml, $e->getErrorMsg());
        }

        $succeeded = false;
        if ($objectClass == 'wcmBizrelation' || is_subclass_of($objectClass, 'wcmBizrelation'))
        {
            $succeeded = $object->save();
        }
        else
        {
            $succeeded = $object->checkin();
        }
        if (!$succeeded)
        {
            throw new wcmWebServiceException(
                WCM_WS_EXC_CHECK_IN_OBJECT_FAILED, $objectClass, $objectId, $object->getErrorMsg());
        }

        return true;
    }

    /**
     * Checks out an object given its class name and identifier, returning
     * its XML representation.
     *
     * If the object class name or identifier is invalid, throws a WCM
     * Web Service exception.
     *
     * @param wcmSession $session     The session
     * @param string     $objectClass The object's class name
     * @param string     $objectId    The object's identifier
     *
     * @return string The XML representation of the object
     */
    public static function checkoutObject($session, $objectClass, $objectId)
    {
        $object = self::getObjectInstance($objectClass, $objectId);
        if (!$object->checkout())
        {
            throw new wcmWebServiceException(WCM_WS_EXC_CHECK_OUT_OBJECT_FAILED, $objectClass, $objectId, $object->getErrorMsg());
        }
        return $object->toXml();
    }

    /**
     * Undoes the effects of a previous object check-out operation.
     *
     * If the object class name or identifier is invalid, or an error
     * occurs, throws a WCM Web Service exception.
     *
     * @param wcmSession $session     The session
     * @param string     $objectClass The object's class name
     * @param string     $objectId    The object's identifier
     *
     * @return boolean True on success
     */
    public static function undoCheckoutObject($session, $objectClass, $objectId)
    {
        $object = self::getObjectInstance($objectClass, $objectId);
        if (!$object->undoCheckout())
        {
            throw new wcmWebServiceException(WCM_WS_EXC_UNDO_CHECK_OUT_OBJECT_FAILED, $objectClass, $objectId, $object->getErrorMsg());
        }
        return true;
    }

    /**
     * Locks an object given its class name and identifier.
     *
     * If the object class name or identifier is invalid, throws a WCM
     * Web Service exception.
     *
     * @param wcmSession $session     The session
     * @param string     $objectClass The object's class name
     * @param string     $objectId    The object's identifier
     *
     * @return boolean True on success
     */
    public static function lockObject($session, $objectClass, $objectId)
    {
        $object = self::getObjectInstance($objectClass, $objectId);
        if (!$object->lock())
        {
            throw new wcmWebServiceException(WCM_WS_EXC_CHECK_OUT_OBJECT_FAILED, $objectClass, $objectId, $object->getErrorMsg());
        }
        return true;
    }

    /**
     * Unlocks an object given its class name and identifier.
     *
     * If the object class name or identifier is invalid, throws a WCM
     * Web Service exception.
     *
     * @param wcmSession $session     The session
     * @param string     $objectClass The object's class name
     * @param string     $objectId    The object's identifier
     *
     * @return boolean True on success
     */
    public static function unlockObject($session, $objectClass, $objectId)
    {
        $object = self::getObjectInstance($objectClass, $objectId);
        if (!$object->unlock())
        {
            throw new wcmWebServiceException(WCM_WS_EXC_UNDO_CHECK_OUT_OBJECT_FAILED, $objectClass, $objectId, $object->getErrorMsg());
        }
        return true;
    }

    /**
     * Gets the XML representation of an object given its class name and
     * identifier.
     *
     * If the object class name or identifier is invalid, throws a WCM
     * Web Service exception.
     *
     * @param wcmSession $session     The session
     * @param string     $objectClass The object's class name
     * @param string     $objectId    The object's identifier
     *
     * @return string The XML representation of the object
     */
    public static function getObject($session, $objectClass, $objectId)
    {
        $object = self::getObjectInstance($objectClass, $objectId);
        return $object->toXml();
    }

    /**
     * Gets a list of XML object representations given their class name,
     * a SELECT 'where' clause, and the name of an attribute to order by.
     *
     * If the object class name is invalid, throws a WCM Web Service exception.
     *
     * @param wcmSession $session     The session
     * @param string     $objectClass The objects' class name
     * @param string     $where       The SELECT 'where' clause
     * @param string     $orderBy     The attribute to order by
     *
     * @return string[] The list of XML object representations
     */
    public static function getObjects($session, $objectClass, $where, $orderBy)
    {
        $object = new $objectClass;
        if (!$object->beginEnum($where, $orderBy))
        {
            throw new wcmWebServiceException(WCM_WS_EXC_GET_OBJECTS_FAILED, $objectClass, $where, $orderBy, $object->getErrorMsg());
        }

        $xmlObjects = array();
        while ($object->nextEnum())
        {
            $xmlObjects[] = $object->toXml();
        }
        $object->endEnum();

        return $xmlObjects;
    }

    /**
     * Deletes an object from the system given its class name and
     * identifier.
     *
     * If the object class name or identifier is invalid, or an error
     * occurs, throws a WCM Web Service exception.
     *
     * @param wcmProject $session     The session
     * @param string     $objectClass The object's class name
     * @param string     $objectId    The object's identifier
     *
     * @return boolean True on success
     */
    public static function deleteObject($session, $objectClass, $objectId)
    {
        $object = self::getObjectInstance($objectClass, $objectId);
        if (!$object->delete())
        {
            throw new wcmWebServiceException(WCM_WS_EXC_DELETE_OBJECT_FAILED, $objectClass, $objectId, $object->getErrorMsg());
        }

        return true;
    }

    /**
     * Deletes objects from the system given their class name and
     * a SELECT 'where' clause.
     *
     * If the class name is invalid or an error occurs, throws a WCM Web
     * Service exception.
     *
     * @param wcmProject $session     The session
     * @param string     $objectClass The object's class name
     * @param string     $where       The SELECT 'where' clause
     *
     * @return boolean True on success
     */
    public static function deleteObjects($session, $objectClass, $where)
    {
        $object = new $objectClass;
        if (!$object->beginEnum($where))
        {
            throw new wcmWebServiceException(WCM_WS_EXC_DELETE_OBJECTS_FAILED, $objectClass, $where, $object->getErrorMsg());
        }

        while ($object->nextEnum())
        {
            if (!$object->delete())
            {
                throw new wcmWebServiceException(WCM_WS_EXC_DELETE_OBJECT_FAILED, $objectClass, $object->id, $object->getErrorMsg());
            }
        }

        $object->endEnum();

        return true;
    }
}