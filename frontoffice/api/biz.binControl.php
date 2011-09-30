<?php 
/**
 * Project:     WCM
 * File:        biz.binSearchControl.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

class binControl {
    protected $project;
    protected $session;
    
    /**
     * Constructor
     */

    public function __construct() {
        $this->session = wcmSession::getInstance();
        $this->project = wcmProject::getInstance();
    }
    
    /**
     * Create a new bin
     *
     * @return
     */

    public function create($name, $description = "", $content = "") {
        $bin = new wcmBin($this->project);
        
        $bin->name = $name;
        $bin->description = $description;
        $bin->userId = $this->session->userId;
        $bin->content = $content;
        $bin->dashboard = $this->session->getSiteId();
        
        $bin->save();
    }
    
    /**
     *
     *
     * @return
     */

    function remove($ids) {
    
        $aId = explode(",", $ids);
        foreach ($aId as $id) {
            $bin = new wcmBin($this->project);
            $bin->id = $id;
            $bin->removeOne();
        }
    }
    
    /**
     * Retrieve the user's bins
     *
     * @return array user's bin
     */

    public function getUserBins() {
        $aBins = array();
        
        $bin = new wcmBin($this->project);
        $where = "userId = ".$this->session->userId." AND dashboard=".$this->session->getSiteId();
        if ($bin->beginEnum($where, "name")) {
            while ($bin->nextEnum()) {
                $aBin = array();
                $aBin["text"] = $bin->name;
                $aBin["id"] = $bin->id;
                $aBin["leaf"] = true;
                $aBin["iconCls"] = "ari-bin";
                $aBin["checked"] = false;
                $aBins[] = $aBin;
            }
        }
        return ($aBins);
    }
    
    /**
     * Retrieve the user's bins
     *
     * @return array user's bin
     */

    public function getUserBinsMenu($cmpId) {
        $aBins = array();
        
        $bin = new wcmBin($this->project);
        $where = "userId = ".$this->session->userId." AND dashboard=".$this->session->getSiteId();
        if ($bin->beginEnum($where, "name")) {
            while ($bin->nextEnum()) {
                $handler = "function(item) {ARe.bin.folderToBin(".$bin->id.", '$cmpId')}";
                if ($cmpId == "preview") {
                    $handler = "function(item) {ARe.bin.previewToBin(".$bin->id.")}";
                }
                
                $aBin = array();
                $aBin["text"] = $bin->name;
                $aBin["handler"] = $handler; // "function(item) {ARe.bin.folderToBin(".$bin->id.", '$cmpId')}";
                $aBins[] = $aBin;
            }
        }
        return ($aBins);
    }
    
    /**
     * Retrieve the user's bin data given by id
     *
     * @param int $id	bin id
     */

    public function getBinData($id, $json = true) {
        $value = "";
        $bin = new wcmBin($this->project);
        if ($bin->beginEnum("id=".$id)) {
        	$tabClass = array();
            while ($bin->nextEnum()) {
                $aContents = explode('/', $bin->content);
                if ($aContents) {
                    $ini = 0;
                    foreach ($aContents as $aContent) {
                        if ($aContent) {
                            list($objectClass, $objectId) = explode('_', $aContent, 2);
                            if ($objectClass && $objectId) {
                            	
                            	$tabClass[$objectClass][$objectId] = 1;
                            	
                                /*if ($ini == 0) {
                                    $value .= "(classname:".$objectClass." and objectId:".$objectId.")";
                                } else {
                                    $value .= " OR (classname:".$objectClass." and objectId:".$objectId.")";
                                }
                                $ini++;*/
                            }
                        }
                    }
                }
            }
            $cptClass = 0;
            foreach($tabClass as $classes=>$classe) {
            	if($cptClass > 0)
            		$value .= " OR ";
            	$value .= "(classname:".$classes." AND ";
            	$cptObject = 0;
            	foreach($classe as $objects=>$object) {
	            	if ($cptObject == 0)
	            		$value .= "objectId:(";
	            	else
	            		$value .= ",";
	            	$value .= $objects;
	            	$cptObject++;
            	}
            	$value .= "))";
            	$cptClass++;
            }
        }
        
        if ($value == "") {
            return (null);
        }
        
        $bRequest = array("query"=>array(), "params"=>array());
        
        $bRequest["params"]["ctId"] = (isset($_REQUEST["ctId"]) && $_REQUEST["ctId"] != "") ? $_REQUEST["ctId"] : 0;
        $bRequest["params"]["start"] = (isset($_REQUEST["start"]) && $_REQUEST["start"] != "") ? $_REQUEST["start"] : 0;
        $bRequest["params"]["limit"] = (isset($_REQUEST["limit"]) && $_REQUEST["limit"] != "") ? $_REQUEST["limit"] : 15;
        $bRequest["params"]["sort"] = (isset($_REQUEST["sort"]) && $_REQUEST["sort"] != "") ? $_REQUEST["sort"] : null;
        $bRequest["params"]["dir"] = (isset($_REQUEST["dir"]) && $_REQUEST["dir"] != "") ? $_REQUEST["dir"] : null;
        
        $bRequest["query"]["bincontent"] = $value;
        $bRequest["query"]["folderid"] = null;
        $bRequest["query"]["fulltext"] = null;
        
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        
        if ($json) {
            return ($siteSearch->getResultJSON($bRequest["params"]["start"], $bRequest["params"]["limit"]));
        } else {
            return ($siteSearch->getResult($bRequest["params"]["start"], $bRequest["params"]["limit"]));
        }
        
    }

    public function getDocuments($id, $json = true) {
        $news_value = "";
	$slideshow_value = "";
	$video_value = "";
	$prevision_value = "";
        $bin = new wcmBin($this->project);
        if ($bin->beginEnum("id=".$id)) {
            while ($bin->nextEnum()) {
                $aContents = explode('/', $bin->content);
                if ($aContents) {
                	$ini = 0;
			$ini_news = 0;
			$ini_slideshow = 0;
			$ini_video = 0;
			$ini_prevision = 0;
		
			$count_aContents = count($aContents);
                    foreach ($aContents as $aContent) {
                        if ($aContent) {
                            list($objectClass, $objectId) = explode('_', $aContent, 2);
                            if ($objectClass && $objectId) {
				switch($objectClass){
					case 'news':
						if ($ini_news == 0) {
				                    $news_value .= "(classname:".$objectClass." and objectId:(".$objectId.", ";
				                } else {
				                    $news_value .= $objectId.", ";
				                }
						$ini_news++;	
					break;
					case 'slideshow':
						if ($ini_slideshow == 0) {
				                    $slideshow_value .= "(classname:".$objectClass." and objectId:(".$objectId.", ";
				                } else {
				                    $slideshow_value .= $objectId.", ";
				                }
						$ini_slideshow++;	
					break;
					case 'video':
						if ($ini_video == 0) {
				                    $video_value .= "(classname:".$objectClass." and objectId:(".$objectId.", ";
				                } else {
				                    $video_value .= $objectId.", ";
				                }
						$ini_video++;	
					break;
					case 'prevision':
						if ($ini_prevision == 0) {
				                    $prevision_value .= "(classname:".$objectClass." and objectId:(".$objectId.", ";
				                } else {
				                    $prevision_value .= $objectId.", ";
				                }
						$ini_prevision++;
					break;
				}
				
                                $ini++;
				//on a parcouru tous les éléments
				if($ini+1 == $count_aContents){
					$value = substr($news_value,0,-2)."))";
					if($slideshow_value != ""){
						$value .= " OR ".substr($slideshow_value,0,-2)."))";
					}
					if($video_value != ""){
						$value .= " OR ".substr($video_value,0,-2)."))";
					}
					if($prevision_value != ""){
						$value .= " OR ".substr($prevision_value,0,-2)."))";
					}
				}
                            }
                        }
                    }
                }
            }
        }
        
        if ($value == "") {
            return (null);
        }
        
        $bRequest = array("query"=>array(), "params"=>array());
        
        $bRequest["params"]["ctId"] = "binDocuments$id";
        $bRequest["params"]["start"] = 0;
        $bRequest["params"]["limit"] = $ini;
        $bRequest["params"]["sort"] = "publicationDate";
        $bRequest["params"]["dir"] = "DESC";
        
        $bRequest["query"]["bincontent"] = $value;
        $bRequest["query"]["folderid"] = null;
        $bRequest["query"]["fulltext"] = null;
	
        $siteSearch = new wcmSiteSearcher($bRequest);
        $siteSearch->execute();
        
        return ($siteSearch->getResult($bRequest["params"]["start"], $bRequest["params"]["limit"]));
        
    }
    
    /**
     * Adds a given item to the selected bin.
     *
     * @param int    $id      The bin ID
     * @param string $content The item content to add
     */

    function addToBin($id, $content) {
        $aContentToAdd = split("/", $content);
        
        $bin = new wcmBin($this->project);
        if ($bin->beginEnum("id=".$id)) {
            while ($bin->nextEnum()) {
                $contentArray = explode('/', $bin->content);
                foreach ($aContentToAdd as $item) {
                    $key = array_search($item, $contentArray);
                    if ($key === false) {
                        $contentArray[] = $item;
                    }
                }
                $bin->content = implode('/', $contentArray);
                $bin->save();
            }
        }
    }
    
    /**
     * Removes a given item from the selected bin.
     *
     * @param int    $id      The bin ID
     * @param string $content The item content to remove
     */

    function removeFromBin($id, $content) {
        $aContentToRemove = split("/", $content);
        
        $bin = new wcmBin($this->project);
        
        if ($bin->beginEnum("id=".$id)) {
            while ($bin->nextEnum())
                $contentArray = explode('/', $bin->content);
        }
        
        foreach ($aContentToRemove as $item) {
            $key = array_search($item, $contentArray);
            if ($key !== false) {
                unset($contentArray[$key]);
            }
        }
        
        $bin->content = implode('/', $contentArray);
        $bin->save();
    }
    
    /**
     * Removes all items from bin
     *
     * @param int    $id      The bin ID
     * @param string $content The item content to remove
     */

    function clear($ids) {
        $aId = explode(",", $ids);
        foreach ($aId as $id) {
            $bin = new wcmBin($this->project);
            $bin->refresh($id);
            $bin->content = "";
            $bin->save();
        }
    }
    
}

?>
