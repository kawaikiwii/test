<?php
/**
 * Project:     WCM
 * File:        placeElle.php
 *
 * @copyright   (c)2011 Relaxnews
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the photo
 */
class placeElleAction extends wcmMVC_BizAction
{
	
	private function cleanString($s)
	{
		$table = array(
        'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'Ć'=>'C', 'ć'=>'c', 'Ĉ'=>'C', 'ĉ'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y');

		$s = stripslashes($s);
	    $s = htmlspecialchars($s);
	    $s = strip_tags($s);
	    $s = str_replace(' ', '', $s);
	    $s = str_replace('\'', '', $s);
	    $s = strtr($s, $table);
	    $s = strtolower($s);
		return $s;
	}
	
	private function checkAndUpdateList($listCode, $value, $cacheArray)
	{
		$list = wcmList::getListLabelsFromParentCode($listCode);
		$token = explode(",", $value);
		if (sizeof($token) > 1)
		{
			foreach ($token as $val)
			{
				if (!in_array($val, $list))
				{
					$liste = new wcmList();
					$liste->refreshByCode($listCode);
					$liste->addList($this->cleanString($val), $val, $liste->id);
				}	
			}
			// si le tableau authors est en cache, on le vide pour forcer sa reconstruction
			$ArrayAC = wcmCache::fetch($cacheArray);
			if (!empty($ArrayAC))	wcmCache::store($cacheArray, "");
		}
		else if (!in_array($value, $list))
		{
			$liste = new wcmList();
			$liste->refreshByCode($listCode);
			$liste->addList($this->cleanString($value), $value, $liste->id);
			// si le tableau authors est en cache, on le vide pour forcer sa reconstruction
			$ArrayAC = wcmCache::fetch($cacheArray);
			if (!empty($ArrayAC))	wcmCache::store($cacheArray, "");
		}
		else
			return false;	
	}
	
	private function savePeople()
	{
		$people = getArrayParameter($_REQUEST, 'people');
		
		if (!empty($people))
			$this->checkAndUpdateList("elle_people", $people, "ArrayACListEPeople");
		
	}
	
 	/**
     * is called on checkin and on save before the store
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);
        
        $this->savePeople();
    }
}
