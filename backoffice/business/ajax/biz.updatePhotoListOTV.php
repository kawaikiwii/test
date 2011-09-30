<?php
/**
 * Project:     WCM
 * File:        biz.updatePhotoList.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();
$config = wcmConfig::getInstance();
$photoPath = WCM_DIR.DIRECTORY_SEPARATOR.$config['wcm.backOffice.photosPathOTV'];
    
// Retrieve parameters
$command	= getArrayParameter($_REQUEST, "command", null);
$folder	= getArrayParameter($_REQUEST, "folder", null);

header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<ajax-response>\n";
echo "<response type=\"item\" id=\"photosList\"><![CDATA[";
switch ($command)
{
	case "updatePhotoList":
		echo "<table>";
		$_SESSION['pathImagesOTV'] = $folder;
		$dir = new DirectoryIterator($photoPath.$folder);
		
		$cpt = 1;
		foreach ($dir as $file)
		{
			if (!$file->isDir())
			{
				$filename = basename($file);
				if ($cpt ==  1)
					echo '<tr>';

				echo '<td><img id="'.$config['wcm.backOffice.photosPathOTV'].$folder.DIRECTORY_SEPARATOR.$filename.'" width="100px" src="'.$config['wcm.backOffice.url'].$config['wcm.backOffice.photosPathOTV'].$folder.DIRECTORY_SEPARATOR.$filename.'" style="cursor:pointer" onClick="selectedPhoto(this.id)"; /></td>';

				if ($cpt == 6)
				{
					echo '</tr>';
					$cpt = 0;
				}
				$cpt++;
			}
		}
		echo '</tr></table>';
		if ($cpt == 1)
			echo "<br/>Pas d'image dans ce dossier";
		break;
	default:
		break;
}
echo "]]></response>";
echo "</ajax-response>";