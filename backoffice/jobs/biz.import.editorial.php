<?php

/*
 * Project:     WCM
 * File:        biz.import.editorial.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/*
** Classe permettant d'importer le contenu éditorial
** d'une source ("afp" ou "biz") à partir de fichiers
** XML au format NewsML
**
** Principe algorithmique :
**
** 1. Scan de répertoires d'import pour une source donnée
**    avec récupération des sous-répertoires à traiter
**
** 2. Pour chaque sous-répertoire, on cherche une rubrique
**    associée par son "code"
**
** 3. On charge le fichier "index.xml" qui contient la
**    liste des articles XML à importer
**
** 4. Si sa date de modification est plus récente que
**    la dernière date d'importation dans la rubrique
**    on importer les flux
**
** 5. On met "offline" tous les articles de la rubrique
**    pour la source donnée
**
** 6. Pour chaque fichier XML, on traite l'article
**    Si l'article n'existe pas déjà dans la base
**    on le créé avec une date d'expiration à J+30,
**    sinon on le met à jour et "online" avec sa position
**
** 7. On traite chaque photo de l'article
**    Pour chaque composant photo, si la photo n'existe pas
**    on la créée sinon on la met à jour avec sa légende
**    => Les fichiers photo et vignette sont renommés et
**       déplacés dans un répertoire spécifique à la source
**
** 8. On met à jour la date d'import de la rubrique avec la
**    dernière date de modification du fichier "index.xml"
**    puis on recalcule l'organisation de la rubrique
**
*/
class importEditorial
{
    private $session;
    private $source;
    private $basePath;
    private $photoPath;
    private $generate;
    
    public  $logger;

    /************************************************************************
    * Constructor
    *
    * @param wcmSession $session    Session wcm
    * @param string     $source     Source d'import ("afp" ou "biz")
    * @param string     $basePath   Répertoire racine pour l'import
    * @param string     $photoPath  Répertoire de stockage des photos
    * @param wcmLogger  $logger     Logger spécifique (ou null pour le créer)
    * @param bool       $generate   True pour générer les éléments importés
    *
    ************************************************************************/
    function __construct(&$session, $source, $basePath, $photoPath, &$logger = null, $generate=true)
    {
        $this->session   =& $session;
        $this->source    =  $source;
        $this->basePath  =  $basePath;
        $this->photoPath =  $photoPath;
        $this->generate  =  $generate;

        // logger spécifique ?
        if ($logger != null)
        {
            $this->logger =& $logger;
        }
        else
        {
            // logger (no verbose, no debug, no flush file, don't handle error)
            $this->logger = new wcmLogger(false, false, null, false);
        }
    }


    /************************************************************************
    * Unzip
    *
    * Méthode qui permet de décompresser un fichier zip $file 
    * dans un répertoire de destination $path
    * et qui retourne un tableau contenant la liste des fichiers extraits
    * Si $effacer_zip est égal à true, on efface le fichier zip d'origine $file
    *
    ************************************************************************/
    public function unzip($file, $path='', $effacer_zip=false)
    {
        $zip = zip_open($file);
        if ($zip)
        {
            while ($zip_entry = zip_read($zip)) //Pour chaque fichier contenu dans le fichier zip
            {
                if (zip_entry_filesize($zip_entry) > 0)
                {
                    $complete_path = $path.dirname(zip_entry_name($zip_entry));
                    $nom_fichier = zip_entry_name($zip_entry);
                    if(!file_exists($complete_path))
                    {
                        $tmp = '';
                        foreach(explode('/',$complete_path) AS $k)
                        {
                            $tmp .= $k.'/';
                            if(!file_exists($tmp))
                            { mkdir($tmp, 0777); }
                        }
                    }
                    if (zip_entry_open($zip, $zip_entry, "r"))
                    {
                        $fd = fopen($path."/".$nom_fichier, 'w');
                        fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
                        fclose($fd);
                        zip_entry_close($zip_entry);
                    }
                }
            }
            zip_close($zip);
            /*On efface éventuellement le fichier zip d'origine*/
            if ($effacer_zip === true)
            unlink($file);
        }
    }


    /*************************************************************************
    * Lancement de la procédure de traitement
    *
    *************************************************************************/
    public function process()
    {

        // Vérifier le répertoire racine
        if (!is_dir($this->basePath))
        {
            $this->logger->logError("Le répertoire" . $this->basePath . " est invalide");
            return false;
        }

        $this->logger->logMessage("Démarrage de l'import pour la source ".$this->source." sur ".$this->basePath);

        if ($this->source == 'biz')
        {
            $this->generate = false;
        
            // On traite tous les zip
            $directory = @opendir($this->basePath);
            while (($file = @readdir($directory)) !== false)
            {
                if (filetype($this->basePath.$file) == 'file' && (substr($file,strpos($file,"."))==".zip"))
                {
                    echo $file."<br>";
                    // Traitement dfes fichiers zip
                    $this->logger->logError("Décompression du fichier : ".$file);
                    $this->unzip($this->basePath.$file,$this->basePath,true);

                    $directory_2 = @opendir($this->basePath);
                    // Au premier niveau on ne traite que les sous-répertoires
                    while (($file_2 = @readdir($directory_2)) !== false)
                    {
                        // Ignorer les dossiers "." et ".."
                        if (filetype($this->basePath.$file_2) == 'dir' && ($file_2[0] != '.'))
                        {
                            $this->processDirectory($this->basePath.$file_2);
                        }
                        //sleep(1);
                    }
                    @closedir($directory_2);
                }
            }
            @closedir($directory);

        }
        else
        {
            $directory_2 = @opendir($this->basePath);
            // Au premier niveau on ne traite que les sous-répertoires
            while (($file_2 = @readdir($directory_2)) !== false)
            {
                // Ignorer les dossiers "." et ".."
                if (filetype($this->basePath.$file_2) == 'dir' && ($file_2[0] != '.'))
                {
                    $this->processDirectory($this->basePath.$file_2);
                }
                //sleep(1);
            }
            @closedir($directory_2);
        }               
        

        
        // Génération des modèles liés
        $generator = new wcmTemplateGenerator($this->logger, false, false);

        if ($this->source == 'afp')
        {
            // Identifiant de la generation 'Fil AFP'
            $AUTOGENERATION_ID = 84;
            // Génération du fil AFP
            $generator->executeGenerationContent($AUTOGENERATION_ID);
        }

        // Identifiant de la generation 'Blocs Actualités'
        $AUTOGENERATION_ID_BLOCS_ACTU = 79;
        // Génération des blocs de Une
        $generator->executeGenerationContent($AUTOGENERATION_ID_BLOCS_ACTU);
        
        $this->logger->logMessage("Fin de l'import");

        /* ENVOI DU LOG PAR MAIL */
        
        if ($this->source == 'biz')
        {
        $to = "tho78tlse@yahoo.fr,tchao@tchao.org";
        $subject = "Trace Import Hermes La Provence DEV";
        $message = $this->logger->display(false,true);

        $headers = 'From: tnivot@eurocortex.fr' . "\r\n" .'Reply-To: tnivot@eurocortex.fr' . "\r\n" .'X-Mailer: PHP/' . phpversion();
        
        mail($to, $subject, $message, $headers);
        }
        
        /* ********************* */
    }
    
    /*************************************************************************
    * Traitement d'un dossier (pour un import d'une seule rubrique)
    *
    *************************************************************************/
    public function processDirectory($path,$idParent=null)
    {
        if (!is_dir($path))
        {
            $this->logger->logError("Le répertoire" . $path . " est invalide");
        }
        else
        {        
            $this->logger->logMessage("Traitement du répertoire " . $path);

            // Recherche d'une rubrique associée à ce dossier
            $rubrique = new rubrique($this->session->_project);
            $code = substr(strrchr($path, '/'), 1);
            $where = ($idParent) ? "idParent=".$idParent : "idParent IS NULL";
            
            
            if ($this->source == "afp" && $code == "fra")
            {
                $rubrique->refresh(3); // rubrique Actualités / France      
            }
            else if ($this->source == "afp" && $code == "spo")
            {
                $rubrique->refresh(5); // rubrique Actualités / Sport           
            }
            else if ($this->source == "afp" && $code == "mon")
            {
                $rubrique->refresh(4); // rubrique Actualités / Monde           
            }
            else if ($this->source == "afp" && $code == "pol")
            {
                $rubrique->refresh(1643); // rubrique Actualités / Politique            
            }
            else if (!$rubrique->refreshByCode($code,$where))
            {
                // Rubrique introuvable
                $this->logger->logWarning("Pas de rubrique ayant pour code ". $code);
                $this->logger->logWarning("Création de la rubrique : ". $code);
                $rubrique->code = $code;
                $rubrique->nom = $code;
                $rubrique->idParent = $idParent;
                $rubrique->checkin();
            }
                  

            // Recherche du fichier "index.xml"
            if (!is_file($path."/index.xml"))
            {
                // Pas de fichier d'index
                $this->logger->logWarning("Pas de fichier d'index pour la rubrique " . $path);
            }
            else
            {
                // Comparaison de la date de modification avec la dernière date de traitement
                $dateMajIndex  = date("Y-m-d H:i:s", filemtime($path."/index.xml"));
                $dernierImport = $rubrique->dateDernierImport;
                if ($dateMajIndex <= $dernierImport)
                {
                    // Pas de modification depuis le dernier import
                    $this->logger->logMessage("L'index de rubrique ".$code." est inchangé depuis le dernier import (" . $dernierImport . " &gt; " . $dateMajIndex . ")");
                }
                else
                {
                    $this->logger->logVerbose("Fichier index " . $dateMajIndex . " - Rubrique " . $dernierImport);
                
                    // Chargement du fichier d'index XML
                    $domXml = @DomDocument::load($path."/index.xml");
                    if (!$domXml)
                    {
                        // Fichier xml invalide !
                        $this->logger->logError("Le fichier " . $path . "/index.xml ne semble pas être un XML valide");
                    }
                    else
                    {

                        // Construction d'un requêteur XPath sur le document XML
                        $xPath = new DOMXPath($domXml);
                        
                        // Construction des articles XML associés
                        $articles = array();

                        // Récupération des id des articles forcés dans la rubrique (dont le placement n'a pas encore expiré)
                        $sql  = "SELECT idDestination FROM biz_rubrique_organisation WHERE idRubrique=".$rubrique->id;
                        $sql .= " AND forcer=1 AND typeDestination='article'";
                        $sql .= " AND dateExpiration > '".date("Y-m-d")."'";
                        $rubrique->_database->setQuery($sql);
                        $idList = $rubrique->_database->loadResultArray();
                        $forceIds = null;
                        foreach($idList as $id)
                        {
                            if ($forceIds != null) $forceIds .= ",";
                            $forceIds .= $id;
                        }
                        
                        // Mise à jour de la date de dernier import avec la date de dernière MAJ du fichier index.XML
                        $sql  = "UPDATE biz_rubrique";
                        $sql .= " SET dateDernierImport='".$dateMajIndex."'";
                        $sql .= " WHERE id=".$rubrique->id;
                        $sql .= ";";

                        // Mise "offline" des articles de la source et de la rubrique
                        // ET qui ne sont pas en placement forcé
                            
                        // ATTENTION MIS EN COMMENTAIRE CAR PB OFFLINE AVEC CERTAINS ARTICLES
                        /*
                        $sql .= "UPDATE biz_article";
                        $sql .= " SET statut='offline' WHERE source='".$this->source."'";
                        $sql .= " AND idRubrique=".$rubrique->id;
                        if ($forceIds) $sql .= " AND id NOT IN (".$forceIds.")";
                        $sql .= ";";
                        */
/*
                        // Mise "offline" des articles de la source et de la rubrique
                        // ET qui ne sont pas en placement forcé
                        // DANS LA TABLE DE RECHERCHE
                        $sql .= "UPDATE biz_recherche";
                        $sql .= " SET statut='offline' WHERE typeObjet='article'";
                        $sql .= "  AND source='".$this->source."'";
                        $sql .= " AND idRubrique=".$rubrique->id;
                        if ($forceIds) $sql .= " AND idObjet NOT IN (".$forceIds.")";
*/
                        $rubrique->_database->setQuery($sql);
                        if (!$rubrique->_database->query_batch())
                        {
                            $this->logger->logError("Erreur lors de la mise à jour de la rubrique : " . $rubrique->_database->_errorMsg . "<br/>" . $rubrique->_database->explain());
                        }
                        else
                        {
                            // Enregistrement (et donc mise "online") des nouveaux articles de la rubrique
                            $position = 1;
                            $nodes = $xPath->query("NewsItem/NewsComponent/NewsComponent/NewsItemRef/@NewsItem");
                            foreach($nodes as $node)
                            {
                                // Traitement de l'article et ajout dans le tableau
                                $idArticle = $this->processArticle($path . "/", $node->nodeValue, $rubrique, $position);
                                if ($idArticle) $position++;
                                sleep(1);
                            }

                            // Calcul de l'organisation de la rubrique
                            $orga = new rubrique_organisation($this->session->_project, $rubrique->id);
                            if (!$orga->compute())
                            {
                                $this->logger->logError("Erreur lors du calcul de l'organisation de rubrique : " . $orga->_lastErrorMsg);
                            }
                            else
                            {
                                $this->logger->logVerbose("Fin du traitement du répertoire " . $path);

                                if ($this->generate)
                                {
                                    $this->logger->logMessage("Génération de la rubrique ".$rubrique->nom);
                                    $rubrique->generate(false, $this->logger);
//                                  sleep(1);
                                }
                            }
                        }
                    }
                }
            }

            // On traite les sous-répertoires
            $directory = @opendir($path);
            while (($file = @readdir($directory)) !== false)
            {
                // Ignorer les dossiers "." et ".."
                if (filetype($path."/".$file) == 'dir' && ($file[0] != '.'))
                {
                    $this->processDirectory($path."/".$file,$rubrique->id);
                }
//              sleep(1);
            }
            @closedir($directory);
            
            
            if ($this->source == 'biz')
            {
                // Si Hermes, on vide le répertoire et on le supprime
                $directory = @opendir($path);
                while (($file = @readdir($directory)) !== false)
                {
                    // Ignorer les dossiers "." et ".."
                    if (filetype($path."/".$file) == 'file')
                    {
                        unlink($path."/".$file);
                    }
    //              sleep(1);
                }
                @closedir($directory);
                rmdir($path);
            }
        }
    }
    
    /*************************************************************************
    * Traitement d'un article
    *
    * @param string     Chemin du fichier XML de l'article
    * @param filename   Nom du fichier XML de l'article
    * @param rubrique   Rubrique de l'article
    * @param int        Position de l'article dans la rubrique
    *
    * @return bool  False on failure (see $this->logger for details)
    *
    *************************************************************************/
    private function processArticle($path, $file, $rubrique, $position)
    {
        $this->logger->logVerbose("Traitement du fichier article " . $file);
        
        // Chargement du fichier XML de l'article
        $domXml = @DomDocument::load($path.$file);
        if (!$domXml)
        {
            // Fichier xml invalide !
            $this->logger->logError("Le fichier article " . $path.$file . " ne semble pas être un XML valide");
            return false;
        }

        // Construction d'un requêteur XPath sur le document XML
        $xPath = new DOMXPath($domXml);
        
        // Création d'un nouvel article
        $article = new article($this->session->_project);

        // Récupération du code unique AFP de l'article (si disponible)
        $uniqueCode = getXPathNodeValue($xPath, null, "NewsItem/Identification/NewsIdentifier/NewsItemId");
        if ($uniqueCode && ($this->source == "afp"))
        {
            // Récupération des propriétés d'un article existant dans la même rubrique...
            // Si le même article doit appartenir à plusieurs rubriques, il faut doublonner !
            $article->refreshBycodeSource($uniqueCode, $rubrique->id);
            $article->codeSource = $uniqueCode;
            if ($article->id == 0)
            {
                $this->logger->logMessage("Création d'article (code AFP : $uniqueCode)");
                $article->statut = "online";
            }
            else
            {
                $this->logger->logMessage("Mise a jour d'article (code AFP : $uniqueCode)");
                $article->statut = "online";
            }
        }

        // Récupération de la date de publication depuis le format ISO
        $isoDate = getXPathNodeValue($xPath, null, "NewsItem/NewsManagement/FirstCreated");
        $datePublication = mktime(substr($isoDate, 9, 2) + substr(date("O"),2,1),substr($isoDate, 11, 2),substr($isoDate, 13, 2),substr($isoDate, 4, 2), substr($isoDate, 6, 2), substr($isoDate, 0, 4));
        
        // Les articles AFP expirent dans 7 jours, les articles Business expirent dans 5 ans
        if ($this->source == "afp")
            $dateExpiration  = mktime(0,0,0, substr($isoDate, 4, 2), substr($isoDate, 6, 2)+7, substr($isoDate, 0, 4));
        else
            $dateExpiration  = mktime(0,0,0, substr($isoDate, 4, 2), substr($isoDate, 6, 2), substr($isoDate, 0, 4)+5);


        // Récupération de la date de modification depuis le format ISO
        $isoDate = getXPathNodeValue($xPath, null, "NewsItem/NewsManagement/ThisRevisionCreated");
        $dateModificationEditoriale = mktime(substr($isoDate, 9, 2) + substr(date("O"),2,1),substr($isoDate, 11, 2),substr($isoDate, 13, 2),substr($isoDate, 4, 2), substr($isoDate, 6, 2), substr($isoDate, 0, 4));

        // Mise à jour des propriétés rédactionnelles
        $article->dateModificationEditorial  = date("Y-m-d H:i:s", $dateModificationEditoriale);
        $article->dateExpiration  = date("Y-m-d H:i:s", $dateExpiration);
        if ($this->source == "afp") // Si AFP, on garde la date de modification éditoriale
            $article->datePublication = date("Y-m-d H:i:s", $dateModificationEditoriale);
        else
            $article->datePublication = date("Y-m-d", $datePublication)." ".date("H:i:s");
        
        $article->idRubrique = $rubrique->id;
        
        $article->source = $this->source;
        if ($this->source == "biz")
        {
            $this->gratuit = 0;
            $article->statut = "offline";
        }

        $article->type = "standard";

        $article->surTitre = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsComponent/NewsLines/SupHeadLine"));
        $article->sousTitre = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsComponent/NewsLines/SubHeadLine"));
        $article->titre = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsComponent/NewsLines/HeadLine"));
        $article->resume = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsComponent/NewsLines/AbstractLine"));
        //$article->encadre = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsComponent/NewsLines/FrameLine"));
        $article->titreOriginal = $article->titre;
        $article->motsCles = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/Identification/NameLabel"));
        $article->signature = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsComponent/NewsLines/CopyrightLine"));
        $article->position = $position;
        
        // Générer un résumé à partir du premier paragraphe si nécessaire
        if (strlen($article->resume) == 0)
        {
            $article->resume = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsComponent/NewsComponent/ContentItem/DataContent/text()"));
            if (strlen($article->resume) > 255) $article->resume = (substr($article->resume, 0, 250) . "...");
        }

        // Pour l'afp, retirer du surtitre "(AFP)"
        if ($this->source == "afp" && (substr($article->surTitre, -5) == "(AFP)"))
            $article->surTitre = trim(substr($article->surTitre, 0, -5));
       
        // Pour l'import Hermès, affectation de l'arbre Hermès et de la page Logique + commune
        if ($this->source == "biz")
        {
            $article->hermes_page = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsManagement/LogicalPage/@FormalName"));
            $article->hermes_arbre = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsManagement/Level/@FormalName"));
            $article->communes     = utf8_decode(getXPathNodeValue($xPath, null, "NewsItem/NewsComponent/DescriptiveMetadata/Property[@FormalName='Location']/Property[@FormalName='City']/@Value"));
        }

        // Préparation du processeur XSL pour le traitement des articles
        $xslPath  = WCM_DIR . "/business/jobs/biz.import.editorial.xsl";
        $domXsl = @DOMDocument::load($xslPath);
        $xslProcessor = new XSLTProcessor;
        $xslProcessor->importStyleSheet($domXsl);
        $article->texte = $xslProcessor->transformToXML($domXml);

        // Enregistrement de l'article
        if (!$article->checkin(null, $this->session->userId))
        {
            $this->logger->logError("Erreur lors de l'enregistrement de l'article ".$article->titre." : ".$article->_lastErrorMsg);
            return false;
        }
        $this->logger->logMessage("Enregistrement de l'article (".$article->id.") ". $article->titre);

        // Traitement des photos de l'article
        $photos = $article->getPhotoContenu();
        $photos->removeAll();
        $position = 1;
        $nodes  = $xPath->query("NewsItem/NewsComponent/NewsComponent[@Duid != '']");
        $this->logger->logVerbose("Traitement des photos de l'article " . $article->codeSource);
        foreach($nodes as $node)
        {
            $idPhoto = $this->processPhoto($path, $node, $datePublication);
            if ($idPhoto)
            {
                $this->logger->logVerbose("Association de la photo " . $idPhoto . " à l'article " . $article->id);
                $photos->insert($position, $idPhoto);
                $position++;
            }
//            sleep(1);
        }

        // Générer l'article ?
        if ($this->generate)
        {
            $this->logger->logMessage("Génération de l'article ". $article->titre);
            $article->generate(false, $this->logger);
//            sleep(1);
        }
        
        return $article;
    }

    /*************************************************************************
    * Traitement d'une photo d'un article
    *
    * @param string         Chemin du fichier photo
    * @param DomElement     Noeud xml correspond au noeud "NewsComponent" photo
    * @param date           Date de publication de l'article (utilisée pour
    *                       la création du chemin de stockage des photos)
    *
    * @return bool  False on failure (see $this->logger for details)
    *
    *************************************************************************/
    private function processPhoto($path, $node, $datePublication)
    {
        // Construction d'un requêteur XPath
        $xPath = new DOMXPath($node->ownerDocument);

        // Récupération des propriétés du NewsComponent actuel
        $duid = getXpathNodeValue($xPath, $node, "@Duid");
                
        // Récupération du fichier original
        $nodeO = getXPathFirstNode($xPath, $node, "NewsComponent[Role/@FormalName='Quicklook']");
        if (!$nodeO)
        {
            $this->logger->logError("Erreur : l'original de la photo " . $duid . " est introuvable");
            return false;
        }
        $fichierO = utf8_decode(getXPathNodeValue($xPath, $nodeO, "ContentItem/@Href"));

        $this->logger->logVerbose("Traitement de la photo " . $fichierO);

        // Récupération du fichier vignette
        $nodeV = getXPathFirstNode($xPath, $node, "NewsComponent[Role/@FormalName='Thumbnail']");
        if (!$nodeV)
        {
            $this->logger->logError("Erreur : la vignette " . $duid . " de la photo " . $fichierO . " est introuvable");
            return false;
        }
        $fichierV = utf8_decode(getXPathNodeValue($xPath, $nodeV, "ContentItem/@Href"));

        // Création d'une nouvelle photo
        $photo = new photo($this->session->_project);

        // Récupération des propriétés d'une photo existante (si possible)
        if (!$photo->refreshBycodeSource($fichierO))
        {
            // Récupération de l'extension (avec le point)
            $extension = substr($fichierO, strrpos($fichierO, "."));

            // Nouvelle photo : on déplace la vignette et l'original, on gère la règle de nommage
            // => AFP  : on prend la partie gauche avant ".thumbnail.default.nnnxnnn.jpg"
            // => BIZN : on prend le nom sans extension
            if ($this->source == "afp")
            {
                $nomCourt = substr($fichierV, 0, strpos($fichierV, ".thumbnail"));

                // Mémoriser le code unique pour la prochaine fois
                $photo->codeSource = $fichierO;
            }
            else
            {
                $nomCourt = basename($fichierO);
                $nomCourt = substr($nomCourt, 0, strrpos($nomCourt, "."));
            }

            // Noms des fichiers finaux (original et vignette)
            // => Placés par source, puis segmentés par année-mois / jour (sur la date de publication ***TODO)
            umask(0);
            $repertoire  = $this->photoPath;
            if (!is_dir($repertoire))
                @mkdir($repertoire, 0777, true);
            $repertoire .= $this->source;
            if (!is_dir($repertoire))
                @mkdir($repertoire, 0777, true);
            $repertoire .= "/" . date("Y-m", $datePublication);
            if (!is_dir($repertoire))
                @mkdir($repertoire, 0777, true);
            $repertoire .= "/" . date("Y-m-d", $datePublication);
            if (!is_dir($repertoire))
                @mkdir($repertoire, 0777, true);
            $repertoire .= "/";

            // Copie des fichiers original et vignette
            $finalO = $repertoire . $nomCourt . $extension;
            $finalV = $repertoire . $nomCourt . '-thb' . $extension;
/*
            if (!$this->copyMagick($path . $fichierO, $finalO))
                $this->logger->logWarning("Impossible de copier la photo ".$path.$fichierO." vers ".$finalO);
            @chmod($finalO, 0666);
            if (!$this->copyMagick($path . $fichierV, $finalV))
                $this->logger->logWarning("Impossible de copier la vignette ".$path.$fichierV." vers ".$finalV);
            @chmod($finalV, 0666);
*/
            if (!@copy($path . $fichierO, $finalO))
                $this->logger->logWarning("Impossible de copier la photo ".$path.$fichierO." vers ".$finalO);
            @chmod($finalO, 0666);
            if (!@copy($path . $fichierV, $finalV))
                $this->logger->logWarning("Impossible de copier la vignette ".$path.$fichierV." vers ".$finalV);
            @chmod($finalV, 0666);


           // Emplacement relatif des photos (url relative)
            $emplacement = 'img/photos/' . $this->source . "/" . date("Y-m", $datePublication) . "/" . date("Y-m-d", $datePublication) . "/";
            $photo->original = $emplacement .  $nomCourt . $extension;
            $photo->vignette = $emplacement .  $nomCourt . '-thb' . $extension;

            // On renseigne les caractéristiques physiques de l'original et de la vignette
            $photo->largeur = getXPathNodeValue($xPath, $nodeO, "ContentItem/Characteristics/Property[@FormalName='Width']/@Value");
            $photo->hauteur = getXPathNodeValue($xPath, $nodeO, "ContentItem/Characteristics/Property[@FormalName='Height']/@Value");
            $photo->largeurVignette = getXPathNodeValue($xPath, $nodeV, "ContentItem/Characteristics/Property[@FormalName='Width']/@Value");
            $photo->hauteurVignette = getXPathNodeValue($xPath, $nodeV, "ContentItem/Characteristics/Property[@FormalName='Height']/@Value");
        }

        // Mise à jour des propriétés rédactionnelles
        $photo->source   = $this->source;
        $photo->titre    = utf8_decode(getXPathNodeValue($xPath, $node, "NewsLines/HeadLine"));
        $photo->legende  = utf8_decode(getXPathNodeValue($xPath, $node, "NewsComponent[Role/@FormalName='Caption']/ContentItem/DataContent"));
        $photo->credits  = utf8_decode(getXPathNodeValue($xPath, $node, "AdministrativeMetadata/Creator/Party/@FormalName"));
        $photo->credits .= " " . utf8_decode(getXPathNodeValue($xPath, $node, "AdministrativeMetadata/Provider/Party/@FormalName"));

        // Enregistrement de la photo et renvoi de son identifiant  
        if (!$photo->checkin(null, $this->session->userId))
        {
            $this->logger->logError("Impossible d'enregistrer la photo " . $nomCourt . " : " . $photo->_lastErrorMsg);
            return false;
        }
        
        return $photo->id;
    }

    /*************************************************************************
    * Copie d'une photo
    *
    * @param $in    string  Fichier source
    * @param $out   string  Fichier destination
    *
    * @return bool  False on failure (see $this->logger for details)
    *
    *************************************************************************/
    private function copyMagick($in, $out)
    {

        //print_r(MagickGetVersion());
        $w = NewMagickWand();
        if (!@MagickReadImages($w, array($in)))
        {
            $this->logger->logWarning("Erreur de Lecture");
            unset($w);
            return false;
        }
        MagickSetImageCompressionQuality($w,90);
        MagickSetImageResolution($w,72,72);
        MagickSetSamplingFactors($w, array(2, 1, 1));
        if (!MagickWriteImage($w,$out))
        {
            $this->logger->logWarning("Erreur d'écriture");
            unset($w);
            return false;
        }
        unset($w);

        return true;
    }
}
?>
