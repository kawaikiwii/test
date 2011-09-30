<?php
/**
 * Project:     WCM
 * File:        modules/editorial/shared/versioning.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $session = wcmSession::getInstance();
    $bizobject = wcmMVC_Action::getContext();

    echo '<div class="zone">';
    // Add 'create new version' button?
    if ($session->isAllowed($bizobject, wcmPermission::P_WRITE))
    {
        $info = '<ul><li><a href="#" onclick="_wcmAddVersion(); return false;">' . _BIZ_ADD_NEW_VERSION . '</a></li>';
        $info .= '</ul>';
    }
    else
    {
        $info = null;
    }
    wcmGUI::openCollapsablePane(_BIZ_VERSION_HISTORY, true, $info);
?>
    <table class="fullwidth" cellspacing="0" cellpadding="0">
    <tr>
        <th> <?php echo _BIZ_VERSION;?> </th>
        <th> <?php echo _BIZ_REVISION;?> </th>
        <th> <?php echo _BIZ_CREATEDAT;?> </th>
        <th> <?php echo _BIZ_CREATEDBY;?> </th>
        <th width="60%"> <?php echo _BIZ_VERSION_COMMENT;?> </th>
        <th> &nbsp; </th>
    </tr>
<?php
    // retrieve the last 10 versions
    $vm = wcmVersionManager::getInstance();
    $history = $vm->getObjectHistory($bizobject, 10);
    $alternate = false;

    foreach($history as $version)
    {
        // retrieve name of version's creator
        $creator = $version->getCreator();
        $creator = ($creator) ? getConst($creator->name) : _UNKNOWN_USER;

        if ($alternate) { echo '<tr class="alternate">'; } else { echo '<tr>'; }
        echo '<td>' . $version->versionNumber . '</td>';
        echo '<td>' . $version->revisionNumber . '</td>';
        echo '<td class="nowrap">' . getConst($version->createdAt) . '</td>';
        echo '<td>' . $creator . '</td>';
        echo '<td>' . str_replace('\n', '<br/>', $version->comment) . '</td>';
        echo '<td>';
        if ($session->isAllowed($bizobject, wcmPermission::P_WRITE))
        {
            echo '<a href="#" onclick="_wcmRestoreVersion('.$version->id.'); return false;">' . _BIZ_VERSION_RESTORE . '</a>';
            echo '<a href="#" onclick="_wcmRollbackVersion('.$version->id.'); return false;">' . _BIZ_VERSION_ROLLBACK . '</a>';
        }
        echo '</td>';
        echo '</tr>';
        $alternate = !$alternate;
    }
    
    if (count($history) == 0)
    {
        echo '<td colspan="5">(' . _BIZ_NO_VERSION_STORED . ')</td>';
    }
    
    unset($history);
    unset($vm);

    wcmGUI::closeCollapsablePane();
    echo '</div>';