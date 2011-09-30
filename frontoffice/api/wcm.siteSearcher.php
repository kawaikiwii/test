<?php 
/*
 * Project:     WCM
 * File:        business/web/ajax/search/bizsearch.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Helper class to execute search command and render result
 */

class wcmSiteSearcher {
    // Request-specific infos
    public $config;
    public $bizsearch;
    public $pageSize;
    public $queryString;
    public $id;
    public $result;
    
    public $bRequest = array("query"=>array(), "params"=>array());
    
    public $classnameTranslated = array("news"=>_BIZ_NEWS, "event"=>_BIZ_EVENT, "video"=>_BIZ_VIDEO, "slideshow"=>_BIZ_SLIDESHOW, "notice"=>_BIZ_NOTICE, "prevision"=>"Prévision");
    
    public $sortAlias = array("title"=>"title_sort", "publicationDate"=>"publicationDate", "mainChannel"=>"channelid", "classname"=>"classname", "startDate"=>"startDate", "endDate"=>"endDate", "event_startdate"=>"event_startdate", "event_enddate"=>"event_enddate", "prevision_startdate"=>"prevision_startdate", "prevision_enddate"=>"prevision_enddate");
    
    /**
     * Constructor
     *
     * Initializes the following properties:
     *
     * - $config
     * - $bizsearch
     * - $pageSize
     *
     * @param int $pageSize The page size (default is 25)
     */

    public function __construct($bRequest) {
        $today = date("Y-m-d\TH:i:s");
        /* $yesterday = today - 6 months */
        $yesterday = date("Y-m-d\T00:00:00", mktime(0, 0, 0, date("m") - 6, date("d"), date("Y")));
        
        $this->config = wcmConfig::getInstance();
        $this->bizsearch = wcmBizsearch::getInstance();
        $this->session = wcmSession::getInstance();
        
        $this->bRequest["query"]["siteid"] = $this->session->getSiteId();
        $this->bRequest["query"]["workflowstate"] = "published";
        $this->bRequest["query"]["classname"] = 'news,video,slideshow,event,notice,prevision';
        $this->bRequest["query"]["publicationdate"] = "[$yesterday to $today]";
        $this->bRequest["query"]["fulltext"] = null;
        $this->bRequest["query"]["channelid"] = null;
        $this->bRequest["query"]["channelids"] = null;
        $this->bRequest["query"]["id"] = null;
        $this->bRequest["query"]["folderid"] = null;
        $this->bRequest["query"]["listid"] = null;
        
        //$this->bRequest["query"]["accountPermission"] = true;
        
        $this->bRequest["params"] = $bRequest["params"];
        $this->bRequest["query"] = array_merge($this->bRequest["query"], $bRequest["query"]);
        
        $this->pageSize = 25;
    }

    private function getSort($sort, $dir) {
    
        $sort = $this->sortAlias[$sort];
        $baseSort = "publicationDate $dir, publicationTime $dir";
        
        if ($sort != "publicationDate") {
            return ("$sort $dir , $baseSort");
        } else {
            return ($baseSort);
        }
    }

    private function purgeFulltext() {
        if ($fulltext = $this->bRequest["query"]["fulltext"]) {
            // Purge fisrt and last whitespace
            $fulltext = trim($fulltext);
            // Purge double whitespace
            $fulltext = preg_replace('/[\s]+/', ' ', $fulltext);
            // Manage exact string (e.g. "some words") : replace inner space with special char ¤
            $fulltext = preg_replace('/"([^"]*)"/e', "'\"'.str_replace(' ','¤','\\1').'\"'", $fulltext);
            // Made AND by default
            $fulltext = preg_replace('/[\s]+/', ' +', $fulltext);
            $fulltext = preg_replace('/(\+-)+/', '-', $fulltext);
            $fulltext = preg_replace('/(\+\|)+/', '|', $fulltext);
            
            $ftArray = preg_split('/[\s]+/', $fulltext);
            $miArray = array();
            $orArray = array();
            
            foreach ($ftArray as $item) {
                switch (substr($item, 0, 1)) {
                    case "-":
                        $miArray[] = substr($item, 1);
                        $fulltext = str_replace($item, "", $fulltext);
                        break;
                    case "|":
                        $orArray[] = substr($item, 1);
                        $fulltext = str_replace($item, "", $fulltext);
                        break;
                }
            }
            
            if (count($miArray) > 0) {
                $fulltext .= " -(".implode(",", $miArray).")";
            }
            if (count($orArray) > 0) {
                $fulltext .= " |(".implode(",", $orArray).")";
            }
            
            // Manage exact string (e.g. "some words") : replace special char ¤ with inner space
            $fulltext = preg_replace('/"([^"]*)"/e', "'\"'.str_replace('¤',' ','\\1').'\"'", $fulltext);
            $this->bRequest["query"]["fulltext"] = "(".trim($fulltext).")";
        }
    }

    public function execute() {
        $this->purgeFulltext();
        $this->bRequest["query"]["sortedby"] = $this->getSort($this->bRequest["params"]["sort"], $this->bRequest["params"]["dir"]);
        $this->id = "search-".$this->session->userId."-".$this->bRequest["params"]["ctId"];
        
        $query = $this->bRequest["query"];
        
        if ($this->bRequest["query"]["folderid"]) {
            $folder = new folder();
            $folder->refresh($this->bRequest["query"]["folderid"]);
            if ($folder->type == "auto" && $folder->request != "") {
                $request = unserialize($folder->request);
                $query = (isset($request['query']) ? $query." AND (".$request['query']." OR folderid:$folder->id)" : $query);
                
                if (isset($request['query'])) {
                    $this->bRequest["query"]["folderid"] = "(folderid:$folder->id OR ".$request['query'].")";
                }
            }
            unset($folder);
            $query = $this->bRequest["query"];
        }
        
        if (isset($this->bRequest["query"]["bincontent"])) {
            $query = $this->bRequest["query"]["bincontent"];
        }
        
        $this->bRequest["params"]["executionTime"] = date("Y-m-d\TH:i:s");
        
        $numFound = $this->bizsearch->initSearch($this->id, $query, $this->bRequest["query"]["sortedby"], "FO");
        
        $this->computeResult($numFound);
    }
    
    /**
     * Computes the search result.
     *
     * Initalizes the following properties:
     *
     * - $result
     *
     * @param int $numFound The number of items found
     * @param int $pageNum  The current page number (default is 1)
     */

    private function computeResult($numFound = null, $pageNum = 1) {
        $result = ($this->result ? $this->result : new StdClass );
        
        if ($numFound === null) {
            $numFound = $result->numFound;
        }
        
        $pageMax = ceil($numFound / $this->pageSize);
        if ($pageNum > $pageMax) {
            $pageNum = 1;
        }
        
        $result->numFound = $numFound;
        $result->pageMax = $pageMax;
        $result->pageNum = $pageNum;
        $result->first = ($pageNum - 1) * $this->pageSize + 1;
        $result->last = $result->first + min($this->pageSize, $numFound - $result->first + 1) - 1;
        
        $this->result = $result;
    }

    public function getResult($start, $limit) {
        $this->session->setLanguage($this->session->getSite()->language);
        
        $aResults = array();
        
        foreach ($this->bizsearch->getDocumentRange($start, $start + $limit, $this->id, false) as $item) {
            $aResults[] = $item;
        }
        return ($aResults);
    }

    public function getResultJSON($start, $limit, $list = false) {
        $this->session->setLanguage($this->session->getSite()->language);
        $config = wcmConfig::getInstance();
        
        $aResults = array("resultsCount"=>$this->result->numFound, "executionTime"=>$this->bRequest["params"]["executionTime"],
        
            //"fulltext"=>$this->bRequest["query"]["fulltext"],
            "tmlRequest"=>$this->bizsearch->getNativeQuery(), "query"=>$this->bizsearch->getQuery(), "ctId"=>$this->bRequest["params"]["ctId"], "searchId"=>$this->id,
            //"SITEID" => $this->session->getSiteId(),
            //"LANGUAGE" => $this->session->getLanguage(),
            );
        clearstatcache();
        foreach ($this->bizsearch->getDocumentRange($start, $start + $limit, $this->id, false) as $item) {
            $mainChannel = "undefined";
            $rootChannel = "undefined";
            $root = "undefined";
            
            $categs = $item->getAssoc_categorization();
            
            $root = @$categs["parentChannel_title"];//"_RLX_WELLBEING";//$GLOBALS["RUBRICS"][$lang][$item->channelId]["rootTitle"];
            $rootChannel = @$item->getAssoc_mainChannelCss(); //"wellbeing";//strtolower(substr($GLOBALS["RUBRICS"][$lang][$item->channelId]["rootTitle"], 5, strlen($GLOBALS["RUBRICS"][$lang][$item->channelId]["rootTitle"])));
            $mainChannel = @$categs["mainChannel_title"];//"WB";//getConst($GLOBALS["RUBRICS"][$lang][$item->channelId]["title"]);
            
        	if (trim($item->title) != "") {
            	$related = $item->getAssoc_relateds();
            	$photos = array();
            	$miniature = "";
            	if($related) {
            		foreach($related as $relation) {
	            		if($relation["relation"]["destinationClass"] == "photo") {
	            			$photos = $item->getRelatedsByClassAndKind("photo");
	            			break;
	            		}
	            		elseif($related[0]["relation"]["destinationClass"] == "work") {
	            			$related2 = $related[0]["object"]->getAssoc_relateds();
	            			if($related2) {
	            				foreach($related2 as $relation2) {
	            					if($relation2["relation"]["destinationClass"] == "photo") {
		            					$photos = $related2;
		            					break;
	            					}
	            				}
	            			}
	            		}
            		}
            	}
            	elseif(isset($item->properties["illustration"][1]["thumbnail"]) && is_file("../../repository/illustration/photo/archives/".$item->properties["illustration"][1]["thumbnail"]))
            		$miniature = "http://repository.relaxnews.net/illustration/photo/archives/".$item->properties["illustration"][1]["thumbnail"];
            	
                $aItem = array();
                $aItem["classname"][] = $item->getClass();
                $aItem["classnameTranslated"][] = $this->classnameTranslated[$item->getClass()];
                $aItem["id"][] = $item->id;
                $aItem["rootChannel"][] = $rootChannel;
                $aItem["mainChannel"][] = $mainChannel;
                $aItem["root"][] = $root;
                $aItem["title"][] = $this->highlightKeyword($item->title);
                if($item->isAllowed("primaire","children") || $item->getClass() == "prevision") {
                    $aItem["description"][] = $this->highlightKeyword($item->getDescription());
                    if ($photos) {
                    	foreach($photos as $photo) {
                    		if($photo["relation"]["destinationClass"] == "photo") {
                    			$aItem["photo"][] = $photo["object"]->getPhotoUrlByFormat("s50", "w250");
                    			break;
                        	}
                    	}
                    }
                    elseif($miniature != "")
                    	$aItem["photo"][] = $miniature;
                    else
                    	$aItem["photo"][] = $config['wcm.webSite.urlRepository']."images/default/no-pic.png";
                	$aItem["allowed"][] = "true";
                }
                else {
                    $aItem["description"][] = "";
                    $aItem["allowed"][] = "false";
                }
                $aItem["publicationDate"][] = ($item->publicationDate) ? $item->publicationDate : null;
                $aItem["startDate"][] = ($item->getClass() == 'event' || $item->getClass() == 'prevision') ? $item->startDate : null;
                $aItem["endDate"][] = ($item->getClass() == 'event' || $item->getClass() == 'prevision') ? $item->endDate : null;
                
                if ($item->getClass() == 'news')
                    $aItem["breaking"][] = (isset($item->listIds) && $item->listIds != "") ? in_array(247, (unserialize($item->listIds))) : false;
                if ($list)                
                    $aItem["list"][] = $this->getListObject($item->getClass(), $item->id);
                
                $aResults["results"][] = $aItem;
            }
        }
        return (json_encode($aResults)."\n");
    }

    public function highlightKeyword($str) {
    
        if (isset($this->bRequest["query"]["fulltext"])) {
            $keywords = $this->bRequest["query"]["fulltext"];
            $keywords = preg_replace('@[^a-zA-Z0-9_\"\sàâäçèéêëîïôöùûüÀÂÄÇÈÉÊËÎÏÔÖÙÛÜ%]@', '', $keywords);
            $str = preg_replace("#\b(".implode("|", preg_split("/[\s,]+/", $keywords)).")\b#i", "<span class='ari-search-hit'>$0</span>", $str);
            $str = preg_replace("#(<a[^>]*)<span class='ari\-search\-hit'>#", "$1", $str);
            $str = preg_replace("#(<a[^>]*)</span>#", "$1", $str);
            $str = preg_replace("#(<img[^>]*)<span class='ari\-search\-hit'>#", "$1", $str);
            $str = preg_replace("#(<img[^>]*)</span>#", "$1", $str);
            $str = preg_replace("#(<img[^>]*)<span class='ari\-search\-hit'>#", "$1", $str);
            $str = preg_replace("#(<img[^>]*)</span>#", "$1", $str);
            $str = preg_replace("#(<[^>]*)<span class='ari\-search\-hit'>#", "$1", $str);
            $str = preg_replace("#(<[^>]*)</span>#", "$1", $str);
        }
        return ($str);
    }

    private function getListObject($classname, $id) {
        $project = wcmproject::getInstance();
        $config = wcmConfig::getInstance();
        
        $bizObject = new $classname($project, $id);
        $permalinks = str_replace("%format%", "list", $bizObject->permalinks);
        $filename = $config['wcm.webSite.repository'].$permalinks;
        
        if (file_exists($filename)) {
            return (file_get_contents($filename));
            exit;
            
        }
    }

    private function fieldDateToArray($date, $gmt = null) {
        $dateFormat = array('hour'=>substr($date, 11, 2), 'minute'=>substr($date, 14, 2), 'second'=>substr($date, 17, 2), 'month'=>substr($date, 5, 2), 'day'=>substr($date, 8, 2), 'year'=>substr($date, 0, 4));
        
        $dateFormat['mktime'] = mktime($dateFormat['hour'], $dateFormat['minute'], $dateFormat['second'], $dateFormat['month'], $dateFormat['day'], $dateFormat['year']);
        
        if ($gmt == null) {
            $gmt = 0;
        }
        $dateFormat['mktimeGmt'] = mktime(($dateFormat['hour'] - intval($gmt)), $dateFormat['minute'], $dateFormat['second'], $dateFormat['month'], $dateFormat['day'], $dateFormat['year']);
        
        return $dateFormat;
    }
    
}

?>
