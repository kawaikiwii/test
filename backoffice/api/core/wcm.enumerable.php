<?php

/**
 * Project:     WCM
 * File:        wcm.enumerable.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This interface describe a common enumerable object
 */
interface wcmEnumerable
{
    /**
     * Initialize and starts a new enumeration on current object
     *
     * @param string $where     Optional where clause
     * @param string $orderby   Optional order clause
     * @param int    $offset    Optional offset of first row (default is zero)
     * @param int    $limit     Optional maximum number of returned rows (default is zero which means return all rows)
     * @param string $of        Optional assoc Array with foreign constrain (key=className, value=id)
     *
     * @return boolean  True on success, false on failure
     */
    public function beginEnum($where = null, $orderby = null, $offset = 0, $limit = 0, $of = null);

    /**
     * Stops current enumeration
     */
    public function endEnum();

    /**
     * Moves to the next item of current enumeration
     *
     * @return boolean True if enumeration has succeed, false otherwise
     */
    public function nextEnum();

    /**
     * Returns the total number of objects which can be enumerated (regardless of the
     * offset and limit parameters passed in the {@link beginEnum()} method)
     *
     * @return int Total number of enumerable objects
     */
    public function enumCount();
}

?>