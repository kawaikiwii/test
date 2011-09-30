<?php
/**
 * Project:     WCM
 * File:        indexation.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

set_time_limit(3600);

$bizsearch = wcmBizsearch::getInstance();
$logmsg = null;

// Launch purge process?
$classList = getArrayParameter($_REQUEST, 'reindex', null);
if ($classList)
{
    $classes = explode(',', $classList);
    $logmsg .= _BIZ_STARTING . '<br/>';
    foreach($classes as $className)
    {
        $logmsg .= _BIZ_PROCESSING . ' ' . $className . '...<br/>';

        $where = null;
        $modificationDate = getArrayParameter($_REQUEST, 'modificationDate', '');
        if ($modificationDate !== null && $modificationDate != '')
        {
            // Reduce scope to corresponding bizobjects
            $where = "modifiedAt >= '" . date('Y-m-d', time() + ($modificationDate * 24 * 60 * 60)) . " 00:00:00'";
        }
        // Reindex all implies removing previously indexed content
        $bizsearch->deindexBizobjects($className);

        $bizobject = new $className;
        $bizobject->beginEnum($where, 'id');
        while($bizobject->nextEnum())
        {
            $bizsearch->indexBizobject($bizobject);
        }
        $bizobject->endEnum();
    }
    $logmsg .= _BIZ_DONE . '<br/>';
}

include(WCM_DIR . '/pages/includes/header.php');
?>
        <script language="javascript">
            function reindex()
            {
                var f = document.forms['frmIndexation'];
                var s = '';

                for (var i=0; i < f.classList.length; i++)
                {
                    if (f.classList[i].checked)
                    {
                        if (s != '') s += ',';
                        s += f.classList[i].value;
                    }
                }
                f.reindex.value = s;
                f.submit();
            }
        </script>
        <div style="margin:10px">
            <form name="frmIndexation" action="?" method="post">
            <input type="hidden" name="reindex" value=""/>
            <div class="header"> <?php echo _BIZ_REINDEX_CONTENT_TITLE; ?></div>
            <div style="margin:20px">
                <span class="warning"> <?php echo _BIZ_REINDEX_CONTENT_WARNING_MSG; ?><br/>
                &nbsp;<br/>
                <table>
                <tr valign="top">
                    <td>
                        <table cellspacing="1" cellpadding="3" border="0" bgcolor="#c0c0c0">
                        <tr bgcolor="#f4f4f4">
                            <td width="40" align="center"> <b> <?php echo _BIZ_INDEX; ?> </b> </td>
                            <td width="140"> &nbsp; <b> <?php echo _BIZ_KIND_ELEMENT; ?> </b> </td>
                            <td width="100" align="right"> <b> <?php echo _BIZ_NUMBER_RECORD_BUSINESS_DB; ?> </b> &nbsp; </td>
                            <td width="100" align="right"> <b> <?php echo _BIZ_NUMBER_RECORD_SEARCH_ENGINE; ?> </b> &nbsp; </td>
                        </tr>
                        <?php
                            $classList = getClassList();
                            $unIndexedClassList = array('chapter', 'pollChoice', 'admin1Codes', 'admin2Codes', 'alternate_names', 'city', 'continent', 'country', 'timeZone');
                            foreach($classList as $className => $classLabel)
                            {
                                $indexCount = $bizsearch->getCount($className);
                                if (!in_array($className, $unIndexedClassList))
                                {
                                    if (class_exists($className)) 
                                        $bizobject = new $className(wcmProject::getInstance());
                                    else
                                        continue;
    
                                    if ($bizobject)
                                    {
                                        $bizobject->beginEnum();
                                        $dbCount = $bizobject->enumCount();
                                        $bizobject->endEnum();
    
                                        echo "<tr bgcolor='#ffffff'>";
                                        if ($indexCount != $dbCount)
                                            echo "<td bgcolor='#f0c000' align='center'> <input type='checkbox' name='classList' value='$className' checked> </td>";
                                        else
                                            echo "<td align='center'> <input type='checkbox' name='classList' value='$className'> </td>";
                                        echo "<td> &nbsp; " . $classLabel . "</td>";
                                        echo "<td align='right'>" . $dbCount . " &nbsp; </td>";
                                        echo "<td align='right'>" . $indexCount . " &nbsp; </td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                        ?>
                        </table>
                    </td>
                    <td>
                        <div style="margin-left:30px; width:400px">
                            <table cellspacing="1" cellpadding="3" border="0">
                            <tr>
                                <td> <?php echo _MODIFICATION_DATE ?> </td>
                                <td>
                                    <select name="modificationDate" style="width:100%">
                                    <?php
                                        echo '<option value="">('._BIZ_ALL_DATES.')</option>';
                                        $dateList = getDateList();
                                        foreach($dateList as $date => $caption)
                                        {
                                            if ((int)$date <= 0)
                                            {
                                                echo '<option value="'.$date.'">'.$caption.'</option>';
                                            }
                                        }
                                    ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td align="right"> <img src="img/icons/indexation.gif" alt="" border="0"> </td>
                                <td> <a href="javascript:reindex()"><?php echo _BIZ_EXECUTE_REINDEXATION; ?></a> </td>
                            </tr>
                            </table>
                            <?php
                                if ($logmsg)
                                {
                                    echo '<div class="header">' . _BIZ_INDEXING_RESULT . '</div>';
                                    echo '<div style="margin-left:30px">' . $logmsg . '</div>';
                                }
                            ?>
                        </div>
                    </td>
                </tr>
                </table>
            </div>
            </form>
        </div>
<?php include(WCM_DIR . '/pages/includes/footer.php'); ?>