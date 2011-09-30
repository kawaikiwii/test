<?php

/**
 * Project:     WCM
 * File:        semanticData.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

include(WCM_DIR . '/pages/includes/header.php');

$kinds       = getArrayParameter($_REQUEST, "kinds",       null);
$classNames  = getArrayParameter($_REQUEST, "classNames",  null);
$where       = getArrayParameter($_REQUEST, "where",       null);
$forceUpdate = getArrayParameter($_REQUEST, "forceUpdate", null);

?>
<script language="javascript" type"text/javascript">
    var updateSemanticDataCmd = 'updateSemanticData';
    var updateSemanticDataPHP = '.php';

    function updateSemanticData(kindList, classList, where, forceUpdate) {
        $('divSemanticDataStatus').innerHTML = '<?php echo _BIZ_EXECUTION_COMMAND; ?>';

        wcmBizAjaxController.call('biz.updateSemanticData', {
            kindList: kindList.join(','),
            classList: classList.join(','),
            where: encodeURIComponent(where),
            forceUpdate: (forceUpdate ? '1' : '0'),
            resultDivId: 'divSemanticDataStatus'
        });
    }
</script>
<table>
<tr height="99%" valign="top">
    <td colspan="2" class="mainContent">
        <div style="margin:10px">
            <form name="frmSemanticData" action="?" method="post">
                <div class="header"><?php echo _BIZ_UPDATE_SEMANTIC_DATA; ?></div>
                <div style="margin:20px">
                    <table>
                        <tr valign="top">
                            <td><?php echo _BIZ_SEMANTIC_DATA_KINDS; ?></td>
                            <td>&nbsp;</td>
                            <td><?php echo _BIZ_SEMANTIC_DATA_BIZOBJECTS; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <select id="semanticDataKindList" name="semanticDataKindList"
                                        multiple="multiple" size=10 style="width:11em">

                                    <option value="">(<?php echo _BIZ_ALL; ?>)</option>
                                    <?php
                                        $kindList = array_keys(getSemanticDataKindList());
                                        $kindList = array_combine($kindList, $kindList);
                                        renderHtmlOptions($kindList, $kinds);
                                    ?>
                                </select>
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <select id="semanticDataClassList" name="semanticDataClassList"
                                        multiple="multiple" size=10 style="width:10em">
        
                                    <option value="">(<?php echo _BIZ_ALL; ?>)</option>
                                    <?php
                                        $classList = getClassList();
                                        renderHtmlOptions($classList, $classNames);
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><?php echo _BIZ_SEMANTIC_DATA_WHERE; ?></td>
                            <td>&nbsp;</td>
                            <td>
                                <input type="text" id="semanticDataWhere" name="semanticDataWhere"
                                       value="<?php echoH8($where); ?>" style="width:10em">
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><?php echo _BIZ_SEMANTIC_DATA_FORCE_UPDATE; ?></td>
                            <td>&nbsp;</td>
                            <td>
                            <input type="checkbox" <?php if ($forceUpdate) echo 'checked' ?>
                                   id="semanticDataForceUpdate" name="semanticDataForceUpdate"
                                   style="align:left;width:10em">
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><input type="reset" value="<?php echoH8(_BIZ_RESET); ?>"></td>
                            <td>&nbsp;</td>
                            <td>
                                <input type="button" value="<?php echoH8(_BIZ_UPDATE); ?>"
                                       onClick="updateSemanticData($('semanticDataKindList').getValue(),
                                                                   $('semanticDataClassList').getValue(),
                                                                   $('semanticDataWhere').getValue(),
                                                                   $('semanticDataForceUpdate').checked)">
                            </td>
                        </tr>
                        <tr><td><div id="divSemanticDataStatus"></div></td></tr>
                    </table>
                </div>
            </form>
        </div>
    </td>
</tr>
</table>
<?php
    include(WCM_DIR . '/pages/includes/footer.php');