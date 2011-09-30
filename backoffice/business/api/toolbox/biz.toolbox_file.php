<?php
/**
 * Takes a numerical ID and applies basic math to obscure it
 *
 * @param int $argId ID to encode
 * @return int Encoded ID
 */
function encodeId($argId)
{
    $seaSalt = mt_rand(1,98);
    $salt = ($seaSalt & 1)? $seaSalt + 1 : $seaSalt;
    $id = $argId * ($salt / 2) + $seaSalt;
    $seaSalt = (string) ($seaSalt < 10)? '0' . $seaSalt : $seaSalt;
    $id .= $seaSalt;
    return (int) $id;
}

/**
 * Decodes a number that was created using encodeId()
 *
 * @param int $argId Number to decode
 * @return int Decoded ID
 */
function decodeId($argId)
{
    $seaSalt = substr($argId, strlen($argId)-2,2);
    $id = substr($argId, 0, strlen($argId)-2);
    $seaSalt = (int) ($seaSalt{0} == 0)? $seaSalt{1} : $seaSalt;
    $salt = ($seaSalt & 1)? $seaSalt + 1 : $seaSalt;
    $id = ($id - $seaSalt) / ($salt / 2);
    return (int) $id;
}

/**
 * Generates a download link.
 * 
 * This function creates a link pointing to download.php with the following
 * three query parameters:
 * h: A security hash
 * f: An encoded file id
 * u: An encoded user id
 *
 * @param mixed $argFile Can be either an item id or an item bizobject
 * @param mixed $argUser Can be either a user id or a user bizobject
 * @param int $argExpire Number of days link should stay active, defaults to 0
 *                       which means link will expire the next day. 
 * @return string Download link.
 */
function generateProtectedLink($argFile,$argUser,$argExpire=0)
{
    
    $fileId = (is_numeric($argFile))? $argFile : $argFile->id;
    $userId = (is_numeric($argUser))? $argUser : $argUser->id;
    
    $fileId = encodeId($fileId);
    $userId = encodeId($userId);
    
    $expire = mktime(23,59,59,date('n'),date('j')+$argExpire,date('Y'));
    
    $hash = md5($expire.$fileId.$userId).$expire;
    $link = 'download.php?h='.$hash.'&f='.$fileId.'&u='.$userId;
    return $link;
}

/**
 * Checks to see if the hash supplied is a valid hash.
 *
 * @param string $argHash A 32 character md5 hash string
 * @param int $argFileId Encoded file id
 * @param int $webuserId Encoded user id
 * @return bool True if hash is valid, false if it isn't
 */
function validateHash($argHash,$argFileId,$webuserId)
{
    $hash = substr($argHash,0,32);
    $expire = substr($argHash,32,strlen($argHash));
    if (mktime() > $expire) return false;
    return (md5($expire.$argFileId.$webuserId) == $hash)? true : false;
}

/**
 * Output's a file to the browser.
 * 
 * Reads the file to standard output, passing along all necessary headers
 * to force the web browser to download the file. Ignores file type.
 *
 * @param  mixed $argFile Either an item bizobject or an item id
 * @param  int   $webuserId ID of user downloading the file, if any
 * @return bool  True on success, false on failure.
 */
function downloadFile($argFile, $webuserId=0)
{
    if (is_numeric($argFile))
    {
        $project = wcmProject::getInstance();
        $file = new item($project,$argFile);
    }
    else
    {
        $file =& $argFile;
    }
    
    if (is_file($file->location))
    {
        $file->markAsDownloaded($webuserId);
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private',false);
        header('Content-Type: application/force-download');
        header('Content-disposition: attachment; filename='.basename($file->location));
        header('Content-Transfer-Encoding: binary');
        header('Content-Lenght: '.filesize($file->location));
        readfile($file->location);
        return true;
    }
    return false;
}

/**
 * Get a list of available files
 * 
 * You can supply an access level to limit the list to specific files.
 * Returns a Creole result set which can be iterated over using
 * foreach()
 *
 * @param int $argAccess Can be null or item::ACCESS_PUBLIC, item::ACCESS_PRIVATE, item::ACCESS_PROTECTED
 * @return object Creole result set
 */
function listFiles($argAccess = '')
{
    $project    = wcmProject::getInstance();
    $connector  = $project->datalayer->getConnectorByReference("biz");
    $db = $connector->getBusinessDatabase();
    
    if ($argAccess != '')
    {
        $query = 'SELECT id FROM biz_item WHERE access=?';
        $params[] = $argAccess;
        $res = $db->executeQuery($query, $params);
    } else {
        $query = 'SELECT id FROM biz_item';
        $res = $db->executeQuery($query);
    } 
    return $res;
}