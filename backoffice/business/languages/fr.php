<?php
/**
 * Project:     WCM
 * File:        fr.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
require_once("bizFr.php");
require_once("channels.php");
require_once("iptcTags.php");
/**
 * Tells WCM which language is currently defined in this constant list
  */
DEFINE('_BIZ_WCM_LANGUAGE','fr');

/**
 * Specific (business dependant) language resources
 *
 * french version
 */

// Common
DEFINE('_BIZ_COPYRIGHT','Back-office éditorial - &copy;2006-2008 Nstein');
DEFINE('_BIZ_ACCESS_PRIVATE','Privé');
DEFINE('_BIZ_ACCESS_PROTECTED','Protégé');
DEFINE('_BIZ_ACCESS_PUBLIC','Public');
DEFINE('_BIZ_ACCESS_TITLE','Accès');
DEFINE('_BIZ_ADD','Ajouter');
DEFINE('_BIZ_ADD_PHOTO_MSG','Ajouter une photo');
DEFINE('_BIZ_AJAX_PROCESSING_DONE_MSG','Requête terminée');
DEFINE('_BIZ_AJAX_PROCESSING_MSG','Requête en cours');
DEFINE('_BIZ_ALL','Tous');
DEFINE('_BIZ_ALL_DATES','Toutes les dates');
DEFINE('_BIZ_APPROVE','Valider');
DEFINE('_BIZ_APPROVED','Approuvé');
DEFINE('_BIZ_ASSET','Objet métier');
DEFINE('_BIZ_AUDIO','Audio');
DEFINE('_BIZ_AUTHOR','Auteur');
DEFINE('_BIZ_A_POSTERIORI','Publication automatique');
DEFINE('_BIZ_A_PRIORI','Requiert une approbation');
DEFINE('_BIZ_APPLY','Appliquer');
DEFINE('_BIZ_BEGINDATE','Date début');
DEFINE('_BIZ_BIZCLASS','Classe métier');
DEFINE('_BIZ_BLOG','blogue');
DEFINE('_BIZ_BODY','Corps');
DEFINE('_BIZ_BY','Par');
DEFINE('_BIZ_CANCEL','Annuler');
DEFINE('_BIZ_CAPTION','Légende');
DEFINE('_BIZ_CHANGE','Changer');
DEFINE('_BIZ_CHECKEDOUT','Cet objet a été extrait par : ');
DEFINE('_BIZ_CHECKEDOUT_NOT_REMOVED',' est extrait et n\' pu être purgé.');
DEFINE('_BIZ_CHOOSE_VISUAL','Choisir le visuel');
DEFINE('_BIZ_CHOOSE_LINKS','Choisir les liens');
DEFINE('_BIZ_CLOSED','Fermé (mais en ligne)');
DEFINE('_BIZ_CONTENT','Contenu');
DEFINE('_BIZ_CONTENT_FOR','Contenu personalisé au :');
DEFINE('_BIZ_CREATEDAT','Créé le ');
DEFINE('_BIZ_CREATEDBY','Créé par ');
DEFINE('_BIZ_CREATED_AT','Crée le');
DEFINE('_BIZ_CREATE_NEW_CONTENT','Création de nouveau contenu');
DEFINE('_BIZ_CREATE_THUMBNAIL','Créer une miniature');
DEFINE('_BIZ_CREDITS','Crédits');
DEFINE('_BIZ_CUSTOM','Personaliser');
DEFINE('_BIZ_DASHBOARD_CONFIGURED','Ce tableau de bord peut-être configuré selon vos besoins.');
DEFINE('_BIZ_DATE','date');
DEFINE('_BIZ_DELETE','Effacer');
DEFINE('_BIZ_DELETE_VISUAL','Effacer le visuel');
DEFINE('_BIZ_DESCRIPTION','Description');
DEFINE('_BIZ_DISPLAYDATE','Date d\'affichage');
DEFINE('_BIZ_DISPLAY_MODES','Modes d\'affichage');
DEFINE('_BIZ_THE_DIRECTORY','Le répertoire ');
DEFINE('_BIZ_DOESNT_EXIST','n\'existe pas');
DEFINE('_BIZ_NO_XML_FILES','Il n\'y a pas de fichier xml dans ce répertoire');
DEFINE('_BIZ_FILES_PROCESSED',' fichiers traités');
DEFINE('_BIZ_DOCUMENT','document');
DEFINE('_BIZ_DONE','Terminé');
DEFINE('_BIZ_EDIT','Editer');
DEFINE('_BIZ_EMAIL','Courriel');
DEFINE('_BIZ_EMPTY_LIST','Effacer');
DEFINE('_BIZ_END_DATE','Date fin');
DEFINE('_BIZ_ENGLISH','Anglais');
DEFINE('_BIZ_ERASE','Effacer');
DEFINE('_BIZ_EXECUTION_COMMAND','Exécution de votre commande, patientez...');
DEFINE('_BIZ_EXPIRATIONDATE','Date d\'expiration');
DEFINE('_BIZ_STARTINGDATE','Date d\'ouverture');
DEFINE('_BIZ_EXTERNAL_LINK','Lien externe');
DEFINE('_BIZ_FILE','Fichier');
DEFINE('_BIZ_FILE_MUST_BEGIN','Le nom du fichier doit commencer avec ');
DEFINE('_BIZ_FIND','Chercher');
DEFINE('_BIZ_FLASH','Flash');
DEFINE('_BIZ_FLAT','Plat');
DEFINE('_BIZ_FORMAT','Format');
DEFINE('_BIZ_FOR','pour');
DEFINE('_BIZ_FREE_HTML','HTML libre');
DEFINE('_BIZ_FREE_TEXT','Texte simple');
DEFINE('_BIZ_FRENCH','Français');
DEFINE('_BIZ_FROM','De');
DEFINE('_BIZ_FULLTEXT','Texte entier');
DEFINE('_BIZ_GENERATION_RESULT','Résultat de la génération');
DEFINE('_BIZ_HIERARCHICAL','Hiérarchique');
DEFINE('_BIZ_ID','Identifiant');
DEFINE('_BIZ_IMAGE','Image');
DEFINE('_BIZ_IMPORTATION','Importation');
DEFINE('_BIZ_IMPORT','Importer');
DEFINE('_BIZ_IMPORT_SUCCESS','Processus réussi');
DEFINE('_BIZ_IMPORT_ENDING','Le processus se termine...');
DEFINE('_BIZ_INDEX','Index');
DEFINE('_BIZ_INVALID_CRITERIONS','Critéres invalides');
DEFINE('_BIZ_INVALID_XML','Erreur. Le xml est invalide. Veuillez contacter l\'administrateur.');
DEFINE('_BIZ_INVALID_CLASSNAME','Nom de classe invalide : %s');
DEFINE('_BIZ_INVALID_ID','Identifiant invalide: %s');
DEFINE('_BIZ_INVALID_OBJECT_XML','Représentation XML invalide pour l\'objet %s de type %s');
DEFINE('_BIZ_ITEM_LINKS','Liens de l\'item');
DEFINE('_BIZ_LIST_DISPLAY','Afficher sous forme de liste');
DEFINE('_BIZ_IS_URL','Est-ce une URL ?');
DEFINE('_BIZ_KB','ko');
DEFINE('_BIZ_KEYWORDS','Mots-clés');
DEFINE('_BIZ_KIND_OF_FILE','Type de fichier');
DEFINE('_BIZ_LABEL','Étiquette');
DEFINE('_BIZ_LANGUAGE','Langue');
DEFINE('_BIZ_LOCATION','Emplacement');
DEFINE('_BIZ_METADATA','Métadonnées');
DEFINE('_BIZ_METADESCRIPTION','Méta Description');
DEFINE('_BIZ_MODIFIED','Modifié ');
DEFINE('_BIZ_MODIFIEDAT','Modifié le ');
DEFINE('_BIZ_MODIFIEDBY','Modifié par ');
DEFINE('_BIZ_MOVEDOWN','Descendre');
DEFINE('_BIZ_MOVEUP','Monter');
DEFINE('_BIZ_MUST_CHOOSE_OBJECT','Vous devez choisir un objet');
DEFINE('_BIZ_MY_BIN','Mon panier');
DEFINE('_BIZ_MY_FAVORITES','Mes favoris');
DEFINE('_BIZ_NAME','Nom');
DEFINE('_BIZ_NEW','Nouveau');
DEFINE('_BIZ_NICKNAME','Nom d\'usager');
DEFINE('_BIZ_USERNAME','Surnom');
DEFINE('_BIZ_NOT_CHECKEDOUT','Cet objet n\'est pas extrait.');
DEFINE('_BIZ_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_NO_LINK_ENTERED','Pas de lien entré');
DEFINE('_BIZ_NO_THUMBNAIL','Pas de miniature');
DEFINE('_BIZ_NTAGS','NTags');
DEFINE('_BIZ_NUMBER_SUSPICIONS','Nombre de suspicions');
DEFINE('_BIZ_OBJECTS_NO_RESULT','Aucun objet trouvé');
DEFINE('_BIZ_OBJECTS_SEARCH','Recherche d\'objets');
DEFINE('_BIZ_OBJECTS_SEARCH_MSG_RESULT','Résultat de votre recherche : %s objet%s');
DEFINE('_BIZ_OBJECTS_SEARCH_MSG_RESULT_TINY','Résultat : %s objet%s');
DEFINE('_BIZ_OF','sur');
DEFINE('_BIZ_OFFLINE','Hors ligne');
DEFINE('_BIZ_OK','OK');
DEFINE('_BIZ_ONLINE','En ligne');
DEFINE('_BIZ_ONLINE_CLOSED','En ligne (fermé)');
DEFINE('_BIZ_ONLINE_OPEN','En ligne (ouvert)');
DEFINE('_BIZ_ORIGINAL','Original');
DEFINE('_BIZ_OR','ou');
DEFINE('_BIZ_PARAMETERS','Paramètres');
DEFINE('_BIZ_PARENTY','Parentée');
DEFINE('_BIZ_PASSWORD','Mot de passe');
DEFINE('_BIZ_PERMALINK','Permalien');
DEFINE('_BIZ_PDF','PDF');
DEFINE('_BIZ_PHOTO_LINKS','Liens de la photo');
DEFINE('_BIZ_PHOTO_SELECT_MSG','Résultat de votre recherche : %s photo%s');
DEFINE('_BIZ_PHOTO_SELECT_TITLE_MSG','Choisir une photo');
DEFINE('_BIZ_PHOTO_TOO_BIG','La photo dépasse la taille de fichier permise.');
DEFINE('_BIZ_PODCAST','podcast');
DEFINE('_BIZ_POLL_LINKS','Liens du sondage');
DEFINE('_BIZ_POSITION','Position');
DEFINE('_BIZ_PREPROD_WEBSITE','Site web de pré-production');
DEFINE('_BIZ_PREVIEW','Prévisualisation');
DEFINE('_BIZ_PREVIOUS_PAGE','Page précédente');
DEFINE('_BIZ_NEXT_PAGE','Page suivante');
DEFINE('_BIZ_PREVIEW_WEBSITE','Prévisualisation du site web');
DEFINE('_BIZ_RELATIVE_PATH_FROM','Chemin relatif à partir de ');
DEFINE('_BIZ_PROCESSING','Traitement de');
DEFINE('_BIZ_PROPERTIES','Propriétés');
DEFINE('_BIZ_PUBLICATIONDATE','Date de publication');
DEFINE('_BIZ_PUBLISH','Générer');
DEFINE('_BIZ_PUBLISHED','Publié');
DEFINE('_BIZ_PUBLISHED_IN','Publié dans');
DEFINE('_BIZ_NEVER_PUBLISHED','N/A');
DEFINE('_BIZ_PX','px');
DEFINE('_BIZ_RANK','Position');
DEFINE('_BIZ_REFERREDBY','Référent');
DEFINE('_BIZ_RELEASEDATETIME','Date/heure de publication');
DEFINE('_BIZ_RESET','Réinitialiser');
DEFINE('_BIZ_RESET_SYSTEM_DEFAULT','Réinitialiser');
DEFINE('_BIZ_RESULTS','Résultats');
DEFINE('_BIZ_RESULTS_FIRST','Premiers');
DEFINE('_BIZ_RESULTS_LAST','Derniers');
DEFINE('_BIZ_RESULTS_NEXT','Suivants');
DEFINE('_BIZ_RESULTS_PREVIOUS','Précédents');
DEFINE('_BIZ_ROOT_ELEMENT','(élément racine)');
DEFINE('_BIZ_SAVE','Enregistrer');
DEFINE('_BIZ_REPLACE','Remplacer');
DEFINE('_BIZ_SAVE_SEARCH','Enregistrer la recherche');
DEFINE('_BIZ_SCORE','Résultat');
DEFINE('_BIZ_SEARCHING','Recherche en cours');
DEFINE('_BIZ_SEARCH_EXISTING_CONTENT','Recherche de contenu existant');
DEFINE('_BIZ_SEARCH_GLOBAL','Recherche globale');
DEFINE('_BIZ_SEARCH_GLOBAL_DAM','Import du DAM');
DEFINE('_BIZ_SEARCH_OBJECTS','Recherche d\'objets');
DEFINE('_BIZ_SEARCH_RESULT','Résultat de votre recherche : %s élément%s');
DEFINE('_BIZ_SEE','Voir');
DEFINE('_BIZ_SEE_REFERENTS','Voir les référents');
DEFINE('_BIZ_SELECT','Sélectionner');
DEFINE('_BIZ_SELECT_ARTICLE','sélectionner l\'article');
DEFINE('_BIZ_SELECT_PHOTO_OR_CANCEL','Sélectionnez une photo ou bien cliquez sur "Annuler"');
DEFINE('_BIZ_SELECT_VISUAL','sélectionner le visuel');
DEFINE('_BIZ_SHOWHIDE_DETAILS','Voir/masquer les détails');
DEFINE('_BIZ_SLIDESHOW_LINKS','Liens du diaporama');
DEFINE('_BIZ_SORTED_BY','Trier par');
DEFINE('_BIZ_SOURCE','Source');
DEFINE('_BIZ_STARTING','Démarrage');
DEFINE('_BIZ_START_DATE','Date de départ');
DEFINE('_BIZ_STATE_PROVINCE','État/Province');
DEFINE('_BIZ_STATUS','Statut');
DEFINE('_BIZ_STATISTICS','Statistiques');
DEFINE('_BIZ_WORKFLOW_STATE','État');
DEFINE('_BIZ_SUBJECT','Sujet');
DEFINE('_BIZ_SUBMIT','Soumettre');
DEFINE('_BIZ_SUBTITLE','Sous-titre');
DEFINE('_BIZ_SURTITLE','Attaque/Introduction');
DEFINE('_BIZ_SUSPICIOUS','Suspect');
DEFINE('_BIZ_SUBSCRIPTIONS','Souscriptions');
DEFINE('_BIZ_SUBSCRIPTION','Souscription');
DEFINE('_BIZ_SUBSCRIPTION_ID','Identifiant de la souscription');
DEFINE('_BIZ_TAGS','Étiquettes (tags)');
DEFINE('_BIZ_TAGS_AVAILABLE','Étiquettes disponibles');
DEFINE('_BIZ_TAGS_OBJECT','Étiquettes pour ');
DEFINE('_BIZ_TASKS','Tâches');
DEFINE('_BIZ_TAXONOMIES','Taxonomies');
DEFINE('_BIZ_ADS','Serveur de pub');
DEFINE('_BIZ_TEASER','Chapo');
DEFINE('_BIZ_TEMPLATE','Modèle');
DEFINE('_BIZ_TEXT','Texte');
DEFINE('_BIZ_THUMBNAIL','Miniature');
DEFINE('_BIZ_TITLE','Titre');
DEFINE('_BIZ_HEADLINE','Titre');
DEFINE('_BIZ_CODE','Code');
DEFINE('_BIZ_CSS','Css');
DEFINE('_BIZ_TO','à');
DEFINE('_BIZ_TOOLS','Outils');
DEFINE('_BIZ_TYPE','Type');
DEFINE('_BIZ_UNDETERMINED','Indéterminé');
DEFINE('_BIZ_UNIT','Unité');
DEFINE('_BIZ_UPDATE','Mettre à jour');
DEFINE('_BIZ_URL','Url');
DEFINE('_BIZ_VALIDATE','Valider');
DEFINE('_BIZ_VALUE','Valeur');
DEFINE('_BIZ_VIDEO','Vidéo');
DEFINE('_BIZ_VIEW','Voir');
DEFINE('_BIZ_VISUAL','Visuel');
DEFINE('_BIZ_WARNING','Avertissement');
DEFINE('_BIZ_WEB_LINKS','Liens');
DEFINE('_BIZ_CHECKIN_UNLOCKED','Enregistrement impossible ! L\\\'objet a été déverrouillé par l\\\'administrateur');
DEFINE('_BIZ_SAVE_UNABLE_LOCK','Sauvegarde impossible ! L\\\'objet a été verrouillé par un autre utilisateur');
DEFINE('_BIZ_EXECUTE_PROCESS','Lancer le processus');
DEFINE('_BIZ_SAVED_BIZOBJECT','Bizobject sauvé');
DEFINE('_BIZ_PARTNER_FEEDS','Flux partenaire');

// NServer and semantic metadata
DEFINE('_BIZ_SEMANTIC_DATA','Text-Mining Engine de Nstein');
DEFINE('_BIZ_SEMANTIC_DATA_BIZOBJECTS','Objets métier');
DEFINE('_BIZ_SEMANTIC_DATA_KINDS','Genre de données sémantiques');
DEFINE('_BIZ_SEMANTIC_DATA_WHERE','Clause SQL');
DEFINE('_BIZ_SEMANTIC_DATA_FORCE_UPDATE','Forcer la mise à jour');
DEFINE('_BIZ_UPDATE_SEMANTIC_DATA','Mettre à jour les données sémantiques');
DEFINE('_BIZ_UPDATE_SEMANTIC_DATA_ALERT','ATTENTION: Cette action va rafraichir toutes les données sémantiques de votre contenu. Ceci inclus la catégorisation IPTC, les concepts, les entités (Organisations, personnalités et lieus), la tonalité et les résumés. Ce processus peut être très long dépendamment de la quantité de contenu à rafraichir.');
DEFINE('_BIZ_UPDATE_SEMANTIC_DATA_FAILED','La mise à jour a échoué');
DEFINE('_BIZ_UPDATE_SEMANTIC_DATA_SUCCEEDED','La mise à jour a complété avec succès');
DEFINE('_BIZ_RESET_SEMANTIC_DATA','Remettre à jour les données sémantiques');
DEFINE('_BIZ_NSERVER','TME');
DEFINE('_BIZ_CONCEPT','Concept');
DEFINE('_BIZ_GEOGRAPHICAL_LOCATION','Lieu géographique');
DEFINE('_BIZ_PERSON_NAME','Nom de personne');
DEFINE('_BIZ_ORGANIZATION','Organisation');
DEFINE('_BIZ_CATEGORIES_SUGGESTED','Catégories suggerées');
DEFINE('_BIZ_CATEGORIES_AVAILABLE','Catégories disponibles');
DEFINE('_BIZ_SIMILAR_ITEMS','Items similaires');
DEFINE('_BIZ_SIZE','Taille');
DEFINE('_BIZ_SUBJECTIVITY_NEGATIVE','Négatif');
DEFINE('_BIZ_SUBJECTIVITY_NEUTRAL','Neutre');
DEFINE('_BIZ_SUBJECTIVITY_POSITIVE','Positif');
DEFINE('_BIZ_NFINDER_ON','Organisations');
DEFINE('_BIZ_NCONCEPT_EXTRACTOR','Concepts');
DEFINE('_BIZ_NFINDER_PN','Noms de personnes');
DEFINE('_BIZ_NSUMMARIZER','Résumé');
DEFINE('_BIZ_NFINDER_GL','Lieux géographiques');
DEFINE('_BIZ_NSENTIMENT','Sentiment / Subjectivité');
DEFINE('_BIZ_TONE','Ton');
DEFINE('_BIZ_SUBJECTIVITY','Subjectivité');
DEFINE('_BIZ_SUGGEST','Suggérer');
DEFINE('_BIZ_NO_SUGGESTION','TME n\'a pas trouvé de suggestion appropriée');
DEFINE('_BIZ_SIMILAR_ARTICLES','Articles similaires');
DEFINE('_BIZ_CONCEPTS','Concepts');
DEFINE('_BIZ_RELATED_ARTICLES','Articles reliés');
DEFINE('_BIZ_SUBJECT_MAP','Subject map');

// Rating and hit counter
DEFINE('_BIZ_RATING','Classement');
DEFINE('_BIZ_RATING_COUNT','Nombre de votes');
DEFINE('_BIZ_VOTES','Votes');
DEFINE('_BIZ_RATING_TOTAL','Pointage total');
DEFINE('_BIZ_RATING_VALUE','Valeur sur 5');
DEFINE('_BIZ_HIT_COUNT','Cette page a été vue');

// Reporting
DEFINE('_BIZ_ACCOUNT_USER','Compte utilisateur');
DEFINE('_BIZ_PARENT_ACCOUNT_USER','Compte parent');
DEFINE('_BIZ_CREATION_DATE_ACCOUNT_USER','Date de création');

// Reindexation
DEFINE('_BIZ_REINDEX_CONTENT_TITLE','Réindexation des contenus éditoriaux pour le moteur de recherche');
DEFINE('_BIZ_REINDEX_CONTENT_WARNING_MSG','<span class="warning">Avertissement :</span>  Cette action permet de ré-indexer les informations stockés dans le back-office pour synchroniser le moteur de recherche.<br/>=> N\'utilisez cette option qu\'en cas de nécessité car son traitement peut être long et coûteux.');
DEFINE('_BIZ_KIND_ELEMENT','Objet métier');
DEFINE('_BIZ_INDEXING_RESULT','Résultat de la réindexation');
DEFINE('_BIZ_NUMBER_RECORD_BUSINESS_DB','En base');
DEFINE('_BIZ_NUMBER_RECORD_SEARCH_ENGINE','Indexé');
DEFINE('_BIZ_EXECUTE_REINDEXATION','Exécuter la réindexation');

// Maintenance and purge
DEFINE('_BIZ_PURGE','Purge');
DEFINE('_BIZ_PURGE_SELECT_OBJECTS_TO_PURGE','Sélection des objets à purger');
DEFINE('_BIZ_PURGE_LOCKED_OBJETS','Purger même les objets verrouillés');
DEFINE('_BIZ_PURGE_CONTENT_TITLE','Purge et maintenance');
DEFINE('_BIZ_PURGE_CONTENT_WARNING_MSG','<span class="warning">Avertissement :</span> Cette action va supprimer l\'ensemble des objets expirés ainsi que les utilisateurs web qui ont été bannis');
DEFINE('_BIZ_PURGE_RESULT','Résultat de la purge');
DEFINE('_BIZ_NUMBER_RECORD_TO_PURGE','Expiré');
DEFINE('_BIZ_EXECUTE_PURGE','Exécuter la purge');
DEFINE('_BIZ_PURGE_DONE','Purge terminée');
DEFINE('_BIZ_PURGE_NOTHING_TO_PURGE','Aucun objet à purger!');
DEFINE('_BIZ_PURGE_DONE_BUT_WITH_LOCKED','Purge terminée, toutefois quelques objets étaient verrouillés et n\'ont pas été purgés');

// Locks
DEFINE('_BIZ_SAVE_CONFIRM_ERASE','L\\\'objet a été modifié ! Voulez-vous l\\\'écraser ?');
DEFINE('_BIZ_OBJECT_UNLOCKED','Objet déverrouillé');
DEFINE('_BIZ_OBJECT_LOCKED','Objet verrouillé');
DEFINE('_BIZ_LOCK_MANAGEMENT','Verrouiller gestion');

// Date interval
DEFINE('_BIZ_NEXT_7_DAYS','Les 7 prochains jours');
DEFINE('_BIZ_NEXT_3_DAYS','Les 3 prochains jours');
DEFINE('_BIZ_TOMORROW','Demain');
DEFINE('_BIZ_TODAY','Aujourd\'hui');
DEFINE('_BIZ_YESTERDAY','Hier');
DEFINE('_BIZ_LAST_3_DAYS','Les 3 derniers jours');
DEFINE('_BIZ_LAST_7_DAYS','Les 7 derniers jours');
DEFINE('_BIZ_LAST_MONTH','Le mois dernier');
DEFINE('_BIZ_LAST_3MONTHS','Les 3 derniers mois');
DEFINE('_BIZ_LAST_YEAR','L\'année dernière');

// Error Messages
DEFINE('_BIZ_ERROR','Erreur : ');
DEFINE('_BIZ_ERROR_ON_UNLOCK','Erreur dans le déverrouillage de l\\\'objet : ');
DEFINE('_BIZ_ERROR_ON_LOCK','Erreur dans le verrouillage de l\\\'objet : ');
DEFINE('_BIZ_ERROR_BANNED_DATE_FORMAT','Le format de date pour le champ date de bannissement doit être YYYY-MM-DD');
DEFINE('_BIZ_ERROR_BAN_BEGIN_DATE_FORMAT','La date de bannissement doit être après la date de début.');
DEFINE('_BIZ_ERROR_BEG_END_DATE_FORMAT','La date de début doit être avant la date de fin.');
DEFINE('_BIZ_ERROR_BEGINPERIOD_DATE_FORMAT','Le format de date pour le champ date de début doit être YYYY-MM-DD');
DEFINE('_BIZ_ERROR_BEGIN_DATE_FORMAT','Le format de date pour le champ date de début doit être YYYY-MM-DD');
DEFINE('_BIZ_ERROR_END_DATE_FORMAT','Le format de date pour le champ date de fin doit être YYYY-MM-DD');
DEFINE('_BIZ_ERROR_DISPLAY_DATE_FORMAT','Le format de date pour le champ date d\\\'affichage doit être YYYY-MM-DD');
DEFINE('_BIZ_ERROR_DATE_CHECK_WRONG','La date de fin doit être plus grande que la date de début');
DEFINE('_BIZ_ERROR_START_DATE_CHECK_WRONG','La date de début doit être plus petite que la date de fin');
DEFINE('_BIZ_ERROR_PUBLICATION_DATE_FORMAT','Le format de date pour le champ date de publication doit être YYYY-MM-DD');
DEFINE('_BIZ_ERROR_RELEASE_DATETIME_FORMAT','Le format de date/heure pour le champ date/heure de publication doit être YYYY-MM-DD HH:MM');
DEFINE('_BIZ_ERROR_EXPIRATION_DATE_FORMAT','Le format de date pour le champ date d\\\'expiration doit être YYYY-MM-DD');
DEFINE('_BIZ_ERROR_SUBSCRIPTION_DATES','Les souscriptions doivent avoir une date de début et une date de fin.');
DEFINE('_BIZ_ERROR_SUBSCRIPTION_VALUE','La valeur de la souscription ne peut être vide');
DEFINE('_BIZ_ERROR_POSITION_FORMAT','La position doit être un nombre');
DEFINE('_BIZ_ERROR_EXP_PUB_DATE_FORMAT','La date de publication doit être avant la date d\\\'expiration.');
DEFINE('_BIZ_ERROR_EXPIRATION_DATE_TODAY','La date d\\\'expiration ne devrait pas être avant aujourd\\\'hui');
DEFINE('_BIZ_ERROR_EMAIL_FORMAT','Veuillez corriger l\\\'adresse de courriel.');
DEFINE('_BIZ_ERROR_EMAIL_ANSWER_FORMAT','Veuillez corriger l\\\'adresse de retour.');
DEFINE('_BIZ_ERROR_SAVE','Enregistrement impossible');
DEFINE('_BIZ_ERROR_HEADLINE_NOT_COMPLETED','Entête non renseigné');
DEFINE('_BIZ_ERROR_TITLE_NOT_COMPLETED','Titre non renseigné');
DEFINE('_BIZ_ERROR_USERNAME_LENGTH','Le surnom est trop long. La limite est de 50 charactères.');
DEFINE('_BIZ_ERROR_URL_NOT_COMPLETED','URL non renseigné');
DEFINE('_BIZ_ERROR_NB_SUSPICIONS','Le nombre de suspicions doit être un nombre de moins de dix chiffres');
DEFINE('_BIZ_ERROR_FIRSTNAME_NOT_COMPLETED','Prénom non renseigné');
DEFINE('_BIZ_ERROR_NAME_NOT_COMPLETED','Nom non renseigné');
DEFINE('_BIZ_ERROR_LASTNAME_NOT_COMPLETED','Nom de famille non renseigné');
DEFINE('_BIZ_ERROR_USERNAME_NOT_COMPLETED','Surnom non renseigné');
DEFINE('_BIZ_ERROR_REFERENT_NOT_COMPLETED','Vous devez choisir un référent pour ce commentaire');
DEFINE('_BIZ_ERROR_REFERENT_CLASS_NOT_COMPLETED','Vous devez choisir une classe de référent pour ce commentaire');
DEFINE('_BIZ_ERROR_NICKNAME_NOT_COMPLETED','Le nom d\'usager doit être spécifié');
DEFINE('_BIZ_ERROR_CLASSNAME_NOT_COMPLETED','Nom de la classe non renseigné');
DEFINE('_BIZ_ERROR_TEXT_NOT_EMPTY','Le champs texte doit être rempli.');
DEFINE('_BIZ_ERROR_SCORE_NUM','Le score doit être un nombre entre 0 et 100.');
DEFINE('_BIZ_ERROR_PHOTO_FORMAT','Erreur dans le format de la photo');
DEFINE('_BIZ_ERROR_UPLOAD_SIZE','Uploaded file exceeded maximum allowed filesize: %s');
DEFINE('_BIZ_ERROR_UPLOAD_PARTIAL','File was only partially uploaded.');
DEFINE('_BIZ_ERROR_UPLOAD_NO_FILE','No file was actually uploaded');
DEFINE('_BIZ_ERROR_UPLOAD_NO_TMP_DIR','No temporary directory was found to write uploaded file to.');
DEFINE('_BIZ_ERROR_UPLOAD_CANT_WRITE','Could not write uploaded file to disk');
DEFINE('_BIZ_ERROR_UPLOAD_EXTENSION','File upload stopped by extension');
DEFINE('_BIZ_ERROR_SITE_IS_MANDATORY','Site non renseigné');
DEFINE('_BIZ_ERROR_CHANNEL_IS_MANDATORY','Section non renseignée');

// Errors associated to fields validation
DEFINE('_BIZ_ERROR_CODE_IS_MANDATORY','Code is mandatory');
DEFINE('_BIZ_ERROR_CODE_TOO_LONG','Code is too long');
DEFINE('_BIZ_ERROR_TITLE_IS_MANDATORY','Le titre est obligatoire');
DEFINE('_BIZ_ERROR_TITLE_TOO_LONG','Le titre est trop long');
DEFINE('_BIZ_ERROR_CHAPTER_TITLE_TOO_LONG','Le titre de la page est trop long');
DEFINE('_BIZ_ERROR_SUBTITLE_TOO_LONG','Le sur-titre est trop long');
DEFINE('_BIZ_ERROR_SUPTITLE_TOO_LONG','Le sous-titre est trop long');
DEFINE('_BIZ_ERROR_KEYWORDS_TOO_LONG','Les mots-cles sont trop longs');
DEFINE('_BIZ_ERROR_CREDITS_TOO_LONG','Les crédits sont trop longs');
DEFINE('_BIZ_ERROR_AUTHOR_TOO_LONG','L\'auteur est trop long');
DEFINE('_BIZ_ERROR_NICKNAME_TOO_LONG','Le surnom est trop long');
DEFINE('_BIZ_ERROR_USERNAME_IS_MANDATORY','Le nom est obligatoire');
DEFINE('_BIZ_ERROR_USERNAME_TOO_LONG','Le nom est trop long');
DEFINE('_BIZ_ERROR_FIRSTNAME_TOO_LONG','Le prénom est trop long');
DEFINE('_BIZ_ERROR_LASTNAME_TOO_LONG','Le nom de famille est trop long');
DEFINE('_BIZ_ERROR_EMAIL_TOO_LONG','L\'addresse email est trop longue');
DEFINE('_BIZ_ERROR_CITY_TOO_LONG','Le nom de la ville est trop long');
DEFINE('_BIZ_ERROR_COUNTRY_TOO_LONG','Le nom du pays est trop long');
DEFINE('_BIZ_ERROR_POSTAL_CODE_TOO_LONG','Le code postal est trop long');
DEFINE('_BIZ_ERROR_PHONE_TOO_LONG','Le numéro de téléphone est too long');
DEFINE('_BIZ_ERROR_STATE_TOO_LONG','Le nom de l\'état est trop long');
DEFINE('_BIZ_ERROR_SOURCE_TOO_LONG','Le nom de la source est trop long');
DEFINE('_BIZ_ERROR_SOURCE_ID_TOO_LONG','L\'identifiant de la source est trop long');
DEFINE('_BIZ_ERROR_SOURCE_VERSION_TOO_LONG','Le numéro de version de la source est trop long');
DEFINE('_BIZ_ERROR_SENDER_TOO_LONG','Le nom de l\'expéditeur est trop long');
DEFINE('_BIZ_ERROR_FROM_TOO_LONG','L\'adresse de l\'expéditeur est trop longue');
DEFINE('_BIZ_ERROR_REPLY_TO_TOO_LONG','L\'adresse de réponse est trop longue');
DEFINE('_BIZ_ERROR_PUBLICATIONNAME_IS_MANDATORY','Le nom de la publication est obligatoire');
DEFINE('_BIZ_ERROR_ISSUENUMBER_NUM','Le numéro de parution doit être égal ou supérieur à 1');
DEFINE('_BIZ_ERROR_PERMALINKS_TOO_LONG','Le permaliens est trop long');

// Question Messages
DEFINE('_BIZ_QUESTION_CONFIRM_DELETE','Etes-vous sur de vouloir supprimer cet élément ?');

// BizObject
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_BIZOBJECT','Choisissez un objet pour ce commentaire');
DEFINE('_BIZ_CONTRIBUTION_SELECT_BIZOBJECT_LIST','Sélectionnez un objet dans la liste ci-dessous');
DEFINE('_BIZ_CHOOSE_NEXT_TRANSITION','Choisissez la transition suivante');
DEFINE('_BIZ_SWITCH_TO_OBJECT_SITE','Pour éditer cet objet, vous devez modifier le site sélectionné pour celui qui contient l\'objet');

// Channel
DEFINE('_BIZ_CHANNEL','Rubrique');
DEFINE('_BIZ_CHANNELS','Rubriques');
DEFINE('_BIZ_CHANNEL_CHOOSE_CONTENT','Choisissez le contenu pour cette rubrique');
DEFINE('_BIZ_CHANNEL_CONTENT','Contenu pour cette rubrique');
DEFINE('_BIZ_CHANNEL_SEARCH','Recherche de rubrique');
DEFINE('_BIZ_NEW_QUERY','Nouvelle requête');
DEFINE('_BIZ_CHOOSE_QUERY','Choisir la requête');
DEFINE('_BIZ_CHANNEL_INDENT','&nbsp;&nbsp;&nbsp;');
DEFINE('_BIZ_CHANNEL_INDENT_SYMBOL',' :: ');
DEFINE('_BIZ_CHANNEL_TREE','Menu des rubriques');
DEFINE('_BIZ_SEE_ORGANIZATION_CHANNEL','Voir l\'organisation de la rubrique');
DEFINE('_BIZ_CHANNEL_LOAD_NEAREST_CONTENT','Plus récent');
DEFINE('_BIZ_CHANNEL_MANAGE_DATE','Il n\'y a pas de contenu de spécifié pour la date voulue. Le contenu pour la date suivante a été chargé : ');
DEFINE('_BIZ_CHANNEL_EMPTY_CONTENT','Le contenu pour cette date a été vidé');
DEFINE('_BIZ_MANAGE_CHANNEL','Gestion rubrique');
DEFINE('_BIZ_DESIGN_CHANNEL','Conception');
DEFINE('_BIZ_CHANNEL_MANAGE_MSG','Gestion de la rubrique ');
DEFINE('_BIZ_CHANNEL_MANAGE_CONTENT_OF','Gestion du contenu du ');
DEFINE('_BIZ_PREVIEW_CHANNEL_MSG','Prévisualisation de la rubrique');
DEFINE('_BIZ_CONTENT_CHANNEL','Contenu');
DEFINE('_BIZ_CHANNEL_ORGANISATION','Choisir le type d\'organisation');
DEFINE('_BIZ_CHANNEL_REQUEST_CHANNEL','Requête');
DEFINE('_BIZ_CHANNEL_REQUEST','Requêtes de la rubrique');
DEFINE('_BIZ_FORCED_CHANNEL','Contenu forcé');
DEFINE('_BIZ_CHANNEL_FORCED','Contenu forcé de la rubrique:');
DEFINE('_ADD_FORCED_BIZOBJECT','Ajouter un contenu');
DEFINE('_BIZ_DATE_MANAGEMENT','Gestion par date');
DEFINE('_BIZ_FORCED_MANAGEMENT','Gestion par élément forcé');
DEFINE('_BIZ_CUSTOM_MANAGEMENT','Gestion personnalisée');
DEFINE('_BIZ_MIXED_MANAGEMENT','Gestion mixte');
DEFINE('_BIZ_ORDER_BY','Ordonner par');
DEFINE('_BIZ_LIMIT','Limiter');
DEFINE('_BIZ_BUILD_QUERY','Construire la requête');

// Pages : website/section
DEFINE('_BIZ_SECTION_PROMO_PANEL','Panneau promotionel');
DEFINE('_BIZ_SECTIONS_HIERARCHY','Hiérarchie des Rubriques');
DEFINE('_BIZ_SECTION_CONTENT_RULES','Gestion du contenu automatisé');
DEFINE('_BIZ_SECTION_CONTENT_RULES_MANAGEMENT','Règles');
DEFINE('_BIZ_SECTION_CONTENT_RULES_OPTIONS','Options');
DEFINE('_BIZ_SECTION_CONTENT_FORCED','Contenu forcé');

// Article
DEFINE('_BIZ_ARTICLE','Article');
DEFINE('_BIZ_ARTICLES','Articles');
DEFINE('_BIZ_ARTICLE_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_ARTICLE_SEARCH','Recherche d\'article');
DEFINE('_BIZ_ARTICLE_SEARCH_MSG_RESULT','Résultat de votre recherche : %s article%s');
DEFINE('_BIZ_ARTICLE_LINKS_MSG','Choisir les liens');
DEFINE('_BIZ_CREATE_CONTRIBUTION_ARTICLE','Créer un commentaire pour cet article');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_ARTICLE','Choisissez un article pour ce commentaire');
DEFINE('_BIZ_CONTRIBUTION_SELECT_ARTICLE_LIST','Sélectionnez un article dans la liste ci-dessous');
DEFINE('_BIZ_SECTION','Rubrique');
DEFINE('_BIZ_SECTIONS','Rubriques');
DEFINE('_BIZ_ADD_CHAPTERS','Ajouter des pages');
DEFINE('_BIZ_CHAPTER','Page');
DEFINE('_BIZ_CHAPTERS','Pages');
DEFINE('_BIZ_NEW_CHAPTER','Nouvelle page');
DEFINE('_BIZ_ABSTRACT','Résumé');
DEFINE('_BIZ_PAGE_TITLE','Titre de page');
DEFINE('_BIZ_PAGE_CONTENT','Contenu de la page');
DEFINE('_ADD_PAGE_BEFORE','Ajouter une page avant');
DEFINE('_ADD_PAGE_AFTER','Ajouter une page après');
DEFINE('_ADD_PAGE','Ajouter une page');
DEFINE('_BIZ_NEW_INSERTS','Nouvel insert');
DEFINE('_ADD_INSERTS','Ajouter une insert');
DEFINE('_DELETE_INSERTS','Supprimer cet insert');
DEFINE('_BIZ_INSERTS_KIND','Type');
DEFINE('_BIZ_INSERTS_CONTENT','Texte');
DEFINE('_BIZ_ARTICLE_INSERTS','Inserts');
DEFINE('_BIZ_PAGE_SUBTITLE','Sous-titre');
DEFINE('_ADD_INSERTS_AFTER','Ajouter nouvel insert');
DEFINE('_BIZ_INSERTS_TITLE','Le chiffre (LChiffre + LRepère) ou le nom (LExpert) ou le titre (Autre)');
DEFINE('_BIZ_INSERTS_SOURCE','La source (LChiffre + LRepère) ou la fonction (LExpert)');
DEFINE('_BIZ_CHAPTER_AUTHOR','Intervenant');
DEFINE('_BIZ_CHAPTER_COMPANY','Société');

// News Item
DEFINE('_BIZ_NEWSITEM','Nouvelle');
DEFINE('_BIZ_NEWSITEMS','Nouvelles');
DEFINE('_BIZ_NEWSITEM_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_NEWSITEM_SEARCH','Recherche de nouvelle');
DEFINE('_BIZ_NEWSITEM_SEARCH_MSG_RESULT','Résultat de votre recherche : %s nouvelle%s');
DEFINE('_BIZ_NEWSITEM_LINKS_MSG','Choisir les liens');
DEFINE('_BIZ_CREATE_CONTRIBUTION_NEWSITEM','Créer un commentaire pour cette nouvelle');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_NEWSITEM','Choisissez une nouvelle pour ce commentaire');
DEFINE('_BIZ_CONTRIBUTION_SELECT_NEWSITEM_LIST','Sélectionnez une nouvelle dans la liste ci-dessous');

// Forum
DEFINE('_BIZ_FORUM','Forum');
DEFINE('_BIZ_FORUMS','Forums');
DEFINE('_BIZ_FORUM_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_FORUM_SEARCH','Recherche de forum');
DEFINE('_BIZ_FORUM_SEARCH_MSG_RESULT','Résultat de votre recherche : %s forum%s');
DEFINE('_BIZ_FORUM_LINKS_MSG','Choisir les liens');
DEFINE('_BIZ_KIND','Type');
DEFINE('_BIZ_POST','Message');
DEFINE('_BIZ_MODERATION','Modération');
DEFINE('_BIZ_RESTRICTED_TOPICS','Sujets restreints');
DEFINE('_BIZ_CREATE_CONTRIBUTION_FORUM','Créer un commentaire pour ce forum');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_FORUM','Choisissez un forum pour ce commentaire');
DEFINE('_BIZ_CONTRIBUTION_SELECT_FORUM_LIST','Sélectionnez un forum dans la liste ci-dessous');

// Item
DEFINE('_BIZ_ITEM','Item');
DEFINE('_BIZ_ITEMS','Items');
DEFINE('_BIZ_ITEM_ALLOWED_FILENAME_EXTENSIONS','Extensions de nom de fichier autorisées');
DEFINE('_BIZ_ITEM_TYPE','Type d\'item');
DEFINE('_BIZ_ITEM_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_ITEM_INVALID_PATH','Chemin invalide');
DEFINE('_BIZ_ITEM_INVALID_SOURCE','Source invalide');
DEFINE('_BIZ_ITEM_BROWSER_DISPLAY','Affichage en mosaïque');
DEFINE('_BIZ_ITEM_LIST_DISPLAY','Affichage en liste');
DEFINE('_BIZ_ITEM_SEARCH','Recherche d\'item');
DEFINE('_BIZ_ITEM_SEARCH_MSG_RESULT','Résultat de votre recherche : %s item%s');
DEFINE('_BIZ_ITEM_NAME','Nom de l\'item à créer');
DEFINE('_BIZ_CHOOSE_ITEM','Choisir l\'item');
DEFINE('_BIZ_CHOOSE_PHOTO','Choisir la photo');
DEFINE('_BIZ_ENTER_FILE_OR_CANCEL','Entrer un nom de fichier ou cliquez sur "Annuler"');
DEFINE('_BIZ_DELETE_ITEM','Effacer l\'item');
DEFINE('_BIZ_SELECT_ITEM','Sélectionner l\'item');
DEFINE('_BIZ_SELECT_ITEM_OR_CANCEL','Sélectionnez un item ou bien cliquez sur "Annuler"');
DEFINE('_BIZ_FILE_EXISTS','Le fichier spécifié existe déjà.');
DEFINE('_BIZ_FOLDER_EXISTS','Le dossier spécifié existe déjà.');
DEFINE('_BIZ_IMPOSSIBLE_CREATE_FOLDER','Impossible de créer le dossier');
DEFINE('_BIZ_LOAD_ERROR','Erreur de chargement');
DEFINE('_BIZ_PARENT_FOLDER','Dossier parent');

// Poll
DEFINE('_BIZ_DISPLAY_BEFORE_TOTALVOTE','(Affichage avant le vote total)');
DEFINE('_BIZ_KIND_OF_POLL','Type de sondage');
DEFINE('_BIZ_MINIMAL_VOTE','Nb de vote minimaux');
DEFINE('_BIZ_POLL','Sondage');
DEFINE('_BIZ_POLLS','Sondages');
DEFINE('_BIZ_POLL_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_POLL_SEARCH','Recherche de sondage');
DEFINE('_BIZ_POLL_CHOICE','Choix du sondage');
DEFINE('_BIZ_POLL_CHOICES','Choix du sondage');
DEFINE('_BIZ_POLL_QCU','Question à choix unique');
DEFINE('_BIZ_POLL_QCM','Question à choix multiples');
DEFINE('_BIZ_POLL_SEARCH_MSG_RESULT','Résultat de votre recherche : %s sondage%s');
DEFINE('_BIZ_VOTE','Votez');
DEFINE('_BIZ_POLL_QUESTION','Question du sondage');
DEFINE('_BIZ_POLL_QUESTION_TEXT','Question');
DEFINE('_BIZ_SINGLE_CHOICE','Choix unique');
DEFINE('_BIZ_MULTIPLE_CHOICE','Choix multiples');

// Contribution
DEFINE('_BIZ_CONTRIBUTION','Commentaire');
DEFINE('_BIZ_CONTRIBUTIONS','Commentaires');
DEFINE('_BIZ_CONTRIBUTION_REFERENT','Référent');
DEFINE('_BIZ_CONTRIBUTION_REFERENT_CLASS','Classe du référent');
DEFINE('_BIZ_CONTRIBUTION_EMAIL','Etre alerté par courriel lors d\'une réponse à ce commentaire');
DEFINE('_BIZ_CONTRIBUTION_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_CONTRIBUTION_SEARCH','Recherche de commentaires');
DEFINE('_BIZ_CONTRIBUTION_SEARCH_MSG_RESULT','Résultat de votre recherche : %s commentaire%s');
DEFINE('_BIZ_PARENT_CONTRIBUTION','Commentaire parent');
DEFINE('_BIZ_CREATE_CONTRIBUTION_CONTRIBUTION','Créer un commentaire en réponse à ce commentaire');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_CONTRIBUTION','Choisissez un commentaire parent pour ce commentaire');
DEFINE('_BIZ_CONTRIBUTION_SELECT_CONTRIBUTION_LIST','Sélectionnez un commentaire parent dans la liste ci-dessous');
DEFINE('_BIZ_CONTRIBUTION_STATE','État des commentaires');
DEFINE('_BIZ_CONTRIBUTION_NONE','Commentaires interdites');
DEFINE('_BIZ_CONTRIBUTION_CLOSED','Fermé');
DEFINE('_BIZ_CONTRIBUTION_OPEN','Ouvert');
DEFINE('_BIZ_CONTRIBUTION_PLEASE_ENTER_YOUR','S.v.p. entrer votre');
DEFINE('_BIZ_NO_CONTRIBUTION','Aucun commentaire');
DEFINE('_BIZ_FOLLOW_UPS','Ajouts');
DEFINE('_BIZ_CONTRIBUTION_NO_REFERENT','Pas de référent');
DEFINE('_BIZ_CONTRIBUTION_ORPHAN','Ce commentaire n\'a pas de parent');

// Shared media
DEFINE('_BIZ_SHARED_MEDIA','Médias');

// Photo
DEFINE('_BIZ_CHANGE_PHOTO','Changer la photo');
DEFINE('_BIZ_PHOTO','Photo');
DEFINE('_BIZ_PHOTOS','Photos');
DEFINE('_BIZ_PHOTOS_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_PHOTOS_SEARCH','Recherche de photo');
DEFINE('_BIZ_PHOTOS_SEARCH_MSG_RESULT','Résultat de votre recherche : %s photo%s');
DEFINE('_BIZ_SITE_TITLE_ALREADY_USED','Ce titre est déjà utilisé');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_PHOTO','Choisissez une photo parente pour ce commentaire');
DEFINE('_BIZ_CONTRIBUTION_SELECT_PHOTO_LIST','Sélectionnez une photo parente dans la liste ci-dessous');
DEFINE('_BIZ_PHOTOS_SEE_ORIGINAL','Voir la photo originale');
DEFINE('_BIZ_MANAGE_CROPPING','Gestion des formats et des cadrages');
DEFINE('_BIZ_DIMENSIONS','Dimensions');
DEFINE('_BIZ_SELECT_RATIO','Choisir un ration');
DEFINE('_BIZ_RATIO_THUMB_SQUARE','Vignette carrée');
DEFINE('_BIZ_RATIO_THUMB_HORIZONTAL','Vignette horizontale');

// Vidéo
DEFINE('_BIZ_CHANGE_VIDEO','Changer la vidéo');
DEFINE('_BIZ_VIDEO','Vidéo');
DEFINE('_BIZ_VIDEOS','Vidéos');
DEFINE('_BIZ_VIDEOS_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_VIDEOS_SEARCH','Recherche de vidéo');
DEFINE('_BIZ_VIDEOS_SEARCH_MSG_RESULT','Résultat de votre recherche : %s vidéo%s');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_VIDEO','Choisissez une vidéo parente pour ce commentaire');
DEFINE('_BIZ_CONTRIBUTION_SELECT_VIDEO_LIST','Sélectionnez une vidéo parente dans la liste ci-dessous');
DEFINE('_BIZ_VIDEO_URL','Lien');
DEFINE('_BIZ_VIDEO_EMBED','Balise EMBED');

// Site
DEFINE('_BIZ_SITE','Site web');
DEFINE('_BIZ_SITES','Sites');
DEFINE('_BIZ_SITE_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_SITE_CANT_DELETE_CURRENT','Il n\'est pas permis d\'effacer le site couramment utilisé');
DEFINE('_BIZ_SITE_SEARCH','Recherche de site');
DEFINE('_BIZ_SITE_SEARCH_MSG_RESULT','Résultat de votre recherche : %s site%s');
DEFINE('_BIZ_ADD_NEW_LINK','Ajouter');

// Menu and recipe
DEFINE('_BIZ_ADDITIONAL_INFO','Information supplémentaire');
DEFINE('_BIZ_ADDITIONAL_INFO_TITLE','Titre (information supplémentaire');
DEFINE('_BIZ_ADD_INGREDIENT','Ajouter un ingrédient');
DEFINE('_BIZ_ADD_NUTRITIONAL','Ajouter une information nutritionnelle');
DEFINE('_BIZ_ADD_RECIPE_MSG','Ajouter une recette');
DEFINE('_BIZ_COOKTIME','Temps de cuisson');
DEFINE('_BIZ_FULL_TEXT_INGREDIENTS','Ingrédients (texte)');
DEFINE('_BIZ_GENERATE_FULL_TEXT','Générer le texte complet');
DEFINE('_BIZ_GENERATE_LIST','Générer la liste d\'ingrédients');
DEFINE('_BIZ_INGREDIENTS','Ingrédients');
DEFINE('_BIZ_MENU','Menu');
DEFINE('_BIZ_MENU_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_MENU_RECIPES_MSG','Les recettes de ce menu');
DEFINE('_BIZ_MENU_SEARCH','Chercher un menu');
DEFINE('_BIZ_MENU_SEARCH_MSG_RESULT','Résultat de votre recherche : %s menu%s');
DEFINE('_BIZ_NUTRITIONALS','Info nutritionnelle');
DEFINE('_BIZ_PORTION','Portion');
DEFINE('_BIZ_PREPMETHOD','Méthode de préparation');
DEFINE('_BIZ_PREPTIME','Temps de préparation');
DEFINE('_BIZ_QUANTITY','Quantité');
DEFINE('_BIZ_RECIPE','Recette');
DEFINE('_BIZ_RECIPE_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_RECIPE_SEARCH','Recherche de recettes');
DEFINE('_BIZ_RECIPE_SEARCH_MSG_RESULT','Résultat de votre recherche : %s recette%s');

//Upload
DEFINE('_BIZ_UPLOAD_ITEM','Télécharger un item');
DEFINE('_BIZ_UPLOAD_PHOTO','Télécharger une photo');
DEFINE('_BIZ_UPLOAD_VIDEO','Télécharger une vidéo');
DEFINE('_BIZ_UPLOAD_FOLDER_SOURCE','Répertoire source');
DEFINE('_BIZ_UPLOAD_FOLDER_DESTINATION','Répertoire destination');
DEFINE('_BIZ_UPLOAD_XML_FILENAME_PREFIX','Prefixe des fichiers XML');
DEFINE('_BIZ_UPLOAD_NEW_FOLDER','Nouveau répertoire');
DEFINE('_BIZ_CREATE_FOLDER','Créer le répertoire');
DEFINE('_BIZ_FILE_TO_UPLOAD','Fichier à Télécharger');
DEFINE('_BIZ_DESTINATION','Destination');
DEFINE('_BIZ_IMAGE_FILE','Fichier image');
DEFINE('_BIZ_IMAGE_NAME','Nom de l\'image à créer');
DEFINE('_BIZ_UPLOAD_THEPHOTO','Télécharger la photo...');
DEFINE('_BIZ_PHOTO_EXISTS','La photo existe déjà.');
DEFINE('_BIZ_VIDEO_FILE','Fichier vidéo');
DEFINE('_BIZ_VIDEO_NAME','Nom de la vidéo à créer');
DEFINE('_BIZ_UPLOAD_THEVIDEO','Télécharger la vidéo...');
DEFINE('_BIZ_VIDEO_EXISTS','La vidéo existe déjà.');

// Links
DEFINE('_BIZ_LINKS','Liens');
DEFINE('_BIZ_LINKS_ADD','Ajout d\'un nouveau lien');
DEFINE('_BIZ_LINKS_CHOOSE_ELEMENT','Choisissez un élément pour le lien');
DEFINE('_BIZ_LINKS_MODIF','Modification du contenu du lien');
DEFINE('_BIZ_LINKS_SELECT_ELEMENT_LIST','Sélectionnez élément dans la liste ci-dessous');
DEFINE('_BIZ_LINKING_TO_ITSELF','Vous ne pouvez pas lier un object à lui même.');

// Slideshow
DEFINE('_BIZ_SLIDESHOW','Diaporama');
DEFINE('_BIZ_SLIDESHOWS','Diaporamas');
DEFINE('_BIZ_ADD_PHOTOS_MSG','Ajouter des photos');
DEFINE('_BIZ_SLIDESHOW_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_SLIDESHOW_SEARCH','Chercher un diaporama');
DEFINE('_BIZ_SLIDESHOW_SEARCH_MSG_RESULT','Résultat de votre recherche : %s diaporama%s');
DEFINE('_BIZ_SLIDESHOW_PHOTOS_MSG','Ajouter des photos au diaporama');

// Newsletter
DEFINE('_BIZ_NEWSLETTER','Infolettre');
DEFINE('_BIZ_NEWSLETTERS','Infolettres');
DEFINE('_BIZ_NEWSLETTER_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_NEWSLETTER_SEARCH','Chercher une infolettre');
DEFINE('_BIZ_NEWSLETTER_SEARCH_MSG_RESULT','Résultat de votre recherche : %s infolettre%s');
DEFINE('_BIZ_NEWSLETTER_MSG_DEFAULT_PROPERTIES','Configuration par défaut');
DEFINE('_BIZ_SENDER','Expéditeur');
DEFINE('_BIZ_REPLY_TO','Adresse de retour');
DEFINE('_BIZ_TITLE_MSG','Titre du message');
DEFINE('_BIZ_NEWSLETTER_MSG_TEMPLATE_USED','Modèles de l\'infolettre');
DEFINE('_BIZ_HTML_TEMPLATE','HTML');
DEFINE('_BIZ_TEXT_TEMPLATE','Texte en clair');
DEFINE('_BIZ_NEWSLETTER_SUBSCRIBERS','Abonnés');
DEFINE('_BIZ_NEWSLETTER_EXISTING_SUBSCRIBERS','Internautes abonnés à cette infolettre');
DEFINE('_BIZ_NEWSLETTER_ADD_SUBSCRIBER','Ajouter un abonné');
DEFINE('_BIZ_NEWSLETTER_FORCED_CONTENT','Contenu forcé');
DEFINE('_BIZ_NEWSLETTER_CONFIGURATION','Configuration');
DEFINE('_BIZ_NEWLETTERS_TEMPLATES','Gabarits de courriels');

// Webuser
DEFINE('_BIZ_WEBUSER','Internaute');
DEFINE('_BIZ_WEBUSER_ADD','Ajouter un internaute');
DEFINE('_BIZ_WEBUSERS','Internautes');
DEFINE('_BIZ_WEBUSER_ID','Id de l\'interanaute');
DEFINE('_BIZ_WEBUSER_CONTRIBUTIONS','Commentaires de l\'internaute');
DEFINE('_BIZ_WEBUSER_SEARCH','Chercher un internaute');
DEFINE('_BIZ_WEBUSER_CLEAR','Effacer l\'internaute');
DEFINE('_BIZ_WEBUSER_SEARCH_MSG_RESULT','Résultat de votre recherche : %s internaute%s');
DEFINE('_BIZ_WEBUSER_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_FIRSTNAME','Prénom');
DEFINE('_BIZ_LASTNAME','Nom');
DEFINE('_BIZ_ADDRESS','Adresse');
DEFINE('_BIZ_ADDRESS1','Adresse (1)');
DEFINE('_BIZ_ADDRESS2','Adresse (2)');
DEFINE('_BIZ_ADDRESS3','Adresse (3)');
DEFINE('_BIZ_PHONE_NUMBER','Téléphone');
DEFINE('_BIZ_POSTALCODE','Code postal');
DEFINE('_BIZ_CITY','Ville');
DEFINE('_BIZ_COUNTRY','Pays');
DEFINE('_BIZ_MANAGE_USER','Voir cet usager');
DEFINE('_BIZ_NBERRORS','Nombre d\'erreurs');
DEFINE('_BIZ_NBPERIODS','Nombre de périodes');
DEFINE('_BIZ_BANNEDDATE','Date de bannissement');
DEFINE('_BIZ_BEGINPERIODDATE','Date de début');
DEFINE('_BIZ_OLDEMAILS','Anciens courriels');
DEFINE('_BIZ_DEMOGRAPHICS','Informations géographiques');
DEFINE('_BIZ_BANNED','Bani');
DEFINE('_BIZ_WAITING','En attente');
DEFINE('_BIZ_VALID','Valide');
DEFINE('_BIZ_LAST_LOGIN','Derniére ouverture de session');

// Collection
DEFINE('_BIZ_COLLECTION','Collection');
DEFINE('_BIZ_COLLECTIONS','Collections');
DEFINE('_BIZ_COLLECTION_SEARCH','Chercher une collection');
DEFINE('_BIZ_COLLECTION_SEARCH_MSG_RESULT','Résultat de votre recherche : %s collection%s');
DEFINE('_BIZ_COLLECTION_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_ADD_COLLECTION_ELEMENT_MSG','Ajouter un lien');
DEFINE('_BIZ_COLLECTION_LINKS_MSG','Choisir les liens');
DEFINE('_BIZ_NEW_COLLECTION','Nouvelle collection');
DEFINE('_BIZ_CHOOSE_OR_CREATE_COLLECTION','Choisir ou créer collection');

// Questionnaire
DEFINE('_BIZ_QUESTIONNAIRE','Questionnaire');
DEFINE('_BIZ_QUESTIONNAIRE_SEARCH','Chercher un questionnaire');
DEFINE('_BIZ_QUESTIONNAIRE_SEARCH_MSG_RESULT','Résultat de votre recherche : %s questionnaire%s');
DEFINE('_BIZ_QUESTIONNAIRE_NO_RESULT','Aucun résultat retourné');
DEFINE('_BIZ_ADD_QUESTIONNAIRE_ELEMENT_MSG','Ajouter un lien');
DEFINE('_BIZ_QUESTIONNAIRE_ADD_QUESTION','Ajouter des questions');
DEFINE('_BIZ_QUESTIONNAIRE_LINKS_MSG','Choisir les liens');
DEFINE('_BIZ_QUESTIONNAIRE_TYPE','Type de questionnaire');
DEFINE('_BIZ_QUESTIONNAIRE_CHOICES','Questions');
DEFINE('_BIZ_QUIZ','Quiz');
DEFINE('_BIZ_SUMMARY','Résumé');
DEFINE('_BIZ_QUESTIONS','Questions');
DEFINE('_BIZ_QUESTION','Question');
DEFINE('_BIZ_ANSWER','Réponse');
DEFINE('_BIZ_ANSWER_MODE','Mode de réponse');
DEFINE('_BIZ_ANSWER_WEIGHT','Poid de la réponse');
DEFINE('_BIZ_QUESTION_MULTIPLE','Réponses multiples');
DEFINE('_BIZ_QUESTION_SINGLE','Réponse unique');
DEFINE('_BIZ_QUESTION_FREEFORM','Réponse libre');
DEFINE('_BIZ_SHOWHIDE_ANSWER','Voir/cacher la liste de questions');

// NServer Msg
DEFINE('_BIZ_MSG_RESET','Initialisation terminée');
DEFINE('_BIZ_MSG_INSERT','Insertion terminée');
DEFINE('_BIZ_MSG_NEW','Créer un nouvel item');
DEFINE('_BIZ_MSG_UPDATE','Mise à jour terminée');
DEFINE('_BIZ_MSG_EDIT','Éditer un item');
DEFINE('_BIZ_MSG_LOADING_WAIT','Chargement en cours...');
DEFINE('_BIZ_MSG_INSERT_TEXT','Entrer une valeur ici');
DEFINE('_BIZ_CONFIRM_RESET_MSG','Êtes-vous sur de vouloir initialiser la liste ?');
DEFINE('_BIZ_CONFIRM_RESET_ALL_MSG','Êtes-vous sur de vouloir réinitialiser toutes les listes ?');

// Subscriptions
DEFINE('_BIZ_SUBSCRIPTION_ADD','Ajouter la souscription');
DEFINE('_BIZ_SUBSCRIPTION_START','Début de la souscription');
DEFINE('_BIZ_SUBSCRIPTION_END','Fin de la souscription');
DEFINE('_BIZ_SUBSCRIPTION_TYPE','Type de souscription');
DEFINE('_BIZ_SUBSCRIPTION_TYPE_BIZ','Lier la souscription à un objet métier');
DEFINE('_BIZ_SUBSCRIPTION_TYPE_CUSTOM','Lier la souscription à une valeur personnalisée');
DEFINE('_BIZ_SUBSCRIPTION_BUTTON_ADD','Ajouter cette souscription');
DEFINE('_BIZ_SUBSCRIPTION_VALUE','Valeur de la souscription');
DEFINE('_BIZ_CUSTOMIZED_SUBSCRIPTION','Souscription personnalisée');

// Search
DEFINE('_BIZ_RECORDS_FOUND','trouvés');
DEFINE('_BIZ_RECORDS_PAGE','documents par page');
DEFINE('_BIZ_SECONDS','secondes');
DEFINE('_BIZ_SHOWING','Résultats');
DEFINE('_BIZ_SIMPLESEARCH_TITLE','Chercher un objet pour cette souscription');
DEFINE('_BIZ_FIXED_QUERY','Requête prédéfinie');
DEFINE('_BIZ_MANAGEMENT_BY_DATE','Gestion par date');
DEFINE('_BIZ_SPECIFY_QUERY','Précisez la requête');

/**
 * Specific (generated web site dependant) language resources
 *
 * French version
 */

/* Home */
DEFINE('_WEB_SITE_FULLTITLE','N<span class="green">C</span>M<span class="gray">DEMO</span>');
DEFINE('_WEB_SITENAME','Centre de démo - Nstein');
DEFINE('_WEB_SITETITLE','Démo WCM');

/* Blog */
DEFINE('_WEB_BLOG_TITLE','Blogue de démo WCM - Nstein');

/* Forums */

/* Gallery */
DEFINE('_WEB_GALLERY_TITLE','Galerie de démo WCM - Nstein');

/* general */
DEFINE('_WEB_ALL_FIELDS','Tout les champs doivent être remplis');
DEFINE('_WEB_AUTHOR','Auteur');
DEFINE('_WEB_BACK_SITE','Retour au site');
DEFINE('_WEB_BIGGER','Plus grand');
DEFINE('_WEB_BY','Par');
DEFINE('_WEB_CATEGORIES','Catégories');
DEFINE('_WEB_CHANNEL','Rubrique');
DEFINE('_WEB_CODE_IS_INCORRECT','Le code est incorrect');
DEFINE('_WEB_COMPANY','Nstein Technologies');
DEFINE('_WEB_CONCEPTS','Concepts');
DEFINE('_WEB_COPYRIGHT','Tous droits réservés');
DEFINE('_WEB_DATE','Date');
DEFINE('_WEB_EMAIL','Courriel');
DEFINE('_WEB_ENTER_CODE','Entrez le code');
DEFINE('_WEB_DISCUSS','Commentez');
DEFINE('_WEB_FIND','Chercher');
DEFINE('_WEB_FILE_UNDER','Classer sous');
DEFINE('_WEB_FULLTEXT','Texte entier');
DEFINE('_WEB_IN','sous');
DEFINE('_WEB_KEYWORDS','Mots clés');
DEFINE('_WEB_LATEST','Plus récent');
DEFINE('_WEB_LOCATIONS','Lieux');
DEFINE('_WEB_MENU','Menu');
DEFINE('_WEB_METAS','Métas');
DEFINE('_WEB_MORE_ARTICLES_FROM','Plus d\'articles de ');
DEFINE('_WEB_MOREINFO_ABOUT','Plus d\'information?');
DEFINE('_WEB_MOST_DISCUSSED','Les plus commentés');
DEFINE('_WEB_MOST_POPULAR','Les plus populaires');
DEFINE('_WEB_NAME','Nom');
DEFINE('_WEB_NICKNAME','Pseudonyme');
DEFINE('_WEB_ON','le');
DEFINE('_WEB_ORGANIZATIONS','Organisations');
DEFINE('_WEB_PAGE','Page');
DEFINE('_WEB_PEOPLE','Personnes');
DEFINE('_WEB_PRINT','Imprimer');
DEFINE('_WEB_POPULAR_KEYWORDS','Mots clés populaires');
DEFINE('_WEB_POSTED_AT','Ajouté à');
DEFINE('_WEB_POWERED_BY','Propulsé par le WCM de Nstein');
DEFINE('_WEB_PUBLICATION_DATE','Date de publication');
DEFINE('_WEB_RELATED_CONTENT','Contenu relié');
DEFINE('_WEB_RELATED','Relié');
DEFINE('_WEB_REPLY','Répondre');
DEFINE('_WEB_SAID',' ');
DEFINE('_WEB_SEE','Voir');
DEFINE('_WEB_SEE_ALSO','Voir aussi');
DEFINE('_WEB_SEND','Envoyer');
DEFINE('_WEB_SITE_FEATURES','Rubriques du site');
DEFINE('_WEB_SITE_VALID','Valide');
DEFINE('_WEB_SMALLER','Plus petit');
DEFINE('_WEB_SUBMIT','Soumettre');
DEFINE('_WEB_TITLE','Titre');

// Modules : editorial/article (tabs)
DEFINE('_BIZ_OVERVIEW','Aperçu');
DEFINE('_BIZ_DESIGN','Conception');
DEFINE('_BIZ_COMMENTS','Commentaires');
DEFINE('_BIZ_NEW_ARTICLE','Nouvel article');

// Modules : shared/versioning
DEFINE('_BIZ_VERSIONS','Versions');
DEFINE('_BIZ_VERSION','Version');
DEFINE('_BIZ_REVISION','Révision');
DEFINE('_BIZ_VERSION_HISTORY','Historique');
DEFINE('_BIZ_ADD_NEW_VERSION','Sauvegarder et créer une nouvelle version');
DEFINE('_BIZ_VERSION_COMMENT','Commentaire');
DEFINE('_BIZ_VERSION_RESTORE','Récupérer cette version');
DEFINE('_BIZ_VERSION_ROLLBACK','Revenir à cette version');
DEFINE('_BIZ_NO_VERSION_STORED','aucune version disponible');
DEFINE('_BIZ_VERSION_ADDED','Une nouvelle version a été créée');
DEFINE('_BIZ_VERSION_ADDED_FAILED',':La création de la nouvelle version à échouée');
DEFINE('_BIZ_VERSION_RESTORED','L\'ancienne version a été restaurée');
DEFINE('_BIZ_VERSION_RESTORE_FAILED','La récupération de la version à échouée');
DEFINE('_BIZ_VERSION_ROLLEDBACK','L\'objet a été ramené à sa version précédente');
DEFINE('_BIZ_VERSION_ROLLBACK_FAILED','Le retour à l\'ancienne version à échoué');
DEFINE('_BIZ_VERSION_CREATED_ON_SAVE','Version créée automatiquent à la sauvegarde');

// Modules : editorial/article/Properties
DEFINE('_BIZ_OTHER_SOURCE','Autre source');
DEFINE('_BIZ_OTHER_SOURCE_NAME','Nom de la source');

// Modules : editorial/article/Referencing
DEFINE('_BIZ_REFERENCING','Référencement');
DEFINE('_BIZ_PERMALINKS','Permaliens');
DEFINE('_BIZ_DEFAULT_PERMALINK','Défaut (Adresse conviviale)');
DEFINE('_BIZ_ADDITIONAL_PERMALINKS','Permaliens additionels');
DEFINE('_BIZ_OUTBOUND_LINKS','Contenu similaire');
DEFINE('_BIZ_OUTBOUND_LINKS_INTERNAL','Internes');
DEFINE('_BIZ_OUTBOUND_LINKS_EXTERNAL','Externes');
DEFINE('_BIZ_INBOUND_LINKS','Documents référencant ce document');

// Modules : editorial/article/categorization
DEFINE('_BIZ_CATEGORIZATION','Catégorisation');
DEFINE('_BIZ_CATEGORIZATION_IPTC','IPTC');
DEFINE('_BIZ_CATEGORIZATION_TAGS','"Tags"');

// Module : editorial/article/footprint

DEFINE('_BIZ_TME','Analyse sémantique');
DEFINE('_BIZ_TME_ENTITIES','Entitées');
DEFINE('_BIZ_TME_ENTITITES_GL','Lieux gégraphiques');
DEFINE('_BIZ_TME_ENTITITES_ON','Organisations');
DEFINE('_BIZ_TME_ENTITITES_PN','Personnes');
DEFINE('_BIZ_TME_CONCEPTS','Concepts');
DEFINE('_BIZ_TME_CONCEPTS_COMPLEX','Complexes');
DEFINE('_BIZ_TME_CONCEPTS_SIMPLE','Simple');
DEFINE('_BIZ_TME_SENTIMENT','Analyse du ton et de la subjectivité');
DEFINE('_BIZ_TME_SENTIMENT_TONE','Ton');
DEFINE('_BIZ_TME_SENTIMENT_TONE_POSITIVE','Positif');
DEFINE('_BIZ_TME_SENTIMENT_TONE_NEUTRAL','Neutre');
DEFINE('_BIZ_TME_SENTIMENT_TONE_NEGATIVE','Négatif');
DEFINE('_BIZ_TME_SENTIMENT_SUBJECTIVITY','Subjectivité');
DEFINE('_BIZ_TME_SENTIMENT_SUBJECTIVITY_FACT','Fait');
DEFINE('_BIZ_TME_SENTIMENT_SUBJECTIVITY_OPINION','Opinion');
DEFINE('_BIZ_AD_SERVER','Publicité');
DEFINE('_BIZ_TME_PROCESSING_FAILED','L\'éxecution de l\'engin Text Mining a échoué');
DEFINE('_BIZ_UPDATING_SEMANTIC_DATA_FOR','Mise à jour des données sémantiques pour ');
DEFINE('_BIZ_TOTAL_COMPUT_TIME','Temps de calcul total : ');

// Module : editorial/shared/commenting
DEFINE('_BIZ_CONTRIBUTION_COMMENTS','Commentaires émis sur cet élément');
DEFINE('_BIZ_LAST_COMMENT_TIME','Dernier commentaire fait le');

/* Semantic data */
DEFINE('_WEB_CATEGORIES_AVAILABLE','Catégories existantes');
DEFINE('_WEB_CONCEPT','Concept');
DEFINE('_WEB_GEOGRAPHICAL_LOCATION','Lieux géographiques');
DEFINE('_WEB_NSERVER','TME');
DEFINE('_WEB_PERSON_NAME','Nom de personne');
DEFINE('_WEB_PLACES','Lieux');
DEFINE('_WEB_ORGANIZATION','Organisation');
DEFINE('_WEB_SEMANTIC_CLOUD','Sémantique');
DEFINE('_WEB_SIMILAR_ITEMS','Eléments similaires');

/* Comments & Contributions */
DEFINE('_WEB_ADD','Ajoutez un');
DEFINE('_WEB_BEFIRST_COMMENT','Soyez le premier à commenter ');
DEFINE('_WEB_COMMENT','commentaire');
DEFINE('_WEB_COMMENTS','Commentaires ');
DEFINE('_WEB_COMMENT_NOW','Commentez!');
DEFINE('_WEB_COMMENTS_ON','Commentaires sur ');
DEFINE('_WEB_COMMENT_ON','Commentaire sur ');
DEFINE('_WEB_CONTRIBUTION_PLEASE_ENTER_YOUR','Veuillez entre votre ');
DEFINE('_WEB_LAST_COMMENTS','Derniers commentaires');
DEFINE('_WEB_START_NEW_TOPIC','Débuter un nouveau sujet');
DEFINE('_WEB_POST_COMMENT','Ajoutez votre commentaire *');

/* rating, most viewed & most popular */
DEFINE('_WEB_MOST_POP_PHOTOS','Photos les plus populaires');
DEFINE('_WEB_MOSTVIEWED_PHOTOS','Photos les plus vues');
DEFINE('_WEB_MOSTVIEWED_STORIES','Articles les plus vus');
DEFINE('_WEB_TOPSTORIES','Articles populaires');
DEFINE('_WEB_RATE','Évaluer ');
DEFINE('_WEB_RATE_ARTICLE',' Évaluez cet article');
DEFINE('_WEB_RATE_GALLERY',' Évaluez cette galerie');
DEFINE('_WEB_RATE_PHOTO',' Évaluez cette photo');
DEFINE('_WEB_RATE_RECIPE',' Évaluez cette recette');
DEFINE('_WEB_RATE_THIS',' Évaluez!');
DEFINE('_WEB_RATING','Évaluation');
DEFINE('_WEB_READMORE','En lire plus');
DEFINE('_WEB_READMORE_ON','En lire plus sur ');
DEFINE('_WEB_VIEWED','Vu');

/* search */
DEFINE('_WEB_NSTEIN_SEARCH','Recherche Nsteindaily');
DEFINE('_WEB_REFINE_SEARCH','Raffiner');
DEFINE('_BIZ_REFINE_BUTTON','Raffiner');
DEFINE('_WEB_SEARCH','Recherche');

/* object specific */
/* Articles */

/* Forums */
DEFINE('_WEB_FORUMS','Forums');
DEFINE('_WEB_FORUMS_SUBTITLE','Forums');
DEFINE('_WEB_FORUMS_LAST_FOUR_THREADS','Derniers sujets');
DEFINE('_WEB_FORUMS_LAST_TEN_THREADS','10 derniers sujets');
DEFINE('_WEB_FORUMS_FULL_THREAD','Voir le sujet complet');
DEFINE('_WEB_FORUMS_LAST_POSTS','Derniers messages');
DEFINE('_WEB_FORUMS_ADD_POST','Ajouter un message');
DEFINE('_WEB_FORUMS_SUBMIT_POST','Soumettre le message');
DEFINE('_WEB_FORUMS_POST_NOW','Ajoutez');

/* News article */
DEFINE('_WEB_VIEWCOMPLETE_NEWSITEM','Voir la nouvelle complete');

/* Slideshows */
DEFINE('_WEB_IN_PICTURES','En images');
DEFINE('_WEB_VIEW_GALLERY','Voir la galerie');

/* Photos */
DEFINE('_BIZ_PICTURE','Image');
DEFINE('_WEB_SEE_PHOTO','Voir la photo');
DEFINE('_BIZ_PHOTO_ALLOWED_FILENAME_EXTENSIONS','Extensions autorisées pour les photos :');

/* polls */
DEFINE('_WEB_LATEST_POLLS','Derniers sondages');
DEFINE('_WEB_LETUS_KNOW','Dites-le nous!');
DEFINE('_WEB_THANKS_VOTING','Merci d\'avoir voté');
DEFINE('_WEB_VOTE','Votez');

/* Publicity & Advertisement */
DEFINE('_WEB_SAMPLE_PORTFOLIO','Port folio');

/* Related */
DEFINE('_WEB_RELATED_ARTICLE','Relié');
DEFINE('_WEB_RELATED_CHANNEL','Rubrique reliée');
DEFINE('_WEB_RELATED_COLLECTION','Collection reliée');
DEFINE('_WEB_RELATED_CONTRIBUTION','Commentaire relié');
DEFINE('_WEB_RELATED_FORUM','Forum relié');
DEFINE('_WEB_RELATED_ITEM','Item relié');
DEFINE('_WEB_RELATED_NEWSITEM','Brève reliée');
DEFINE('_WEB_RELATED_NEWSLETTER','Infolettre reliée');
DEFINE('_WEB_RELATED_PHOTO','Photo reliée');
DEFINE('_WEB_RELATED_POLL','Sondage relié');
DEFINE('_WEB_RELATED_SITE','Site relié');
DEFINE('_WEB_RELATED_SLIDESHOW','Diaporama relié');
DEFINE('_WEB_SIMILAR_RESULTS','Résultats similaires');

/* Request */
DEFINE('_BIZ_CHANNEL_REQUEST_NAME','Nom');
DEFINE('_BIZ_CHANNEL_REQUEST_CLASS','Table');
DEFINE('_BIZ_CHANNEL_REQUEST_WHERE','Où');
DEFINE('_BIZ_CHANNEL_REQUEST_ORDERBY','Trier par');
DEFINE('_BIZ_OPERATOR','');
DEFINE('_BIZ_FIELDS','Champs');
DEFINE('_BIZ_COMPARE','Operateur');
DEFINE('_BIZ_ORDER','Ordre');
DEFINE('_BIZ_NEW_CHANNEL_REQUEST_NAME','Veuillez indiquer un nom de requête différent pour dupliquer');
DEFINE('_BIZ_CHECK_REQUEST','Vérifier la validité de la requête');
DEFINE('_BIZ_CHANNEL_REQUEST_VALID','La requete est valide');
DEFINE('_BIZ_CHANNEL_REQUEST_NOT_VALID','La requete n\'est pas valide');

/* Import */
DEFINE('_BIZ_DEFAULT_PARAMETERS','Laisser les paramètres vides si vous souhaitez utiliser leur valeur par defaut');
DEFINE('_BIZ_IMPORT_TYPE','Type d\'import');
DEFINE('_BIZ_IMPORT_PARAMETERS','Paramètres de l\'import');
DEFINE('_ROOT_FOLDER','Répertoire à importer');
DEFINE('_XSL_FOLDER','Répertoire contenant les XSL de transformation');
DEFINE('_PHOTO_FOLDER','Répertoire contenant les photos à importer');
DEFINE('_PHOTO_URL','URL d\'accès aux photos après leur import');
DEFINE('_BIZ_IMPORT_AFP','Importer des fichiers NewsML' );
DEFINE('_BIZ_IMPORT_NITF','Importer des fichiers NITF');
DEFINE('_BIZ_IMPORT_PHOTOS','Importer des photos');
DEFINE('_BIZ_IMPORT_BIZOBJECT','Importer des fichiers en format XML natif WCM');
DEFINE('_BIZ_IMPORT_PRINT','Importer des fichiers print');
DEFINE('_BIZ_BEGIN_IMPORT','Début de l\'import avec %s comme répertoire initial');
DEFINE('_BIZ_END_IMPORT','Fin de l\'import avec %s comme répertoire initial');
DEFINE('_BIZ_READ_FOLDER','Lecture du répertoire :');
DEFINE('_BIZ_PROCESS_FOLDER','Traitement du repertoire :');
DEFINE('_BIZ_PROCESS_FILE','Traitement du fichier :');
DEFINE('_BIZ_INVALID_FILE','Le fichier %s%s est invalide');
DEFINE('_BIZ_XSL_NOT_FOUND','La xsl de transformation n\'existe pas');
DEFINE('_BIZ_XML_INVALID','Le fichier %s ne semble pas être un XML valide');
DEFINE('_BIZ_FILE_IMPORT_INCORRECT','Le fichier "%s" n\'a pas été importé correctement ::');
DEFINE('_BIZ_USE_XSL','Utilisation de la xsl : ');
DEFINE('_BIZ_ROOT_FOLDER','Repertoire principal');
DEFINE('_BIZ_TRANSFORM_IMPORT_ARTICLES_PHOTOS','Transformer et importer les articles et photos');
DEFINE('_BIZ_AFP_IMPORT_RESULT','Résultat de l\'import des articles AFP');
DEFINE('_BIZ_NITF_IMPORT_RESULT','Résultat de l\'import des articles NITF');
DEFINE('_BIZ_PHOTO_IMPORT_RESULT','Résultat de l\'import des photos');
DEFINE('_BIZ_BIZOBJECT_IMPORT_RESULT','Résultat de l\'import bizObjects');
DEFINE('_BIZ_PRINT_IMPORT_RESULT','Résultat de l\'import Print');
DEFINE('_BIZ_IMPORT_FROM_FILES','Import de fichiers');
DEFINE('_BIZ_IMPORT_SOURCE_FORMAT','Format source');
DEFINE('_BIZ_IMPORT_FILE_LOCATION','Emplacement des fichiers');
DEFINE('_BIZ_IMPORT_SOURCE_FOLDER','Répertoire source');
DEFINE('_BIZ_IMPORT_TRANSFORMATION_SETTINGS','Paramètres de transformation');
DEFINE('_BIZ_IMPORT_XSL_TEMPLATE_LOCATION','Répertoire contenant les XSL');
DEFINE('_BIZ_IMPORT_MEDIA_SETTINGS','Paramètres des medias');
DEFINE('_BIZ_IMPORT_EMBEDDED_PHOTOS','Photos incorporées');
DEFINE('_BIZ_IMPORT_DESTINATION_FOLDER','Répertoire destination');
DEFINE('_BIZ_IMPORT_START','Démarrer l\'import');
DEFINE('_BIZ_IMPORT_SETTINGS','Paramètres de l\'import');
DEFINE('_BIZ_IMPORT_JOB_STATUS','Statut de l\'import');
DEFINE('_BIZ_IMPORT_DETAILED','Pour plus d\'informations consulter :');
DEFINE('_BIZ_RESTART','Relancer');
DEFINE('_BIZ_IMPORT_CANCELED','Import annulé par l\'utilisateur');
DEFINE('_BIZ_IMPORT_NO_DESTINATION_DIRECTORY','Le dossier de destination n\'existe pas et n\'a pu être créé');
DEFINE('_BIZ_IMPORT_NO_DESTINATION_DIRECTORY_CREATED','Le dossier de destination n\'existe pas et a été créé');
DEFINE('_BIZ_IMPORT_JPEG','Importer des photos (jpeg) avec les méta-données XMP');
DEFINE('_BIZ_IMPORT_DAM','Importer avec le webservice DAM');
DEFINE('_BIZ_IMPORT_NEWSML','Importer de fichiers NewsML');
DEFINE('_BIZ_IMPORT_NITF','Importer de fichiers NITF');
DEFINE('_BIZ_IMPORT_NSTEIN','Importer de fichiers au format XML Nstein');
DEFINE('_BIZ_IMPORT_CRITERIA','Critères d\'importation');
DEFINE('_BIZ_IMPORT_FROM_WHEN','Importer en fonction de dates');
DEFINE('_BIZ_IMPORT_FROM_WHERE','Importer de quel endroit');
DEFINE('_BIZ_DAM_WEB_SERVICE_URL','URL du webservice DAM');
DEFINE('_BIZ_IMPORT_DAME_REPOSITORY','Répertoire du webservice DAM');
DEFINE('_BIZ_USERID','Identifiant de l\'usager');
DEFINE('_BIZ_MISCELLANEOUS','Divers');
DEFINE('_BIZ_XSL_FOLDER','Dossier des xsl');
DEFINE('_BIZ_CLASSES','Classes');
DEFINE('_BIZ_LAST_THREE_DAYS','Les trois derniers jours');
DEFINE('_BIZ_LAST_SEVEN_DAYS','Les sept derniers jours');
DEFINE('_BIZ_LAST_MONTH','Le dernier mois');
DEFINE('_BIZ_LAST_IMPORT','Dernier import');
DEFINE('_BIZ_IMPORT_DAM_NEW_PROCESSOR','Using additional processor class');
DEFINE('_BIZ_IMPORT_DAM_PROCESSOR_FAILED','Special logic for class %s failed');
DEFINE('_BIZ_IMPORT_DAM_NO_MAPPING','Could not find mapping information for DAM Class');
DEFINE('_BIZ_IMPORT_DAM_INVALID_TOKEN','Invalid security token');

/* Import Article */
DEFINE('_BIZ_CREATE_ARTICLE','Création d\'article (code : %s)');
DEFINE('_BIZ_UPDATE_ARTICLE','Mise a jour d\'article #%s (code : %s)');

/* Import AFP */
DEFINE('_BIZ_NO_INDEX_FILE_FOR_CHANNEL','Pas de fichier d\'index pour la rubrique :');
DEFINE('_BIZ_UPDATE_CHANNEL','Mise à jour de la rubrique : ');
DEFINE('_BIZ_NO_CHANNEL_FOR_FOLDER','Aucune rubrique associé au répertoire : ');
DEFINE('_BIZ_CREATE_CHANNEL','Création de la rubrique : ');
DEFINE('_BIZ_INDEX_CHANNEL_UNCHANGED','L\'index de rubrique %s [%s] est inchangé depuis le dernier import (%s &gt; %s)');
DEFINE('_BIZ_INDEX_INVALID_XML','Le fichier %s/index.xml ne semble pas être un XML valide');
DEFINE('_BIZ_ARTICLE_XML_ERROR','Erreur XML dans l\'article : ');
DEFINE('_BIZ_PROCESS_PHOTO_OF_ARTICLE','Traitement des photos de l\'article #%s (code AFP : %s)');
DEFINE('_BIZ_ASSOC_PHOTO_ARTICLE','Association de la photo #%s à l\'article #%s');
DEFINE('_BIZ_ASSOC_PHOTO_ARTICLE_DONE','Photo %s associées à l\'article %s');
DEFINE('_BIZ_ORIGINAL_PHOTO_NOT_FOUND','Erreur : l\'original de la photo %s est introuvable');
DEFINE('_BIZ_PROCESS_PHOTO','Traitement de la photo ');
DEFINE('_BIZ_THUMBNAIL_NOT_FOUND','Erreur : la vignette %s de la photo %s est introuvable');
DEFINE('_BIZ_CREATE_PHOTO_AFP','Création de la photo (code AFP : %s)');
DEFINE('_BIZ_UPDATE_PHOTO_AFP','Mise a jour de la photo #%s (code AFP : %s)');
DEFINE('_BIZ_CANNOT_COPY_PHOTO_AFP','Impossible de copier la photo %s/%s vers %s');
DEFINE('_BIZ_CANNOT_COPY_THUMBNAIL_AFP','Impossible de copier la vignette %s/%s vers %s');
DEFINE('_BIZ_NOINIT_PHOTO_XML','Impossible d\'initialiser la photo %s à partir du XML');

/* Import Bizobject */
DEFINE('_BIZ_CREATE_BIZOBJECT','Création de l\'objet \'%s\' (code BizObject : %s)');
DEFINE('_BIZ_UPDATE_BIZOBJECT','Mise a jour de l\'objet \'%s\' #%s (code BizObject : %s)');

/* Import Photos */
DEFINE('_BIZ_IPTC_NOT_FOUND','Champs IPTC APP13 non-disponibles');
DEFINE('_BIZ_LEGEND_TITLE_NOT_FOUND','Champs Légende et Titre non-disponibles');
DEFINE('_BIZ_KEYWORDS_NOT_FOUND','Champs Mot-clé non-disponible');
DEFINE('_BIZ_IMPORT_UPDATE_PHOTO','Mise à jour de la photo :');
DEFINE('_BIZ_IMPORT_INSERT_PHOTO','Enregistrement de la photo :');
DEFINE('_BIZ_IMPORT_PROCESS','Traitement des fichiers :');
DEFINE('_BIZ_IMPORT_PHOTO_FILE','Fichier photo :');
DEFINE('_BIZ_ADD_PHOTO_TO_SLIDESHOW','Ajout de la photo au diaporama :');
DEFINE('_BIZ_NO_SLIDESHOW_FOR_PHOTO_KEYWORDS','Pas de diaporama correspondant aux mot-clés [%s] de la photo %s');
DEFINE('_BIZ_FOR_PHOTO','pour la photo');
DEFINE('_BIZ_REJECT','Rejeté');
DEFINE('_BIZ_INVALID_PICTURE','Image invalide (est-ce bien un fichier image ?)');
DEFINE('_BIZ_IMPORT_PHOTO_NO_TITLE',' n\'a pas de titre.');
DEFINE('_BIZ_IMPORT_PHOTO_OK','Cette photo a été importée :');

/* Print Information */
DEFINE('_BIZ_PUBLICATION','Édition');
DEFINE('_BIZ_ISSUE','Parution');
DEFINE('_BIZ_ADD_ISSUE','Ajouter une parution');
DEFINE('_BIZ_ISSUENUMBER','Numéro de parution');
DEFINE('_BIZ_ISSUEDATE','Date parution');
DEFINE('_BIZ_PUBLICATIONNAME','Titre');
DEFINE('_BIZ_PRINT_INFORMATIONS','Informations papier');
DEFINE('_BIZ_PAGE_NUMBER','Numéro de page');
DEFINE('_BIZ_ISSUES','Parutions');
DEFINE('_BIZ_NO_ISSUES','Pas de parution');
DEFINE('_BIZ_NO_CHANNELS','Pas de rubrique');
DEFINE('_BIZ_CREATE_PUBLICATION','Creation du titre : \'%s\'');
DEFINE('_BIZ_UPDATE_PUBLICATION','Mise à jour du titre : \'%s\'');
DEFINE('_BIZ_CREATE_ISSUE','Creation de la parution : \'%s\'');
DEFINE('_BIZ_UPDATE_ISSUE','Mise à jour de la parution : \'%s\'');

// Search
DEFINE('_BIZ_SEARCH_ADD_TO_SELECTED_BIN','Add to selected bin');
DEFINE('_BIZ_TOGGLE','Basculer');
DEFINE('_BIZ_MODIFIED_AT','Modifié le');
DEFINE('_BIZ_ITEMS_FOUND_IN',' éléments trouvés en ');
DEFINE('_BIZ_START_NEW_SEARCH','Faite une nouvelle recherche');
DEFINE('_BIZ_WITH_SELECTED_FILTERS','avec les filtres sélectionnés');
DEFINE('_BIZ_FILTER_SELECTION','la sélection de filtres');
DEFINE('_BIZ_REFINE','Affiner le resultat en utilisant les filtres');
DEFINE('_BIZ_SELECT_ALL','Tout selectionner');
DEFINE('_BIZ_CREATED_BY','Crée par ');
DEFINE('_BIZ_ON',' le ');
DEFINE('_BIZ_MODIFIED_BY','modifié par ');
DEFINE('_BIZ_CANNOT_LOAD_CONFIGURARTION_FILE','Impossible de loader le fichier search_configuration.xml');
DEFINE('_BIZ_SEARCHES','Recherches');
DEFINE('_BIZ_QUERY','Requête');
DEFINE('_BIZ_NATIVE_QUERY','Requête native');
DEFINE('_BIZ_MY_SAVED_SEARCHES','Mes recherches sauvegardées');
DEFINE('_BIZ_SEARCH_HISTORY','Historique de recherche');
DEFINE('_BIZ_REMOVE','Supprimer');
DEFINE('_BIZ_CURRENT_SEARCH','la recherche courante');
DEFINE('_BIZ_DASHBOARD','Ajouter au dashboard ?');
DEFINE('_BIZ_CREATE','Créer');
DEFINE('_BIZ_EMPTY_BIN','un panier vide');
DEFINE('_BIZ_SELECTED_ITEMS','Item(s) selectionné(s)');
DEFINE('_BIZ_ADD_TO_SELECTED_BIN','Ajouter au panier selectionné');
DEFINE('_BIZ_SELECTED_BIN','le panier selectionné');
DEFINE('_BIZ_ALL_BINS','tout les paniers');
DEFINE('_BIZ_CREATE_BIN','Créer un panier');
DEFINE('_BIZ_MANAGE','Gérer');
DEFINE('_BIZ_BIN','le panier');
DEFINE('_BIZ_NO_DETAIL','---'); // Displayed in the search results when an attribute is undefined
DEFINE('_BIZ_GRID_VIEW','Afficher sous forme de grille');
DEFINE('_BIZ_LIST_VIEW','Afficher sous forme de liste');
DEFINE('_BIZ_TOP_CONCEPTS','Concepts');
DEFINE('_BIZ_TOP_ENTITIES','Entités');
DEFINE('_BIZ_TOP_EDITORIAL_TAGS','"Tags" éditoriales');
DEFINE('_BIZ_TOP_SUBJECTS','Sujets');
DEFINE('_BIZ_ASSET_TYPES','Types d\'objects');
DEFINE('_BIZ_DATE_RANGES','Intervalles (dates)');
DEFINE('_BIZ_SOURCES','Sources');
DEFINE('_BIZ_FILTERS','Filtres');
DEFINE('_BIZ_SHARED','Public ?');
DEFINE('_BIZ_SHOW_UI','UI');

// widget
DEFINE('_BIZ_DESIGN_TOOLBOX','Boite à outil');
DEFINE('_BIZ_WIDGET_TEMPLATE','Modèle de page');
DEFINE('_BIZ_WIDGET_ADD','Ajouter un gadget');
DEFINE('_BIZ_WIDGET_ZONE','dans la zone');
DEFINE('_BIZ_LIMIT_TO','Limiter à');
DEFINE('_BIZ_LEFT_ZONE','Zone de gauche');
DEFINE('_BIZ_RIGHT_ZONE','Zone de droite');
DEFINE('_BIZ_MAIN_ZONE','Zone principale');
DEFINE('_BIZ_REMOVE','Enlever cette boîte');
DEFINE('_BIZ_SAVE_WIDGETS','Sauver les gadgets');
DEFINE('_BIZ_REFRESH_WIDGET_BUGS','Rafraîchir');
DEFINE('_BIZ_EDIT_SETTINGS','Changer la configuration');
DEFINE('_BIZ_DOCUMENTS','Documents');
DEFINE('_BIZ_MEDIA','Médias');
DEFINE('_BIZ_WIRE_CONTENT','Contenu du fil');
DEFINE('_BIZ_NEWS_CONTENT','Nouvelle');

// Date Format
DEFINE('_DATE_FORMAT','%Y-%m-%d');
DEFINE('_DATE_TIME_FORMAT','%Y-%m-%d @ %H:%M');

// Publication workflow
DEFINE('_BIZ_VERSION_CREATED_BEFORE_PUBLICATION','Version créée automatiquement avant la publication');
DEFINE('_BIZ_VERSION_CREATED_ON_DEMAND','Version créée par l\'utilisateur ');

// Export
DEFINE('_BIZ_EXPORT','Exporter');
DEFINE('_BIZ_EXPORT_COLLECTION','Exporter vers une collection');
DEFINE('_BIZ_EXPORTED_COLLECTION','Les éléments ont été exportés correctement');
DEFINE('_BIZ_COLLECTION_NAME_MANDATORY','Le nom de la collection est obligatoire');
DEFINE('_BIZ_NO_ITEMS_SELECTED','Aucun item selectionné');
DEFINE('_BIZ_CHOOSE_EXPORT_OBJECT','Choisir le type de bizobjet');
DEFINE('_BIZ_CREATE_NEW','Créer un nouveau bizobjet');
DEFINE('_BIZOBJECTS','Bizobjets');
DEFINE('_BIZ_CHOOSE_BIZOBJECT','Choisir un bizobjet');
DEFINE('_BIZ_EXPORT_SUCCESFULL_TO','Les bizobjets ont été correctement importés vers ');
DEFINE('_BIZ_EXPORT_ERROR','Une erreur est survenue lors de l\'export. Veuillez recommencer.');
DEFINE('_BIZ_CHOOSE_CHANNEL','Choisir une section');
DEFINE('_BIZ_CHOOSE_COLLECTION','Choisir une collection');
DEFINE('_BIZ_CHOOSE_SLIDESHOW','Choisir un diaporama');

// move to channel
DEFINE('_BIZ_MOVE_CHANNEL','Déplacer vers une section');
DEFINE('_BIZ_MOVE_SUCCESS','Les objects choisis ont été déplacés correctement vers la section ');

// FOLDER
DEFINE('_BIZ_FOLDER','Dossier');
DEFINE('_BIZ_PARENT_FOLDER','Dosssier parent');
DEFINE('_BIZ_FOLDERS_HIERARCHY','Hiérarchie des dossiers');
DEFINE('_BIZ_FOLDER_PERM','Permanent');
DEFINE('_BIZ_FOLDER_AUTO','Automatique');
DEFINE('_BIZ_FOLDER_TEMP','Temporaire');

DEFINE('_BIZ_FOR_SELECTION','Pour la selection');
DEFINE('_BIZ_SERVICES','Services');

// OTHER
DEFINE('_BIZ_ALERT','ALERTE');
DEFINE('_BIZ_ERROR_CHANNELID_IS_MANDATORY','Attention: La catégorie principale est obligatoire (bouton radio)');