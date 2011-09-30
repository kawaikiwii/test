<?php

/* OPTIMISATION NSTEIN 29/06/2009 */
/**
 * Project:     WCM
 * File:        wcm.masterClassRegistry.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

class wcmMasterClassRegistry
{
    /**
     * Array of all instanciated masterClasses
     * @var array
     */
    public static $_masterClasses = array();

    /**
     * Get the master class of a given object
     *
     * @param wcmObject $object object for which we want to retrieve the masterclass
     * @return wcmSysclass Masterclass object
     */
    public static function getMC($object)
    {
        if(!isset(self::$_masterClasses[get_class($object)]['mc']))
        {
            self::getMasterClasses($object);
        }

        return self::$_masterClasses[get_class($object)]['mc'];
    }

    /**
     * Get the workflow class of a given object
     *
     * @param wcmObject $object object for which we want to retrieve the workflow class
     * @return wcmWorkflow Workflow object
     */
    public static function getWorkflow($object)
    {
        if(!isset(self::$_masterClasses[get_class($object)]['workflow']))
        {
            self::getMasterClasses($object);
        }

        return self::$_masterClasses[get_class($object)]['workflow'];
    }

    /**
     * Get the database object of a given object
     *
     * @param wcmObject $object object for which we want to retrieve the database object
     * @return wcmDatabase Database object
     */
    public static function getDatabase($object)
    {
        if(!isset(self::$_masterClasses[get_class($object)]['database']))
        {
            self::getMasterClasses($object);
        }

        return self::$_masterClasses[get_class($object)]['database'];
    }

    /**
     * Get the table name of a specific object
     *
     * @param wcmObject $object object for which we want to retrieve the table name
     * @return string table name
     */
    public static function getTableName($object)
    {
        if(!isset(self::$_masterClasses[get_class($object)]['tableName']))
        {
            self::getMasterClasses($object);
        }

        return self::$_masterClasses[get_class($object)]['tablename'];
    }

    /**
     * Define if the object is using optimistic lock or not
     *
     * @param wcmObject $object object for which we want to now lock mechanism
     * @return bool true if lock is optimistic
     */
    public static function getIsLockOptimistic($object)
    {
        if(!isset(self::$_masterClasses[get_class($object)]['isLockOptimistic']))
        {
            self::getMasterClasses($object);
        }

        return self::$_masterClasses[get_class($object)]['isLockOptimistic'];
    }

    /**
     * Retrieve all masterclass for a specific object
     *
     * @param wcmObject $object object for which we want to retrieve all the masterclass
     * @throws Exception    Configuration errors
     */
    public static function getMasterClasses($object)
    {
        $project = wcmProject::getInstance();

        // Not having the sysclass/bizclass is a fatal error!
        if ($object instanceOf wcmBizobject)
        {
            $mc = $project->bizlogic->getBizclassByClassName($object->getClass());
            if (!$mc)
            {
                throw new exception('FATAL ERROR: WCM is not well configured: Cannot find bizclass for ' . $object->getClass());
            }

            $connector = $mc->getConnector();
            if (!$connector)
            {
                throw new exception('FATAL ERROR: Invalid connector reference for bizclass ' . $object->getClass());
            }

            self::$_masterClasses[get_class($object)]['database'] = $connector->getBusinessDatabase();
        }
        else
        {
            $mc = $project->bizlogic->getSysclassByClassName($object->getClass());

            if (!$mc)
                    throw new exception('FATAL ERROR: WCM is not well configured: Cannot find sysclass for ' . $object->getClass());

            self::$_masterClasses[get_class($object)]['database'] = $project->database;
        }

        self::$_masterClasses[get_class($object)]['mc'] = $mc;

        // Retrieve connector table, lock mode and workflow
        self::$_masterClasses[get_class($object)]['tableName'] = $mc->connectorTable;
        self::$_masterClasses[get_class($object)]['isLockOptimistic'] = $mc->allowOptimisticLock;

        // A workflow cannot load dynamically a workflow to avoid recursion (endless loop)
        if (($object instanceOf wcmWorkflow) || !property_exists(get_class($mc), 'workflowId') || !$mc->workflowId)
        {
            self::$_masterClasses[get_class($object)]['workflow'] = '';
        }
        else
        {
            self::$_masterClasses[get_class($object)]['workflow'] = $project->workflowManager->getWorkflowById($mc->workflowId);
        }
    }
}
