<?php
/**
 * Project:     WCM
 * File:        business/modules/maintenance/purge.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
wcmGUI::openForm('wcmPurgeForm', wcmMVC_Action::computeURL(null, 'purge'));
?>
<form id="wcmPurgeForm" action="?" method="post">
<div id="dashboard">
    <div class="zone">
        <h3><?php echo _MENU_SYSTEM_MAINTENANCE_PURGE_CONTENT?></h3>
        <div id="dbzone_1">
            <div class="module autofit">
                <h4><?php echo _BIZ_PURGE_SELECT_OBJECTS_TO_PURGE?></h4>
                <div class="scroll">
                    <table>
                    <tr>
                        <th><?php echo _BIZ_PURGE?></th>
                        <th><?php echo _BIZ_KIND_ELEMENT?></td>
                        <th><?php echo _BIZ_NUMBER_RECORD_BUSINESS_DB?></td>
                        <th><?php echo _BIZ_NUMBER_RECORD_TO_PURGE?></td>
                    </tr>
                <?php
                    $alternate = false;
                    $bizclass = new wcmBizclass();
                    $bizclass->beginEnum();
                    while($bizclass->nextEnum())
                    {
                        $bizobject = new $bizclass->className;
                        if (property_exists($bizobject, 'expirationDate'))
                        {
                            $toPurge = $bizobject->getExpiredCount();
                            
                            if ($alternate) { echo '<tr class="alternate">'; } else { echo '<tr>'; }
                            echo '<td>';
                            if ($toPurge > 0)
                            {
                                echo '<input type="checkbox" name="_purgeClasses[]" value="' . $bizclass->className . '" checked="checked"/>';
                            }
                            echo '</td>';
                            echo '<td>' . getConst($bizclass->name) . '</td>';
                            echo '<td>' . $bizobject->getCount() . '</td>';
                            echo '<td>' . $toPurge . '</td>';
                            echo '</tr>';
                            $alternate = !$alternate;
                        }
                    }
                    $bizclass->endEnum();
                    unset($bizclass);
                ?>
                    </table>
                </div>
            </div>
            <div class="module autofit">
            <h4><?php echo _BIZ_EXECUTE_PURGE?></h4>
            <div style="padding:20px">
                <ul>
                    <?php echo wcmGUI::renderBooleanField('_purgeLocked', false, _BIZ_PURGE_LOCKED_OBJETS)?>
                    <br/>
                    <li><a href="#" onclick="_wcmPurge(); return false;"><?php echo _BIZ_PURGE?></a></li>
                    <br/>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>
<?php
    wcmGUI::closeForm();