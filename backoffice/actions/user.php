<?php
/**
 * Project:     WCM
 * File:        user.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 

/**
 * This class implements the action controller for the user page
 */
class wcmUserAction extends wcmMVC_SysAction
{
    /**
     * Set new groups before save
     */
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);

        // Set new groups
        $groups = array();
        if (isset($_REQUEST['_groups']))
        {
            foreach ($_REQUEST['_groups'] as $id => $active)
            {
                if ($active) $groups[] = $id;
            }
        }
        $this->context->setGroups($groups);
    }
}
