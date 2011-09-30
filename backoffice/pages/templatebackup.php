<?php
/**
 * Project:     WCM
 * File:        templatebackup.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */

    // Include header
    include('includes/header.php');

    if(isset($_REQUEST['backup']))
    {
        $filename = $config['wcm.templates.backupPath']."nstein.wcm.4.templates.backup.".date("Y.m.d.H.i").".tar.gz";
        $zip = new wcmZip($filename, $config['wcm.templates.path'], '.svn', 'tmp');
    }
    
    if(isset($_REQUEST['delete']))
    {
        $filetodelete = $_REQUEST['delete'];
        unlink($config['wcm.templates.backupPath'].$filetodelete);
    }
    
    
    $dir = new DirectoryIterator($config['wcm.templates.backupPath']);
    
?>

    <div id="assetbar">

        <h3><?php echo _TEMPLATE_EXPORT; ?></h3>

        <ul>
            <li><a href="?_wcmAction=templatebackup&backup=1" class="backup"><?php echo _LAUNCH_EXPORT; ?></a></li>
        </ul>

    </div>

<div class="backup list">

        <table>
            <tr>
                <th width="50"></th>
                <th><?php echo _BACKED_UP_DATE; ?></th>
                <th><?php echo _FILE_LOCATION_CLICK; ?></th>
            </tr>
            
<?php

    $keys = array();
    $backups = array();
    
    foreach($dir as $resource) 
    {
        if($resource->isFile())  
        {
            $backups[$resource->getCTime()] = $resource->getFilename();    
        }
        
    }
    
    arsort($backups);
    
    $urltobackup = str_replace(WCM_DIR, '', $config['wcm.templates.backupPath']);
    $wcmdir = str_replace('\\','/',WCM_DIR);
    $urltobackup = str_replace($wcmdir, '', $urltobackup);
    $urltobackup = $config['wcm.backOffice.url'].$urltobackup;
    
    foreach($backups as $time => $name)
    {
?>

            <tr>
                <td>
                    <ul>
                        <li><a href="?_wcmAction=templatebackup&delete=<?php echo $name;?>"><?php echo _DELETE; ?></a></li>
                    </ul>
                </td>
                <td><?php echo date("Y/m/d @ h:i",$time);?></td>
                <td><a href="<?php echo $urltobackup.$name; ?>"><?php echo $name;?></a></td>
            </tr>
            
<?php
    }   
?>

        </table>

    </div>

<?php
   
    include('includes/footer.php');
    
?>