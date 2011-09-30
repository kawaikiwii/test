<?php

/**
 * Project:     WCM
 * File:        wcm.permission.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The wcmPermission class represents a generic action
 * => For each action a set of permissions can be defined (for users and groups)
 */
class wcmPermission
{
    /**
     * This constant represents the value of read permission
     * (int) 1
     */
    const P_READ   = 1;

    /**
     * This constant represents the value of write permission
     * (int) 2
     */
    const P_WRITE  = 2; 

    /**
     * This constant represents the value of execute permission
     * (int) 4
     */
    const P_EXECUTE = 4; 

    /**
     * This constant represents the value of delete permission
     * (int) 8
     */
    const P_DELETE  = 8;
 
    /**
     * This constant represents the spcial value for 'no permissions'
     * (int) 16
     */
    const P_NONE   = 16;

    /**
     * This constant represents the value of all permissions
     * (int) 31
     */
    const P_ALL     = 31;
}