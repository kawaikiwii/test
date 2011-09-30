<?php
/**
 * Project:     WCM
 * File:        work.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the channel
 */
class workAction extends wcmMVC_BizAction
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

	/**
	 * Save contents from content tab
	 */
	private function saveContents()
	{
		if ( isset ($_REQUEST['content_work_title']))
		{
			$contentsTitle = getArrayParameter($_REQUEST, 'content_work_title');
			$contentsDescription = getArrayParameter($_REQUEST, 'content_work_description');
			$contentsText = getArrayParameter($_REQUEST, 'content_work_text');
			$titleSigns = getArrayParameter($_REQUEST, 'content_work_titleSigns');
			$titleWords = getArrayParameter($_REQUEST, 'content_work_titleWords');
			$descriptionSigns = getArrayParameter($_REQUEST, 'content_work_descriptionSigns');
			$descriptionWords = getArrayParameter($_REQUEST, 'content_work_descriptionWords');
			$textSigns = getArrayParameter($_REQUEST, 'content_work_textSigns');
			$textWords = getArrayParameter($_REQUEST, 'content_work_textWords');
			$channelIds = getArrayParameter($_REQUEST, 'channelIds');
			$listIds = getArrayParameter($_REQUEST, 'listIds');
			$folderIds = getArrayParameter($_REQUEST, 'folderIds');

			$contents = array (	'title' =>  $contentsTitle,
							   	'description' => $contentsDescription,
								'text' => $contentsText,
								'titleSigns' => $titleSigns,
								'titleWords' => $titleWords,
								'descriptionSigns' => $descriptionSigns,
								'descriptionWords' => $descriptionWords,
								'textSigns' => $textSigns,
								'textWords' => $textWords);

			$this->context->updateSerialStorage('channelIds', $channelIds);
			$this->context->updateSerialStorage('listIds', $listIds);
			$this->context->updateSerialStorage('folderIds', $folderIds);
			$this->context->updateContents($contents);
		}
	}

	private function saveWorkTypeInfo()
	{
		$arrayTypeInfo = array();
		$save = false;

		if ( isset ($_REQUEST['type']) && !empty($_REQUEST['type']))
		{
			switch ($_REQUEST['type'])
			{
				case "cd":
					$arrayTypeInfo["otype"] = getArrayParameter($_REQUEST, 		'cd_otype');
			        $arrayTypeInfo["titlenb"] = getArrayParameter($_REQUEST, 	'cd_titlenb');
					$arrayTypeInfo["summary"] = getArrayParameter($_REQUEST, 	'cd_summary');
			        
					$arrayTypeInfo["musicStyle"] = getArrayParameter($_REQUEST, 	'cd_musicStyle');
			        if (!empty($arrayTypeInfo["musicStyle"]))
			        	$this->checkAndUpdateList("work_cdmusicstyle", $arrayTypeInfo["musicStyle"], "ArrayACListWCdMusicStyle");
					
			        $arrayTypeInfo["country"] = getArrayParameter($_REQUEST, 	'cd_country');
			        if (!empty($arrayTypeInfo["country"]))
			        	$this->checkAndUpdateList("work_country", $arrayTypeInfo["country"], "ArrayACListWCountry");

			        $arrayTypeInfo["label"] = getArrayParameter($_REQUEST, 		'cd_label');
			        if (!empty($arrayTypeInfo["label"]))
			        	$this->checkAndUpdateList("work_cdlabel", $arrayTypeInfo["label"], "ArrayACListWCdLAbel");

			        $save = true;
			        break;
		        case "cinema":
		        	$arrayTypeInfo["originalTitle"] = getArrayParameter($_REQUEST, 	'cinema_originalTitle');
			        $arrayTypeInfo["duration"] = getArrayParameter($_REQUEST, 		'cinema_duration');
			        $arrayTypeInfo["copies"] = getArrayParameter($_REQUEST, 		'cinema_copies');
			        $arrayTypeInfo["director"] = getArrayParameter($_REQUEST, 		'cinema_director');
			        $arrayTypeInfo["casting"] = getArrayParameter($_REQUEST, 		'cinema_casting');
			        $arrayTypeInfo["summary"] = getArrayParameter($_REQUEST, 		'cinema_summary');
			        
			        $arrayTypeInfo["gender"] = getArrayParameter($_REQUEST, 		'cinema_gender');
			        if (!empty($arrayTypeInfo["gender"]))
			        	$this->checkAndUpdateList("work_moviegender", $arrayTypeInfo["gender"], "ArrayACListWMovieGender");

			        $arrayTypeInfo["country"] = getArrayParameter($_REQUEST, 		'cinema_country');
			        if (!empty($arrayTypeInfo["country"]))
			        	$this->checkAndUpdateList("work_country", $arrayTypeInfo["country"], "ArrayACListWCountry");

			        $save = true;
			        break;
			    case "video":
			        $arrayTypeInfo["originalTitle"] = getArrayParameter($_REQUEST, 	'video_originalTitle');
			        $arrayTypeInfo["duration"] = getArrayParameter($_REQUEST, 		'video_duration');
			        $arrayTypeInfo["copies"] = getArrayParameter($_REQUEST, 		'video_copies');
			        $arrayTypeInfo["director"] = getArrayParameter($_REQUEST, 		'video_director');
			        $arrayTypeInfo["casting"] = getArrayParameter($_REQUEST, 		'video_casting');
			        $arrayTypeInfo["format"] = getArrayParameter($_REQUEST, 		'video_format');
					$arrayTypeInfo["summary"] = getArrayParameter($_REQUEST, 		'video_summary');
			        
			        $arrayTypeInfo["gender"] = getArrayParameter($_REQUEST, 		'video_gender');
			        if (!empty($arrayTypeInfo["gender"]))
			        	$this->checkAndUpdateList("work_moviegender", $arrayTypeInfo["gender"], "ArrayACListWMovieGender");

			        $arrayTypeInfo["country"] = getArrayParameter($_REQUEST, 		'video_country');
			        if (!empty($arrayTypeInfo["country"]))
			        	$this->checkAndUpdateList("work_country", $arrayTypeInfo["country"], "ArrayACListWCountry");

			        $arrayTypeInfo["bonus"] = getArrayParameter($_REQUEST, 			'video_bonus');
			        $arrayTypeInfo["price"] = getArrayParameter($_REQUEST, 			'video_price');

			        $save = true;
			        break;
			    case "book":
			        $arrayTypeInfo["price"]  = getArrayParameter($_REQUEST, 	'book_price');
			        $arrayTypeInfo["pagenb"] = getArrayParameter($_REQUEST, 	'book_pagenb');
					$arrayTypeInfo["summary"] = getArrayParameter($_REQUEST, 	'book_summary');
			        
					$arrayTypeInfo["format"] = getArrayParameter($_REQUEST, 	'book_format');
			        
			        $arrayTypeInfo["gender"] = getArrayParameter($_REQUEST, 	'book_gender');
			        if (!empty($arrayTypeInfo["gender"]))
			        	$this->checkAndUpdateList("work_bookgender", $arrayTypeInfo["gender"], "ArrayACListWBookGender");

			        $arrayTypeInfo["theme"] = getArrayParameter($_REQUEST, 	'book_theme');
			        if (!empty($arrayTypeInfo["theme"]))
			        	$this->checkAndUpdateList("work_booktheme", $arrayTypeInfo["theme"], "ArrayACListWBookTheme");
					
					$arrayTypeInfo["country"] = getArrayParameter($_REQUEST, 	'book_country');
			        if (!empty($arrayTypeInfo["country"]))
			        	$this->checkAndUpdateList("work_country", $arrayTypeInfo["country"], "ArrayACListWCountry");
			        		        	
			        $save = true;
			        break;
			    case "product":
			        $arrayTypeInfo["author"] = getArrayParameter($_REQUEST, 	'product_author');
			        $arrayTypeInfo["producer"] = getArrayParameter($_REQUEST, 	'productproducer');
			        $arrayTypeInfo["summary"] = getArrayParameter($_REQUEST, 	'product_summary');
			        
			        $arrayTypeInfo["country"] = getArrayParameter($_REQUEST, 	'product_country');
			        if (!empty($arrayTypeInfo["country"]))
			        	$this->checkAndUpdateList("work_country", $arrayTypeInfo["country"], "ArrayACListWCountry");
			        		        	
			        
			        $save = true;
			        break;
			    case "videogame":
			        $arrayTypeInfo["developer"] = getArrayParameter($_REQUEST, 	'videogame_developer');
			        $arrayTypeInfo["public"] = getArrayParameter($_REQUEST, 	'videogame_public');
			        $arrayTypeInfo["plateforms"] = getArrayParameter($_REQUEST, 'videogame_plateforms');
			        $arrayTypeInfo["price"] = getArrayParameter($_REQUEST, 		'videogame_price');
					$arrayTypeInfo["summary"] = getArrayParameter($_REQUEST, 	'videogame_summary');
			        
			        $arrayTypeInfo["gender"] = getArrayParameter($_REQUEST, 		'videogame_gender');
			        if (!empty($arrayTypeInfo["gender"]))
			        	$this->checkAndUpdateList("work_videogamegender", $arrayTypeInfo["gender"], "ArrayACListWVideoGameGender");

			        $arrayTypeInfo["country"] = getArrayParameter($_REQUEST, 		'videogame_country');
			        if (!empty($arrayTypeInfo["country"]))
			        	$this->checkAndUpdateList("work_country", $arrayTypeInfo["country"], "ArrayACListWCountry");

			        $save = true;
			        break;
			}
		}
		if ($save == true)
		{
			$this->context->updateWorkTypeInfo($arrayTypeInfo);
		}
	}

	/**
	 * beforeSaving is called on checkin and on save before the store
	 *
	 * @param wcmSession $session Current session
	 * @param wcmProject $project Current project
	 */
	protected function beforeSaving($session, $project)
	{
		parent::beforeSaving($session, $project);
		$this->saveContents();
		$this->saveWorkTypeInfo();
	}

}

