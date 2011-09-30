<?php

$session = wcmSession::getInstance();
$project = wcmProject::getInstance();


$lastImport = $session->getStats('kind=? AND info=?', array(wcmSession::STAT_IMPORT, 'dam'), 0, 1);

$dateCriteria[getConst(_BIZ_LAST_THREE_DAYS)] = mktime(0,0,0,date('n'),date('j')-3,date('Y'));
$dateCriteria[getConst(_BIZ_LAST_SEVEN_DAYS)] = mktime(0,0,0,date('n'),date('j')-7,date('Y'));
$dateCriteria[getConst(_BIZ_LAST_MONTH)] = mktime(0,0,0,date('m')-1,date('j'),date('Y'));

if ($lastImport->getRecordCount())
{
    $lastImport->next();
    $rs = $lastImport->getRow();
    
    $dateCriteria[getConst(_BIZ_LAST_IMPORT)] = strtotime($rs['dateAndTime']);
}

wcmGUI::openForm('importForm','dialogs/import.php','',array('target'=>'importLog'));
wcmGUI::renderHiddenField('siteId',wcmSession::getInstance()->getSiteId());
wcmGUI::RenderHiddenField('plugin','wcmImportDAM');

$config = wcmConfig::getInstance();
?>

<h3><?php echo _BIZ_IMPORT_CRITERIA; ?></h3>


<fieldset>
    <legend><?php echo _BIZ_CLASSES; ?></legend>
    <ul>
        <?php 
        for ($i = 0; isset($config['dam.importRules.allowedClasses.class.'.$i.'.name']); $i++)
        {
            $name = $config['dam.importRules.allowedClasses.class.'.$i.'.name'];
            $damObject = $config['dam.importRules.allowedClasses.class.'.$i.'.damObject'];
            $description = $config['dam.importRules.allowedClasses.class.'.$i.'.description'];
            ?>
            <li>
                <label>
                    <input type="checkbox" name="classes[<?php echo $damObject; ?>]" /> <?php echo $name; ?> 
                    <?php if (!empty($description)): ?> - <?php echo $description; ?><?php endif; ?>
                </label>
            </li>
            <?php
        }
        ?>
    </ul>
</fieldset>
<fieldset>
    <legend><?php echo _BIZ_IMPORT_FROM_WHEN; ?></legend>
    <ul>
        <li>
            <label><?php echo _BIZ_IMPORT_FROM_WHEN; ?></label> 
            <select name="publicationDate">
                <?php foreach ($dateCriteria as $title => $timestamp): ?>
                <option value="<?php echo $timestamp; ?>"><?php echo $title; ?></option>
                <?php endforeach; ?>
            </select>
        </li>
    </ul>
</fieldset>
<fieldset>
    <legend><?php echo _BIZ_IMPORT_FROM_WHERE; ?></legend>
    <ul>
        <li>
            <label><?php echo _BIZ_DAM_WEB_SERVICE_URL; ?></label>
            <input type="text" name="webServiceURL" value="<?php echo $config['dam.webServices']; ?>" size="80" />
        </li>
        <li>
            <label><?php echo _BIZ_IMPORT_DAME_REPOSITORY; ?></label>
            <input type="text" name="mrWebServiceURL" value="<?php echo $config['dam.mediaRepository']; ?>" size="80" />
        </li>
        <li>
            <label><?php echo _USERNAME; ?></label>
            <input type="text" name="login" />
        </li>
        <li>
            <label><?php echo _PASSWORD; ?></label>
            <input type="password" name="password" />
        </li>
        <li>
            <label><?php echo _BIZ_USERID; ?></label>
            <input type="text" name="userId" />
        </li>
    </ul>
</fieldset>
<fieldset>
    <legend><?php echo _BIZ_MISCELLANEOUS; ?></legend>
    <ul>
        <li>
            <label><?php echo _BIZ_XSL_FOLDER; ?></label>
            <input type="text" name="xslFolder" value="<?php echo WCM_DIR; ?>/business/import/xsl/DAM/" size="80" />
        </li>
    </ul>
</fieldset>
<button type="submit" name="simpleImport" onclick="window.open('','importLog','resizable=disallow,scrollbars=1,location=0,status=1,toolbar=0,width=800,height=400'); submit();"><?php echo _BIZ_IMPORT; ?></button>

