<?php
/**
 * Project:     WCM
 * File:        modules/api/permissions/properties.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $sysobject = wcmMVC_Action::getContext();
    
    /**
     * get parameters. For permissions, parameters have to be passed this way:
     * p0 = permissions type (menus, sites, generation sets etc...)
     */
    $targetKind     = getArrayParameter($params, 'targetKind', null);
    $target         = new $targetKind();

    $permissionsMatrix = array();
    
    switch ($sysobject->getClass())
    {
        case 'wcmGroup':
            makePermissionsMatrix($sysobject, $targetKind, $permissionsMatrix);
            break;

        default:
            foreach (wcmProject::getInstance()->membership->getGroups() as $group)
            {
                $title = getConst($group->name);
                $permission = $sysobject->getGroupPermissions($group->id);
                $permissionTarget = $group->id . '_' . $sysobject->getPermissionTarget();
                $permissionsMatrix[] = array('title' => $title, 'permission' => $permission, 'target' => $permissionTarget);
            }
            break;
    }
    
    
    // Render form
    wcmGUI::openFieldset('', array('class' => 'permissions'));
    wcmGUI::renderHiddenField('permissionTypes[]', $targetKind);
    $permissionMasks   = array('wcmMenu'          => wcmPermission::P_READ | wcmPermission::P_NONE,
                               'wcmBizclass'      => wcmPermission::P_READ | wcmPermission::P_WRITE | wcmPermission::P_EXECUTE | wcmPermission::P_DELETE | wcmPermission::P_NONE,
                               'wcmSysclass'      => wcmPermission::P_READ | wcmPermission::P_WRITE | wcmPermission::P_EXECUTE | wcmPermission::P_DELETE | wcmPermission::P_NONE,
                               'site'             => wcmPermission::P_READ | wcmPermission::P_WRITE | wcmPermission::P_NONE,
                               'wcmGenerationSet' => wcmPermission::P_EXECUTE | wcmPermission::P_NONE
                               );
    $permissionHeaders = array(wcmPermission::P_READ    => _READ,
                               wcmPermission::P_WRITE   => _WRITE,
                               wcmPermission::P_EXECUTE => _EXECUTE,
                               wcmPermission::P_DELETE  => _DELETE,
                               wcmPermission::P_NONE    => '(' . _ACCESS_DENIED . ')');
    $permissionMask    = getArrayParameter($permissionMasks, $targetKind, wcmPermission::P_ALL);
       
    echo '<table cellspacing="0">';
    echo '<tr>';
    echo '<td>&nbsp;</td>';
    $p = 1;
    while ($p < wcmPermission::P_ALL)
    {
        if ($p & $permissionMask) echo '<th>' . $permissionHeaders[$p] . '</th>';
        $p = $p << 1;
    }
    
    $targetName = ($targetKind == 'wcmGroup') ? $sysobject->getClass() : $targetKind;
    
    echo '<th><a href="javascript:toggleCheckboxes(\'' . $targetName . '\');" class="toggle" title="' . _TOGGLE . '"><span>' . _TOGGLE . '</span></a></th>';
    echo '</tr>';

    foreach ($permissionsMatrix as $row)
    {
        echo '<tr>';
        echo '<th style="text-align: left;">';
        echo '<label>' . $row['title'] . '</label></th>';
        $sP = 1;
        while ($sP < wcmPermission::P_ALL)
        {
            if ($sP & $permissionMask)
            {
                echo '<td>';
                echo '<input name="permissions[]" value="' . $row['target'] . '*' . $sP .'#'. $sysobject->id . '" ';
                if (($sP & $row['permission']) != 0) echo ' checked="checked"';
                if ($sP == wcmPermission::P_NONE && $row['permission']==0) echo ' checked="checked"';
                echo ' type="checkbox"/>';
                echo '</td>';
            }
            $sP = $sP << 1;
        }
        echo '<td><a href="javascript:toggleCheckboxes(\'' . $row['target'] . '\');" class="toggle" title="' . _TOGGLE . '"><span>' . _TOGGLE . '</span></a></td>';
        echo '</tr>';
    }
    echo '</table>';
    wcmGUI::closeFieldset();

    
    
/**
 * Populates the permissionsMatrix recursively according to parentIds
 * 
 * @param   string  $targetKind         targeted sysobject
 * @param   array   &$permissionsMatrix array to populate
 * @param   int     $parentId           id of parent element (default is null)
 * @param   string  $prefix             string used to prefix child elements
 */
function makePermissionsMatrix($sysobject, $targetKind, &$permissionsMatrix = array(), $parentId = 0, $prefix = '')
{
    $target      = new $targetKind();
    $isRecursive = isset($target->parentId);
    if ($isRecursive)
    {
        $where       = 'parentId=' . $parentId;
        $orderBy     = 'parentId, rank';
    }
    else
    {
        $where = null;
        $orderBy = 'id';
    }

    $target->beginEnum($where, $orderBy);
    while ($target->nextEnum())
    {
        $title      = getObjectLabel($target);
        $permission = ($sysobject->getClass() == 'wcmGroup') ? $target->getGroupPermissions($sysobject->id) : $target->getUserPermissions($sysobject->id);
        $permissionTarget = $target->getPermissionTarget();

        $permissionsMatrix[] = array('title' => $prefix . $title, 'permission' => $permission, 'target' => $permissionTarget);
        if ($isRecursive)
        {
            makePermissionsMatrix($sysobject, $targetKind, $permissionsMatrix, $target->id, ' :: ' . $prefix);
        }
    }
}


    
