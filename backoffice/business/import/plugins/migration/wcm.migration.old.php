<?php 
/**
 * Project:     WCM
 * File:        wcm.wcmMigration.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */


class wcmMigration extends wcmGenericImport {
    protected $GROUPS_FOLDER = '/opt/nfs/_MIGRATION_/groups';
    protected $USERS_FOLDER = '/opt/nfs/_MIGRATION_/users';
    protected $CATEGORIES_FOLDER = '/opt/nfs/_MIGRATION_/categories';
    protected $NEWS_FOLDER = '/opt/nfs/_MIGRATION_/news';
    protected $FORECAST_FOLDER = '/opt/nfs/_MIGRATION_/forecasts';
    protected $BASKETS_FOLDER = '/opt/nfs/_MIGRATION_/baskets';
    /*
     protected $GROUPS_FOLDER = '/data/ncm/relaxnews/WCM/bo.afprelax.net/__imports/groups';
     protected $USERS_FOLDER = '/data/ncm/relaxnews/WCM/bo.afprelax.net/__imports/users';
     protected $CATEGORIES_FOLDER = '/data/ncm/relaxnews/WCM/bo.afprelax.net/__imports/categories';
     protected $NEWS_FOLDER = '/data/ncm/relaxnews/WCM/bo.afprelax.net/__imports/news';
     protected $BASKETS_FOLDER = '/data/ncm/relaxnews/WCM/bo.afprelax.net/__imports/baskets';
     */
    protected $wcmBiz = null;
    protected $wcmSys = null;
    protected $siteId = 0;
    protected $step = 0;
    protected $currentNewsFolder = 990;
    
    public $wkflow = array("Usable"=>'published', "ValidationPending"=>'submitted', "Valid"=>'approved');
    
    public $icmCatToWcmChannel = array("195"=>"37957", "196"=>"38007", "197"=>"38008", "198"=>"38009", "199"=>"38010", "200"=>"38986", "201"=>"38987", "202"=>"38012", "203"=>"38984", "204"=>"38985", "205"=>"38013", "206"=>"38988", "207"=>"38989", "208"=>"38564", "209"=>"38568", "210"=>"39046", "211"=>"37958", "212"=>"38025", "213"=>"38029", "214"=>"38030", "215"=>"38077", "216"=>"38982", "217"=>"38983", "218"=>"38565", "219"=>"38570", "220"=>"37959", "221"=>"38014", "222"=>"38015", "223"=>"38017", "224"=>"38018", "225"=>"39025", "226"=>"38019", "227"=>"38026", "228"=>"38027", "229"=>"38078", "230"=>"38389", "231"=>"38990", "232"=>"38991", "233"=>"38566", "234"=>"38571", "235"=>"38628", "236"=>"39023", "237"=>"39024", "238"=>"37960", "239"=>"38016", "240"=>"38992", "241"=>"38993", "242"=>"38028", "243"=>"38079", "244"=>"38080", "245"=>"38082", "246"=>"38567", "247"=>"38572");
    
    public $icmFolderToWcmFolder = array("600"=>"38762", "601"=>"38689", "602"=>"38690", "603"=>"38763", "604"=>"38764", "605"=>"38053", "606"=>"38756", "607"=>"38765", "608"=>"38766", "609"=>"38767", "610"=>"38768", "611"=>"38769", "612"=>"38833", "613"=>"38834", "614"=>"38837", "615"=>"38838", "616"=>"38840", "617"=>"38841", "618"=>"38767", "619"=>"38846", "620"=>"38847", "621"=>"38850", "622"=>"38851", "623"=>"38852", "624"=>"38853", "625"=>"38854", "626"=>"38841", "627"=>"38865", "628"=>"38866", "629"=>"38867", "630"=>"38868", "631"=>"38877", "632"=>"38880", "633"=>"38881", "634"=>"38887", "635"=>"38888", "636"=>"38891", "637"=>"38892", "638"=>"38893", "639"=>"38894", "640"=>"38898", "641"=>"38899", "642"=>"38904", "643"=>"38905", "644"=>"38918", "645"=>"38919", "646"=>"38920", "647"=>"38921", "648"=>"38922", "649"=>"38923", "650"=>"38924", "651"=>"38946", "652"=>"38947", "653"=>"38948", "654"=>"38949", "655"=>"38950", "656"=>"38951", "657"=>"38953", "658"=>"38954", "659"=>"38955", "660"=>"38956", "661"=>"38957", "662"=>"38958", "663"=>"38961", "664"=>"38962", "665"=>"38963", "666"=>"38964", "667"=>"38965", "668"=>"38966", "669"=>"38967", "670"=>"38968", "671"=>"38969", "672"=>"38970", "673"=>"38971", "674"=>"38972", "675"=>"38973", "676"=>"38979", "677"=>"38980", "678"=>"39043", "679"=>"39044", "680"=>"39045", "681"=>"39048", "682"=>"39052", "683"=>"39060", "684"=>"39062", "685"=>"39063", "686"=>"39073", "687"=>"39074", "688"=>"39080", "689"=>"39081", "690"=>"39082", "691"=>"39083", "692"=>"39088", "693"=>"39089", "694"=>"39111", "695"=>"39352", "696"=>"39355", "697"=>"39410", "698"=>"39411", "699"=>"39413", "700"=>"38846", "701"=>"39420", "702"=>"39423", "703"=>"39424", "704"=>"39429", "705"=>"39431", "706"=>"39432", "707"=>"39433", "708"=>"39434", "709"=>"39435", "710"=>"39436", "711"=>"39437", "712"=>"39438", "713"=>"39442", "714"=>"39443", "715"=>"39444", "716"=>"39445", "717"=>"39447", "718"=>"39448", "719"=>"39450", "720"=>"39451", "721"=>"39452", "722"=>"39453", "723"=>"39458", "724"=>"39466", "725"=>"39467", "726"=>"39472", "727"=>"39473", "728"=>"39474", "729"=>"39475", "730"=>"39476", "731"=>"39477", "732"=>"39478", "733"=>"39479", "734"=>"39480", "735"=>"39487", "736"=>"39488", "737"=>"39489", "738"=>"39490", "739"=>"39491", "740"=>"39492", "741"=>"39498", "742"=>"39499", "743"=>"39500", "744"=>"39501", "745"=>"39503", "746"=>"39504", "747"=>"39505", "748"=>"39506", "749"=>"39507", "750"=>"39513", "751"=>"39514", "752"=>"39515", "753"=>"39517", "754"=>"39518", "755"=>"39519", "756"=>"39520", "757"=>"39521", "758"=>"39522", "759"=>"39538", "760"=>"39539", "761"=>"39541", "762"=>"39543", "763"=>"39544", "764"=>"39545", "765"=>"39546", "766"=>"39547", "767"=>"39558", "768"=>"39560", "769"=>"39561", "770"=>"39566", "771"=>"39579", "772"=>"39580", "773"=>"39581", "774"=>"39582", "775"=>"39583", "776"=>"39585", "777"=>"39590", "778"=>"39591", "779"=>"39592", "780"=>"39593", "781"=>"39594", "782"=>"39599", "783"=>"39600", "784"=>"39601", "785"=>"39602", "786"=>"39603", "787"=>"39604");
    
    public $icmListCatToWcmList = array("107"=>"38774", "112"=>"38776", "105"=>"38778", "102"=>"38779", "111"=>"38780", "109"=>"38839", "106"=>"39049", "104"=>"39051", "100"=>"39415", "101"=>"39416", "103"=>"39417", "108"=>"39418", "110"=>"39419", "123"=>"38772", "124"=>"38773", "128"=>"38849", "243"=>"38869", "1564"=>"38925", "245"=>"38781", "245"=>"38876", "225"=>"38997", "226"=>"38998", "227"=>"38999", "228"=>"39000", "229"=>"39001", "230"=>"39002", "232"=>"39004", "233"=>"39005", "234"=>"39006", "235"=>"39007", "242"=>"39008", "247"=>"38871", "248"=>"39017", "249"=>"39018");


    public function __construct(array $parameters) {
        $this->siteId = $parameters['siteId'];
        $this->step = $parameters['step'];
        if (isset($parameters['newsFolder']))
            $this->currentNewsFolder = $parameters['newsFolder'];
            
        parent::__construct($parameters);
    }
    
    /**
     * Launch importation process
     */

    public function process() {
        $exitCode = 0;
        
        $this->connectToBizDatabase("mysql.afprelax.local", "RELAX_BIZ", "relaxweb", "kzq!2007", 3306);
        $this->connectToSysDatabase("mysql.afprelax.local", "RELAX_SYS", "relaxweb", "kzq!2007", 3306);
        /*
         $this->connectToBizDatabase ( "192.168.0.6", "relax_biz", "ncm", "ncm!2007", 3306 );
         $this->connectToSysDatabase ( "192.168.0.6", "relax_sys", "ncm", "ncm!2007", 3306 );
         */
        switch ($this->step) {
            case 1:
                echo "-=- Import des groupes -=-\r\n";
                //$this->importGroups();
                break;
                
            case 2:
                echo "-=- Import des utilisateurs -=-\r\n";
                //$this->importUsers();
                break;
                
            case 3:
                echo "-=- Import des comptes -=-\r\n";
                //$this->importAccounts();
                break;
                
            case 4:
                echo "-=- Import des piliers -=-\r\n";
                //$this->importPillars();
                break;
                
            case 5:
                echo "-=- Import des catégories -=-\r\n";
                //$this->importCategories();
                break;
                
            case 6:
                echo "-=- Import des news($this->currentNewsFolder)-=-\r\n";
                $this->importAllNews();
                break;
                
            case 7:
                echo "-=- Import des paniers -=-\r\n";
                //$this->importBaskets();
                break;
                
            case 8:
                echo "-=- Import des prévisions -=-\r\n";
                $this->importAllForecasts();
                break;
        }
        exit($exitCode);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function connectToBizDatabase($hostname, $dbname, $username, $pw, $port) {
        try {
            $this->wcmBiz = new PDO("mysql:host=$hostname; dbname=$dbname", $username, $pw);
        }
        catch(PDOException $e) {
            echo("Connexion à la base de données $dbname impossible : ".$e->getMessage()."\r\n");
            $this->wcmBiz = null;
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function connectToSysDatabase($hostname, $dbname, $username, $pw, $port) {
        try {
            $this->wcmSys = new PDO("mysql:host=$hostname; dbname=$dbname", $username, $pw);
        }
        catch(PDOException $e) {
            echo("Connexion à la base de données $dbname impossible : ".$e->getMessage()."\r\n");
            $this->wcmSys = null;
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importBaskets() {
        $count = 0;
        foreach (glob($this->BASKETS_FOLDER."/*.xml") as $filename) {
            $xml = simplexml_load_file($filename);
            $this->importBasket($xml);
            $count++;
            if ($count == 10)
                break;
        }
        
        return 0;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importBasket($xml) {
        $query = $this->wcmBiz->prepare("SELECT wcmId FROM __migration3 WHERE icmUserId = ".$xml->userId);
        $query->execute();
        if ($row = $query->fetch()) {
            $basket = new wcmBin();
            
            $basket->name = (string) $xml->name;
            $basket->description = (string) $xml->caption;
            $basket->userId = $row['wcmId'];
            $basket->dashboard = 0;
            $basket->content = "";
            
            foreach ($xml->documents->news as $news) {
                $query = $this->wcmBiz->prepare("SELECT wcmId FROM __migration5 WHERE icmDocId = '".$news['id']."'");
                $query->execute();
                if ($row = $query->fetch()) {
                    $basket->content .= '/news_'.$row['wcmId'];
                } else {
                    echo "Correspondance de la news '".$news['id']."' non trouvée.\r\n";
                }
            }
            
            //if ($basket->content != "")
            //    $basket->content = substr($basket->content, 0, strlen($basket->content)-1);
            
            $basket->save();
            $this->wcmBiz->exec("insert into __migration6 values (".(string) $xml['id'].",".$basket->id.")");
            
            echo "Création du panier: ".$xml->caption."\r\n";
        } else {
            echo "Correspondance de l'utilisateur '$userId' non trouvée.\r\n";
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importAllNews() {
        $count = 0;
        foreach (glob($this->NEWS_FOLDER."/".$this->currentNewsFolder."/*.xml") as $filename) {
            $xml = simplexml_load_file($filename);
            $this->importNews($xml);
            
            echo "  $count : unlink($filename)\n";
            unlink($filename);
            $count++;
            if ($count == 1000) {
                echo "Break :count : $count\n";
                break;
            }
        }
        
        return 0;
    }

    public function importAllForecasts() {
        $count = 0;
        foreach (glob($this->FORECAST_FOLDER."/*.xml") as $filename) {
            $xml = simplexml_load_file($filename);
            $this->importForecast($xml);
            
            echo "  $count : unlink($filename)\n";
            unlink($filename);
            
            $count++;
            if ($count == 50) {
                echo "Break :count : $count\n";
                break;
            }
        }
        
        return 0;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importNews($xml) {
        //require_once(WCM_DIR.'/business/import/wcm.commonRelax.php');
        
        $status = $xml->head->system['status'];
        if ($status == "Usable") {
        
            $news = new news();
            $news->siteId = $this->siteId;
            $news->title = (string) $xml->head->administrative->titles->title;
            
            $refDate = explode("/", (string) $xml->head->managment->released['date']);
            if (! empty($refDate) && isset($refDate[0]))
                $refYear = (int) $refDate[0];
                
            $news->publicationDate = str_replace("/", "-", (string) $xml->head->managment->released['date'])." ".$xml->head->managment->released['time'];
            if ((string) $xml->head->managment->embargoed['date'] != "")
                $news->embargoDate = str_replace("/", "-", (string) $xml->head->managment->embargoed['date']);
            if ((string) $xml->head->managment->retired['date'] != "")
                $news->expirationDate = str_replace("/", "-", (string) $xml->head->managment->retired['date']);
                
            $tabList = array();
            $tabFold = array();
            $tabChan = array();
            
            foreach ($xml->head->categories->category as $category) {
                $categorieId = $category['id'];
                $wcmCategorie = 0;
                $isList = true;
                // on passe par la table de correspondance pour déterminer l'id channel à partir de l'id catégorie ICMdoc
                
                $chanId = array_search($categorieId, $this->icmCatToWcmChannel);
                if ($category['main'] == "true") {
                    if ($chanId)
                        $news->channelId = $chanId;
                }
                
                if ($chanId)
                    $tabChan[] = $chanId;
                    
                //$this->getCategories($categorieId, $wcmCategorie, $isList);
                
                //if ($isList == true && $wcmCategorie != 0)
                //    $news->listIds[] = $wcmCategorie;
                //if ($isList == false && $wcmCategorie != 0)
                //    $news->channelIds[] = $wcmCategorie;
                
                $listId = array_search($categorieId, $this->icmListCatToWcmList);
                if ($listId)
                    $tabList[] = $listId;
                    
                // on passe par la table de correspondance pour déterminer l'id folder à partir de l'id folder ICMdoc
                $foldId = array_search($categorieId, $this->icmFolderToWcmFolder);
                if ($foldId)
                    $tabFold[] = $foldId;
            }
            
            if (count($tabChan) > 0)
                $news->channelIds = serialize($tabChan);
            if (count($tabList) > 0)
                $news->listIds = serialize($tabList); //serialize($tabList);
            if (count($tabFold) > 0)
                $news->folderIds = serialize($tabFold); // serialize($tabFold);
                
            $archiveIllustration = array();
            $indice = 1;
            foreach ($xml->head->links->link as $link) {
                if ((isset($refYear) && $refYear >= 2007) && (strtolower((string) $link["rel"]) == "hasillustration" && strtolower((string) $link["type"]) == "image") || strtolower((string) $link["targetType"]) == "image/gif" || strtolower((string) $link["targetType"]) == "image/jpg" || strtolower((string) $link["targetType"]) == "image/jpeg" || strtolower((string) $link["targetType"]) == "image/jpeg" || strtolower((string) $link["targetType"]) == "image/png" || strtolower((string) $link["targetType"]) == "image/tiff" || strtolower((string) $link["targetType"]) == "image/bmp") {
                    $archiveIllustration[$indice]["legend"] = (string) $link->legend;
                    $archiveIllustration[$indice]["rights"] = (string) $link->rights;
                    foreach ($link->content as $linkContent) {
                        $path = $link["path"];
                        if ($path == "")
                            $path = $linkContent["path"];
                            
                        $rendition = (string) $linkContent["rendition"];
                        $archiveIllustration[$indice][$rendition] = (string) $path.$linkContent["href"];
                        /*
                         if (strtolower((string) $linkContent["rendition"]) == "original")
                         {
                         $path = $link["path"];
                         if ($path == "")
                         $path = $linkContent["path"];
                         if (!isset($news->properties['illustration']))
                         $news->properties['illustration'] = "";
                         $news->properties['illustration'] .= $path.$linkContent["href"]."|";
                         }
                         */
                    }
                    $indice++;
                }
            }
            
            /*
             if (isset($news->properties['illustration']))
             $news->properties['illustration'] = substr($news->properties['illustration'], 0, strlen($news->properties['illustration']) - 1);
             */
            $news->properties['illustration'] = $archiveIllustration;
            
            //$news->sourceLocation = (string) $xml->info->location['val'];
            $news->source = 12;
            
            $news->import_feed = 'icm';
            $news->import_feed_id = (string) $xml['guid'];
            
            /*
             $contents = array (	'title' =>  $news->title,
             'description' => (string) $xml->head->administrative->descriptions->description,
             'text' => (string) $xml->content->dataContent,
             'provider' => "icm");
             
             $news->updateContents($contents);
             */
            $news->mustGenerate = false;
            // $news->save();
            //$this->wcmBiz->exec("UPDATE biz_news SET import_feed_id = '".(string) $xml['guid']."' WHERE ID = ".$news->id);
            
            $permalinks = "";
            
            $newsTitle = utf8_decode($news->title);
            $newsTitle = str_replace("'", "''", $newsTitle);
            
            $insert1 = 'INSERT INTO `biz_news` (`createdAt`, `modifiedAt`,`createdBy`, `modifiedBy`, `workflowState`, `properties`, `xmlTags`, `mustGenerate`, `source`, `sourceId`, `sourceVersion`, `sourceLocation`, `permalinks`, `siteId`, `channelId`, `channelIds`, `listIds`, `folderIds`, `publicationDate`, `expirationDate`, `embargoDate`, `title`, `import_feed`, `import_feed_id`, `embedVideo`) VALUES (NOW(), NOW(), 1, 1, "published", \''.serialize($news->properties).'\', "a:0:{}", 1, "12", NULL, NULL, NULL, "'.$permalinks.'", 6, '.$news->channelId.', "'.$news->channelIds.'", "'.$news->listIds.'", "'.$news->folderIds.'", "'.$news->publicationDate.'", NULL, NULL, \''.$newsTitle.'\', "icm", "'.(string) $xml['guid'].'", NULL)';
            //echo $insert1."\n";
            
            $this->wcmBiz->exec($insert1);
            
            $newsId = 0;
            $query = $this->wcmBiz->prepare("SELECT id FROM biz_news WHERE import_feed = 'icm' AND import_feed_id = '$news->import_feed_id'");
            $query->execute();
            if ($row = $query->fetch()) {
                $newsId = $row["id"];
                
            }
            
            if ($newsId != 0) {
                $permadate = str_replace("-", "/", $news->publicationDate);
                $permadate = str_replace(" ", "/", $permadate);
                $permadate = str_replace(":", "", $permadate);
                
                $permalinks = "sites/fra/news/$permadate.$newsId.%format%.html";
                
                $update1 = "UPDATE biz_news SET permalinks = '".$permalinks."' WHERE ID = ".$newsId;
                //echo $update1."\n";
                
                $this->wcmBiz->exec($update1);
                
                $description = ereg_replace("<!--[^>]*->", "", utf8_decode((string) $xml->head->administrative->descriptions->description));
                $text = ereg_replace("<!--[^>]*->", "", utf8_decode((string) $xml->content->dataContent));
                
                $description = str_replace("'", "''", $description);
                $text = str_replace("'", "''", $text);
                
                $insert2 = 'INSERT INTO `biz_content` (`referentId`, `referentClass`, `format`, `lang`, `provider`, `authorId`, `title`, `titleContentType`, `titleSigns`, `titleWords`, `description`, `descriptionContentType`, `descriptionSigns`, `descriptionWords`, `text`, `textContentType`, `textSigns`, `textWords`) VALUES ( '.$newsId.', "news", "default", "fr", "icm", 1, \''.$newsTitle.'\', "text", NULL, NULL, \''.$description.'\', "xhtml", NULL, NULL, \''.$text.'\', "xhtml", NULL, NULL)';
                //echo $insert2."\n\n";
                
                $this->wcmBiz->exec($insert2);
                
                echo "Création de la news: ".$newsId." (".(string) $xml['guid'].")\r\n";
                $this->wcmBiz->exec("insert into __migration5 values ('".(string) $xml['guid']."',".$newsId.")");
            }
        }
        
    }

    public function importForecast($xml) {
        //require_once(WCM_DIR.'/business/import/wcm.commonRelax.php');
        $endDate = 0;
        $data = (string) $xml->content->characteristics['forecastDateEnd'];
        $endTab = explode("/", $data);
        if (! empty($endTab) && isset($endTab[0]))
            $endDate = (int) $endTab[0];
        // si la date de fin est supérieure à 2010 on traite le fichier
        
        $forecast = new forecast();
        $forecast->siteId = $this->siteId;
        $forecast->title = (string) $xml->head->administrative->titles->title;
        $forecast->type = "";
        
        $status = $xml->head->system['status'];
        if ($status == "Usable") {
            $forecast->sourceLocation = "published";
        }
        
        $forecast->publicationDate = str_replace("/", "-", (string) $xml->head->managment->released['date'])." ".$xml->head->managment->released['time'];
        if ((string) $xml->head->managment->embargoed['date'] != "")
            $forecast->embargoDate = str_replace("/", "-", (string) $xml->head->managment->embargoed['date']);
        if ((string) $xml->head->managment->retired['date'] != "")
            $forecast->expirationDate = str_replace("/", "-", (string) $xml->head->managment->retired['date']);
            
        $forecast->startDate = str_replace("/", "-", (string) $xml->content->characteristics['forecastDateBegin']);
        $forecast->endDate = str_replace("/", "-", (string) $xml->content->characteristics['forecastDateEnd']);
        
        $tabList = array();
        $tabFold = array();
        $tabChan = array();
        
        foreach ($xml->head->categories->category as $category) {
            $categorieId = $category['id'];
            $wcmCategorie = 0;
            $isList = true;
            // on passe par la table de correspondance pour déterminer l'id channel à partir de l'id catégorie ICMdoc
            
            $chanId = array_search($categorieId, $this->icmCatToWcmChannel);
            if ($category['main'] == "true") {
                if ($chanId)
                    $forecast->channelId = $chanId;
            }
            
            if ($chanId)
                $tabChan[] = $chanId;
                
            $listId = array_search($categorieId, $this->icmListCatToWcmList);
            if ($listId)
                $tabList[] = $listId;
                
            // on passe par la table de correspondance pour déterminer l'id folder à partir de l'id folder ICMdoc
            $foldId = array_search($categorieId, $this->icmFolderToWcmFolder);
            if ($foldId)
                $tabFold[] = $foldId;
        }
        
        $forecast->channelIds = serialize($tabChan);
        $forecast->listIds = serialize($tabList);
        $forecast->folderIds = serialize($tabFold);
        /*
         $refDate = explode("/", (string) $xml->head->managment->released['date']);
         if (! empty($refDate) && isset($refDate[0]))
         $refYear = (int) $refDate[0];
         */
        $archiveIllustration = array();
        $indice = 1;
        foreach ($xml->head->links->link as $link) {
            //if ((isset($refYear) && $refYear >= 2007) && (strtolower((string) $link["rel"]) == "hasillustration" && strtolower((string) $link["type"]) == "image") || strtolower((string) $link["targetType"]) == "image/gif" || strtolower((string) $link["targetType"]) == "image/jpg" || strtolower((string) $link["targetType"]) == "image/jpeg" || strtolower((string) $link["targetType"]) == "image/jpeg" || strtolower((string) $link["targetType"]) == "image/png" || strtolower((string) $link["targetType"]) == "image/tiff" || strtolower((string) $link["targetType"]) == "image/bmp") {
            if ((strtolower((string) $link["rel"]) == "hasillustration" && strtolower((string) $link["type"]) == "image") || strtolower((string) $link["targetType"]) == "image/gif" || strtolower((string) $link["targetType"]) == "image/jpg" || strtolower((string) $link["targetType"]) == "image/jpeg" || strtolower((string) $link["targetType"]) == "image/jpeg" || strtolower((string) $link["targetType"]) == "image/png" || strtolower((string) $link["targetType"]) == "image/tiff" || strtolower((string) $link["targetType"]) == "image/bmp") {
                $archiveIllustration[$indice]["legend"] = (string) $link->legend;
                $archiveIllustration[$indice]["rights"] = (string) $link->rights;
                foreach ($link->content as $linkContent) {
                    $path = $link["path"];
                    if ($path == "")
                        $path = $linkContent["path"];
                        
                    $rendition = (string) $linkContent["rendition"];
                    $archiveIllustration[$indice][$rendition] = (string) $path.$linkContent["href"];
                }
                $indice++;
            }
        }
        
        $forecast->properties['illustration'] = $archiveIllustration;
        
        $forecast->source = 12;
        $forecast->sourceLocation = (string) $xml->info->location['val'];
        $forecast->mustGenerate = false;
        $forecast->save();
        
        $content = new content();
        $content->referentId = $forecast->id;
        $content->referentClass = $forecast->getClass();
        $content->provider = "icm";
        
        $content->title = $forecast->title;
        $content->description = (string) $xml->head->administrative->descriptions->description;
        $content->text = (string) $xml->content->dataContent;
        $content->save();
        
        echo "Création de la prévision: ".(string) $forecast->id." ( ".$xml['guid'].") \r\n";
        
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function getCategories($categorieId, &$wcmCategorie, &$isList) {
        $wcmCategorie = 0;
        $query = $this->wcmBiz->prepare("SELECT wcmId, isList FROM __migration4 WHERE icmCategoryId = ".$categorieId);
        $query->execute();
        if ($row = $query->fetch()) {
            $wcmCategorie = $row["wcmId"];
            $isList = $row["isList"];
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importCategories() {
        foreach (glob($this->CATEGORIES_FOLDER."/category_0_*.xml") as $filename) {
            $xml = simplexml_load_file($filename);
            if (strtolower((string) $xml->name) != "pillar" && strtolower((string) $xml->name) != "geographie") {
                $this->importCategory($xml, 0, null);
            }
        }
        
        return 0;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importCategory($xml, $level, $parentId) {
        $category = new wcmList();
        $category->parentId = $parentId;
        $category->code = (string) $xml->name;
        $category->label = (string) $xml->caption;
        $category->save();
        $this->wcmBiz->exec("insert into __migration4 values (".(string) $xml['id'].",".$category->id.", 1)");
        
        echo "Création de la catégorie: ".$xml['path']."\r\n";
        foreach (glob($this->CATEGORIES_FOLDER."/category_".++$level."_".(string) $xml['path']."-*.xml") as $filename) {
            $xml = simplexml_load_file($filename);
            $this->importCategory($xml, $level, $category->id);
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importPillars() {
        foreach (glob($this->CATEGORIES_FOLDER."/category_0_*.xml") as $filename) {
            $xml = simplexml_load_file($filename);
            if (strtolower((string) $xml->name) == "pillar") {
                foreach (glob($this->CATEGORIES_FOLDER."/category_1_".$xml['id']."*.xml") as $filename) {
                    $xml = simplexml_load_file($filename);
                    $this->importPillar($xml, 1, null);
                }
                break;
            }
        }
        
        return 0;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importPillar($xml, $level, $parentId) {
        $channel = new channel();
        $channel->parentId = $parentId;
        $channel->rank = $xml['rank'];
        $channel->source = 'icm';
        $channel->sourceId = $xml['id'];
        $channel->description = (string) $xml->name;
        $channel->title = (string) $xml->caption;
        $channel->siteId = $this->siteId;
        $channel->save();
        $this->wcmBiz->exec("insert into __migration4 values (".(string) $xml['id'].",".$channel->id.", 0)");
        
        echo "Création du pilier: ".$xml['path']."\r\n";
        foreach (glob($this->CATEGORIES_FOLDER."/category_".++$level."_".(string) $xml['path']."-*.xml") as $filename) {
            $xml = simplexml_load_file($filename);
            $this->importPillar($xml, $level, $channel->id);
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importAccounts() {
        $folder = opendir($this->USERS_FOLDER);
        while ($file = readdir($folder)) {
            if ($file != "." && $file != "..") {
                $filename = $file;
                $xml = simplexml_load_file($this->USERS_FOLDER.'/'.$filename);
                $this->importAccount($xml);
            }
        }
        closedir($folder);
        return 0;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importAccount($xml) {
        $account = new account();
        
        $query = $this->wcmBiz->prepare("SELECT wcmId FROM __migration2 WHERE icmUserId = ".(string) $xml['id']);
        $query->execute();
        if ($row = $query->fetch())
            $account->wcmUserId = $row["wcmId"];
            
        if ($xml['doNotExpires'] == 'False')
            $account->expirationDate = substr((string) $xml['expirationDate'], 6, 4)."-".substr((string) $xml['expirationDate'], 3, 2)."-".substr((string) $xml['expirationDate'], 0, 2);
            
        if ((string) $xml->company != "") {
            try {
                @$xmlGroup = simplexml_load_file($this->GROUPS_FOLDER.'/group_'.(string) $xml->company.'.xml');
                @$account->companyName = (string) $xmlGroup->caption;
            }
            catch(Exception $e) {
            }
        }
        
        if ((string) $xml->supervisor != "0") {
            $query = $this->wcmBiz->prepare("SELECT wcmId FROM __migration2 WHERE icmUserId = ".(string) $xml->supervisor);
            $query->execute();
            if ($row = $query->fetch())
                $account->managerId = $row["wcmId"];
                
            if ((string) $account->managerId == "")
                echo $xml['id']."\r\n";
        } else {
            $account->managerId = 1;
        }
        
        $account->save();
        $this->wcmBiz->exec("insert into __migration3 values (".$xml['id'].",".$account->id.")");
        echo "Création du compte: ".$xml->login."\r\n";
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importUsers() {
        $folder = opendir($this->USERS_FOLDER);
        while ($file = readdir($folder)) {
            if ($file != "." && $file != "..") {
                $filename = $file;
                $xml = simplexml_load_file($this->USERS_FOLDER.'/'.$filename);
                $this->importUser($xml);
            }
        }
        closedir($folder);
        return 0;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importUser($xml) {
        $oldId = -1;
        $user = new wcmUser();
        $user->login = (string) $xml->login;
        $user->password = md5((string) $xml->password);
        $user->email = (string) $xml->email;
        $user->isAdministrator = $xml['isAdministrator'];
        switch ($xml->languageId) {
            case 1:
                $user->defaultLanguage = "fr";
                break;
                
            case 2:
                $user->defaultLanguage = "en";
                break;
                
            case 3:
                $user->defaultLanguage = "es";
                break;
        }
        $user->name = (string) $xml->name;
        
        $query = $this->wcmSys->prepare("SELECT id, login, email FROM wcm_user WHERE login = '".(string) $xml->login."'");
        $query->execute();
        if ($row = $query->fetch()) {
            if ($row['email'] != $user->email)
                $user->login = "icm_".$user->login;
            else
                $user->id = $row['id'];
        }
        
        foreach ($xml->members->member as $member) {
            $query = $this->wcmBiz->prepare("SELECT wcmId FROM __migration1 WHERE icmGroupId = ".$member['id']);
            $query->execute();
            if ($row = $query->fetch())
                $user->addToGroup($row["wcmId"]);
        }
        
        if ($user->id == 0) {
            $user->save();
            $this->wcmBiz->exec("insert into __migration2 values (".$xml['id'].",".$user->id.")");
            echo "Création de l'utilisateur: ".$xml->login."\r\n";
        } else {
            echo "L'utilisateur '".$xml->login."' existe déjà.\r\n";
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importGroups() {
        $folder = opendir($this->GROUPS_FOLDER);
        while ($file = readdir($folder)) {
            if ($file != "." && $file != "..") {
                $filename = $file;
                $xml = simplexml_load_file($this->GROUPS_FOLDER.'/'.$filename);
                
                // Si ce n'est pas une société
                switch (strtolower($xml->xml->property->kind)) {
                    case 'company':
                        break;
                        
                    case 'subscription':
                        $this->importSubscription($xml);
                        break;
                        
                    case 'profile':
                    case 'accountkind':
                        $this->importGroup($xml);
                        break;
                }
            }
        }
        closedir($folder);
        return 0;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importSubscription($xml) {
        $newsLetter = new newsletter();
        $newsLetter->code = (string) $xml->name;
        $newsLetter->description = (string) $xml->caption;
        $newsLetter->title = (string) $xml->caption;
        $newsLetter->sender = "AFPRELAXNEWS";
        $newsLetter->from = "noreply@afprelaxnews.com";
        $newsLetter->replyTo = "noreply@afprelaxnews.com";
        $newsLetter->siteId = $this->siteId;
        $newsLetter->save($xml['id']);
        $this->wcmBiz->exec("insert into __migration1 values (".$xml['id'].",".$newsLetter->id.")");
        echo "Création de la newsletter: ".$xml->name."\r\n";
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function importGroup($xml) {
        $wcmId = $this->matchGroup($xml->name);
        
        if ($wcmId == 0) {
            $group = new wcmGroup();
            $group->name = "_BIZ_GROUP_".strtoupper($xml->name);
            $group->save();
            $wcmId = $group->id;
            echo "Création du groupe: ".$group->name."\r\n";
        }
        $this->wcmBiz->exec("insert into __migration1 values (".$xml['id'].",".$wcmId.")");
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function matchGroup($groupName) {
        $wcmId = 0;
        switch (strtoupper($groupName)) {
            case "EDITOR":
                $wcmId = 7;
                break;
                
            case "WEBSERVICES":
                break;
                
            case "TRADUCTEURTEDEMIS":
                break;
                
            case "RELAXAGENDA":
                break;
                
            case "MARKETING":
                break;
                
            case "PIGIST":
                break;
                
            case "VALIDEURTEDEMIS":
                break;
                
            case "ADMINISTRATOR":
                $wcmId = 3;
                break;
                
            case "MANAGEUSER":
                break;
                
            case "ROBOT":
                $wcmId = 14;
                break;
                
            case "CUSTOMER":
                $wcmId = 11;
                break;
        }
        
        return $wcmId;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */

    public function getTotal() {
        return 0;
    }
}
