<?php
/**
 * Project:     WCM
 * File:        en.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
require_once("bizEn.php");
require_once("channels.php");
require_once("iptcTags.php");
/**
 * Tells WCM which language is currently defined in this constant list
  */
DEFINE('_BIZ_WCM_LANGUAGE','en');

/**
 * Specific (business dependant) language resources
 *
 * english version
 */

// Common
DEFINE('_BIZ_COPYRIGHT','Authoring back-end - &copy;2006-2008 Nstein');
DEFINE('_BIZ_ACCESS_PRIVATE','Private');
DEFINE('_BIZ_ACCESS_PROTECTED','Protected');
DEFINE('_BIZ_ACCESS_PUBLIC','Public');
DEFINE('_BIZ_ACCESS_TITLE','Access');
DEFINE('_BIZ_ADD','Add');
DEFINE('_BIZ_ADD_PHOTO_MSG','Add photo');
DEFINE('_BIZ_AJAX_PROCESSING_DONE_MSG','Processing done');
DEFINE('_BIZ_AJAX_PROCESSING_MSG','Processing');
DEFINE('_BIZ_ALL','All');
DEFINE('_BIZ_ALL_DATES','All dates');
DEFINE('_BIZ_APPROVE','Approve');
DEFINE('_BIZ_APPROVED','Approved');
DEFINE('_BIZ_ASSET','Business object');
DEFINE('_BIZ_AUDIO','Audio');
DEFINE('_BIZ_AUTHOR','Byline');
DEFINE('_BIZ_A_POSTERIORI','Automatically publish');
DEFINE('_BIZ_A_PRIORI','Requires approval');
DEFINE('_BIZ_APPLY','Apply');
DEFINE('_BIZ_BEGINDATE','Begin date');
DEFINE('_BIZ_BIZCLASS','Bizclass');
DEFINE('_BIZ_BLOG','blog');
DEFINE('_BIZ_BODY','Body');
DEFINE('_BIZ_BY','By');
DEFINE('_BIZ_CANCEL','Cancel');
DEFINE('_BIZ_CAPTION','Caption');
DEFINE('_BIZ_CHANGE','Change');
DEFINE('_BIZ_CHECKEDOUT','This object is checked out by: ');
DEFINE('_BIZ_CHECKEDOUT_NOT_REMOVED',' is checked out and could not be removed.');
DEFINE('_BIZ_CHOOSE_VISUAL','Choose visual');
DEFINE('_BIZ_CHOOSE_LINKS','Choose links');
DEFINE('_BIZ_CLOSED','Closed (but online)');
DEFINE('_BIZ_CONTENT','Content');
DEFINE('_BIZ_CONTENT_FOR','Customized content for:');
DEFINE('_BIZ_CREATEDAT','Created on ');
DEFINE('_BIZ_CREATEDBY','Created by ');
DEFINE('_BIZ_CREATED_AT','Created at');
DEFINE('_BIZ_CREATE_NEW_CONTENT','Create new content');
DEFINE('_BIZ_CREATE_THUMBNAIL','Create Thumbnail');
DEFINE('_BIZ_CREDITS','Credits');
DEFINE('_BIZ_CUSTOM','Custom');
DEFINE('_BIZ_DASHBOARD_CONFIGURED','This dashboard can be configured to fit your needs.');
DEFINE('_BIZ_DATE','date');
DEFINE('_BIZ_DELETE','Delete');
DEFINE('_BIZ_DELETE_VISUAL','Delete visual');
DEFINE('_BIZ_DESCRIPTION','Description');
DEFINE('_BIZ_DISPLAYDATE','Display date');
DEFINE('_BIZ_DISPLAY_MODES','Display Modes');
DEFINE('_BIZ_THE_DIRECTORY','The directory ');
DEFINE('_BIZ_DOESNT_EXIST','doesn\'t exist');
DEFINE('_BIZ_NO_XML_FILES','There are no xml files to process in the directory');
DEFINE('_BIZ_FILES_PROCESSED',' files have been processed');
DEFINE('_BIZ_DOCUMENT','document');
DEFINE('_BIZ_DONE','Done');
DEFINE('_BIZ_EDIT','Edit');
DEFINE('_BIZ_EMAIL','Email');
DEFINE('_BIZ_EMPTY_LIST','Clear');
DEFINE('_BIZ_END_DATE','End date');
DEFINE('_BIZ_ENGLISH','English');
DEFINE('_BIZ_ERASE','Erase');
DEFINE('_BIZ_EXECUTION_COMMAND','Your command is being executed. Please wait...');
DEFINE('_BIZ_EXPIRATIONDATE','Expiration date');
DEFINE('_BIZ_STARTINGDATE','Starting date');
DEFINE('_BIZ_EXTERNAL_LINK','External link');
DEFINE('_BIZ_FILE','File');
DEFINE('_BIZ_FILE_MUST_BEGIN','File must begin by ');
DEFINE('_BIZ_FIND','Search');
DEFINE('_BIZ_FLASH','Flash');
DEFINE('_BIZ_FLAT','Not threaded');
DEFINE('_BIZ_FORMAT','Format');
DEFINE('_BIZ_FOR','for');
DEFINE('_BIZ_FREE_HTML','Free HTML');
DEFINE('_BIZ_FREE_TEXT','Plain Text');
DEFINE('_BIZ_FRENCH','French');
DEFINE('_BIZ_FROM','From');
DEFINE('_BIZ_FULLTEXT','Full text');
DEFINE('_BIZ_GENERATION_RESULT','Generation result');
DEFINE('_BIZ_HIERARCHICAL','Threaded');
DEFINE('_BIZ_ID','Id');
DEFINE('_BIZ_IMAGE','Image');
DEFINE('_BIZ_IMPORTATION','Importation');
DEFINE('_BIZ_IMPORT','Import');
DEFINE('_BIZ_IMPORT_SUCCESS','Process completed successfully');
DEFINE('_BIZ_IMPORT_ENDING','Ending process...');
DEFINE('_BIZ_INDEX','Index');
DEFINE('_BIZ_INVALID_CRITERIONS','Invalid criteria');
DEFINE('_BIZ_INVALID_XML','The XML is invalid. Please contact your administrator.');
DEFINE('_BIZ_INVALID_CLASSNAME','Invalid classname: %s');
DEFINE('_BIZ_INVALID_ID','Invalid identifier: %s');
DEFINE('_BIZ_INVALID_OBJECT_XML','Invalid XML representation for %s object %s');
DEFINE('_BIZ_ITEM_LINKS','Item\'s links');
DEFINE('_BIZ_LIST_DISPLAY','Display as list');
DEFINE('_BIZ_IS_URL','Is this a URL?');
DEFINE('_BIZ_KB','kb');
DEFINE('_BIZ_KEYWORDS','Keywords');
DEFINE('_BIZ_KIND_OF_FILE','Type of file');
DEFINE('_BIZ_LABEL','Label');
DEFINE('_BIZ_LANGUAGE','Language');
DEFINE('_BIZ_LOCATION','Location');
DEFINE('_BIZ_METADATA','Metadata');
DEFINE('_BIZ_METADESCRIPTION','Meta Description');
DEFINE('_BIZ_MODIFIED','Modified ');
DEFINE('_BIZ_MODIFIEDAT','Modified on ');
DEFINE('_BIZ_MODIFIEDBY','Modified by ');
DEFINE('_BIZ_MOVEDOWN','Move down');
DEFINE('_BIZ_MOVEUP','Move up');
DEFINE('_BIZ_MUST_CHOOSE_OBJECT','You must choose a content type');
DEFINE('_BIZ_MY_BIN','My bin');
DEFINE('_BIZ_MY_FAVORITES','My favorites');
DEFINE('_BIZ_NAME','Name');
DEFINE('_BIZ_NEW','New');
DEFINE('_BIZ_NICKNAME','Nickname');
DEFINE('_BIZ_USERNAME','Username');
DEFINE('_BIZ_NOT_CHECKEDOUT','This object is not checked out');
DEFINE('_BIZ_NO_RESULT','No result returned');
DEFINE('_BIZ_NO_LINK_ENTERED','No link entered');
DEFINE('_BIZ_NO_THUMBNAIL','No Thumbnail');
DEFINE('_BIZ_NTAGS','NTags');
DEFINE('_BIZ_NUMBER_SUSPICIONS','Number of suspicious entries');
DEFINE('_BIZ_OBJECTS_NO_RESULT','No result returned');
DEFINE('_BIZ_OBJECTS_SEARCH','Search objects');
DEFINE('_BIZ_OBJECTS_SEARCH_MSG_RESULT','Result for your search: %s object%s');
DEFINE('_BIZ_OBJECTS_SEARCH_MSG_RESULT_TINY','Result : %s object%s');
DEFINE('_BIZ_OF','of');
DEFINE('_BIZ_OFFLINE','In progess (Don\'t publish yet)');
DEFINE('_BIZ_OK','OK');
DEFINE('_BIZ_ONLINE','OK to publish');
DEFINE('_BIZ_ONLINE_CLOSED','OK to publish');
DEFINE('_BIZ_ONLINE_OPEN','In progress (Don\'t publish yet');
DEFINE('_BIZ_ORIGINAL','Original');
DEFINE('_BIZ_OR','or');
DEFINE('_BIZ_PARAMETERS','Parameters');
DEFINE('_BIZ_PARENTY','Parent ID');
DEFINE('_BIZ_PASSWORD','Password');
DEFINE('_BIZ_PERMALINK','Permalink');
DEFINE('_BIZ_PDF','PDF');
DEFINE('_BIZ_PHOTO_LINKS','Photo\'s links');
DEFINE('_BIZ_PHOTO_SELECT_MSG','Result for your search: %s photo%s');
DEFINE('_BIZ_PHOTO_SELECT_TITLE_MSG','Choose a photo');
DEFINE('_BIZ_PHOTO_TOO_BIG','The image exceeds the maximum allowed size.');
DEFINE('_BIZ_PODCAST','podcast');
DEFINE('_BIZ_POLL_LINKS','Poll\'s links');
DEFINE('_BIZ_POSITION','Position');
DEFINE('_BIZ_PREPROD_WEBSITE','Preproduction website');
DEFINE('_BIZ_PREVIEW','Preview');
DEFINE('_BIZ_PREVIOUS_PAGE','Previous page');
DEFINE('_BIZ_NEXT_PAGE','Next page');
DEFINE('_BIZ_PREVIEW_WEBSITE','Preview website');
DEFINE('_BIZ_RELATIVE_PATH_FROM','Relative path from ');
DEFINE('_BIZ_PROCESSING','Processing');
DEFINE('_BIZ_PROPERTIES','Properties');
DEFINE('_BIZ_PUBLICATIONDATE','Publication date');
DEFINE('_BIZ_PUBLISH','Generate');
DEFINE('_BIZ_PUBLISHED','Published');
DEFINE('_BIZ_PUBLISHED_IN','Published in');
DEFINE('_BIZ_NEVER_PUBLISHED','N/A');
DEFINE('_BIZ_PX','px');
DEFINE('_BIZ_RANK','Rank');
DEFINE('_BIZ_REFERREDBY','Referred by');
DEFINE('_BIZ_RELEASEDATETIME','Release Date/Time');
DEFINE('_BIZ_RESET','Reset');
DEFINE('_BIZ_RESET_SYSTEM_DEFAULT','Reset to default');
DEFINE('_BIZ_RESULTS','Results');
DEFINE('_BIZ_RESULTS_FIRST','First');
DEFINE('_BIZ_RESULTS_LAST','Last');
DEFINE('_BIZ_RESULTS_NEXT','Next');
DEFINE('_BIZ_RESULTS_PREVIOUS','Previous');
DEFINE('_BIZ_ROOT_ELEMENT','(root element)');
DEFINE('_BIZ_SAVE','Save');
DEFINE('_BIZ_REPLACE','Replace');
DEFINE('_BIZ_SAVE_SEARCH','Save search');
DEFINE('_BIZ_SCORE','Score');
DEFINE('_BIZ_SEARCHING','Searching');
DEFINE('_BIZ_SEARCH_EXISTING_CONTENT','Search existing content');
DEFINE('_BIZ_SEARCH_GLOBAL','Global search');
DEFINE('_BIZ_SEARCH_GLOBAL_DAM','Import from DAM');
DEFINE('_BIZ_SEARCH_OBJECTS','Search objects');
DEFINE('_BIZ_SEARCH_RESULT','Result for your search : %s element%s');
DEFINE('_BIZ_SEE','See');
DEFINE('_BIZ_SEE_REFERENTS','See referents');
DEFINE('_BIZ_SELECT','Select');
DEFINE('_BIZ_SELECT_ARTICLE','select article');
DEFINE('_BIZ_SELECT_PHOTO_OR_CANCEL','Select a photo or click on cancel');
DEFINE('_BIZ_SELECT_VISUAL','select a visual');
DEFINE('_BIZ_SHOWHIDE_DETAILS','Show/Hide details');
DEFINE('_BIZ_SLIDESHOW_LINKS','Slideshow links');
DEFINE('_BIZ_SORTED_BY','Sorted by');
DEFINE('_BIZ_SOURCE','Source name');
DEFINE('_BIZ_STARTING','Starting');
DEFINE('_BIZ_START_DATE','Start date');
DEFINE('_BIZ_STATE_PROVINCE','State/Province');
DEFINE('_BIZ_STATUS','Status');
DEFINE('_BIZ_STATISTICS','Statistics');
DEFINE('_BIZ_WORKFLOW_STATE','State');
DEFINE('_BIZ_SUBJECT','Subject');
DEFINE('_BIZ_SUBMIT','Add');
DEFINE('_BIZ_SUBTITLE','Deck');
DEFINE('_BIZ_SURTITLE','Lead');
DEFINE('_BIZ_SUSPICIOUS','Suspicious');
DEFINE('_BIZ_SUBSCRIPTIONS','Subscriptions');
DEFINE('_BIZ_SUBSCRIPTION','Subscription');
DEFINE('_BIZ_SUBSCRIPTION_ID','Subscription id');
DEFINE('_BIZ_TAGS','Tags');
DEFINE('_BIZ_TAGS_AVAILABLE','Available Tags');
DEFINE('_BIZ_TAGS_OBJECT','Tags for ');
DEFINE('_BIZ_TASKS','Tasks');
DEFINE('_BIZ_TAXONOMIES','Taxonomies');
DEFINE('_BIZ_ADS','Ads server');
DEFINE('_BIZ_TEASER','Lead');
DEFINE('_BIZ_TEMPLATE','Template');
DEFINE('_BIZ_TEXT','Text');
DEFINE('_BIZ_THUMBNAIL','Thumbnail');
DEFINE('_BIZ_TITLE','Title');
DEFINE('_BIZ_HEADLINE','Headline');
DEFINE('_BIZ_CODE','Code');
DEFINE('_BIZ_CSS','Css');
DEFINE('_BIZ_TO','to');
DEFINE('_BIZ_TOOLS','Tools');
DEFINE('_BIZ_TYPE','Type');
DEFINE('_BIZ_UNDETERMINED','Undetermined');
DEFINE('_BIZ_UNIT','Unit');
DEFINE('_BIZ_UPDATE','Update');
DEFINE('_BIZ_URL','Url');
DEFINE('_BIZ_VALIDATE','Validate');
DEFINE('_BIZ_VALUE','Value');
DEFINE('_BIZ_VIDEO','Video');
DEFINE('_BIZ_VIEW','View');
DEFINE('_BIZ_VISUAL','Visual');
DEFINE('_BIZ_WARNING','Warning');
DEFINE('_BIZ_WEB_LINKS','Links');
DEFINE('_BIZ_CHECKIN_UNLOCKED','Cannot save object as the lock as been removed by an administrator');
DEFINE('_BIZ_SAVE_UNABLE_LOCK','Cannot save object as this object is locked by another user');
DEFINE('_BIZ_EXECUTE_PROCESS','Start');
DEFINE('_BIZ_SAVED_BIZOBJECT','Saved bizobject');
DEFINE('_BIZ_PARTNER_FEEDS','Partner feeds');

// NServer and semantic metadata
DEFINE('_BIZ_SEMANTIC_DATA','Nstein Text Mining Engine');
DEFINE('_BIZ_SEMANTIC_DATA_BIZOBJECTS','Business Objects');
DEFINE('_BIZ_SEMANTIC_DATA_KINDS','Type of metadata');
DEFINE('_BIZ_SEMANTIC_DATA_WHERE','SQL Where Clause');
DEFINE('_BIZ_SEMANTIC_DATA_FORCE_UPDATE','Force Update');
DEFINE('_BIZ_UPDATE_SEMANTIC_DATA','Refresh semantic footprint');
DEFINE('_BIZ_UPDATE_SEMANTIC_DATA_ALERT','WARNING: This action will refresh all of the semantic information associated with your content. This includes IPTC categorization, concepts, entities (Organizations, people and places), tone and summaries. This process can take a long time depending on the amount of content to refresh.');
DEFINE('_BIZ_UPDATE_SEMANTIC_DATA_FAILED','Update failed');
DEFINE('_BIZ_UPDATE_SEMANTIC_DATA_SUCCEEDED','Update succeeded');
DEFINE('_BIZ_RESET_SEMANTIC_DATA','Reset semantic data');
DEFINE('_BIZ_NSERVER','Text mining');
DEFINE('_BIZ_CONCEPT','Concept');
DEFINE('_BIZ_GEOGRAPHICAL_LOCATION','Geographical location');
DEFINE('_BIZ_PERSON_NAME','Person names');
DEFINE('_BIZ_ORGANIZATION','Organisation');
DEFINE('_BIZ_CATEGORIES_SUGGESTED','Suggested categories');
DEFINE('_BIZ_CATEGORIES_AVAILABLE','Available categories');
DEFINE('_BIZ_SIMILAR_ITEMS','Similar Items');
DEFINE('_BIZ_SIZE','Size');
DEFINE('_BIZ_SUBJECTIVITY_NEGATIVE','Negative');
DEFINE('_BIZ_SUBJECTIVITY_NEUTRAL','Neutral');
DEFINE('_BIZ_SUBJECTIVITY_POSITIVE','Positive');
DEFINE('_BIZ_NFINDER_ON','Organizations');
DEFINE('_BIZ_NCONCEPT_EXTRACTOR','Concepts');
DEFINE('_BIZ_NFINDER_PN','Person Names');
DEFINE('_BIZ_NSUMMARIZER','Summary');
DEFINE('_BIZ_NFINDER_GL','Geographic Locations');
DEFINE('_BIZ_NSENTIMENT','Sentiment / Subjectivity');
DEFINE('_BIZ_TONE','Tone');
DEFINE('_BIZ_SUBJECTIVITY','Subjectivity');
DEFINE('_BIZ_SUGGEST','Suggest');
DEFINE('_BIZ_NO_SUGGESTION','TME has found no relevant suggestion');
DEFINE('_BIZ_SIMILAR_ARTICLES','Similar articles');
DEFINE('_BIZ_CONCEPTS','Concepts');
DEFINE('_BIZ_RELATED_ARTICLES','Related Articles');
DEFINE('_BIZ_SUBJECT_MAP','Subject map');

// Rating and hit counter
DEFINE('_BIZ_RATING','Rating');
DEFINE('_BIZ_RATING_COUNT','Total votes');
DEFINE('_BIZ_VOTES','Votes');
DEFINE('_BIZ_RATING_TOTAL','Points');
DEFINE('_BIZ_RATING_VALUE','Rating value');
DEFINE('_BIZ_HIT_COUNT','Hit counter');

// Reporting
DEFINE('_BIZ_ACCOUNT_USER','User account');
DEFINE('_BIZ_PARENT_ACCOUNT_USER','Parent account');
DEFINE('_BIZ_CREATION_DATE_ACCOUNT_USER','Creation date');

// Reindexation
DEFINE('_BIZ_REINDEX_CONTENT_TITLE','Re-index editorial content on the search engine');
DEFINE('_BIZ_REINDEX_CONTENT_WARNING_MSG','<span class="warning">Warning:</span> This action will allow you to re-index stored data to render it searchable <br/>=> Warning: the process will take a lot of time.');
DEFINE('_BIZ_KIND_ELEMENT','Business object');
DEFINE('_BIZ_INDEXING_RESULT','Indexing result');
DEFINE('_BIZ_NUMBER_RECORD_BUSINESS_DB','In database');
DEFINE('_BIZ_NUMBER_RECORD_SEARCH_ENGINE','Indexed');
DEFINE('_BIZ_EXECUTE_REINDEXATION','Begin re-indexing');

// Maintenance and purge
DEFINE('_BIZ_PURGE','Purge');
DEFINE('_BIZ_PURGE_SELECT_OBJECTS_TO_PURGE','Select objects to purge');
DEFINE('_BIZ_PURGE_LOCKED_OBJETS','Purge even locked objects');
DEFINE('_BIZ_PURGE_CONTENT_TITLE','Purge and perform maintenance');
DEFINE('_BIZ_PURGE_CONTENT_WARNING_MSG','<span class="warning">Warning:</span> This action will remove all expired objects and banned webusers');
DEFINE('_BIZ_PURGE_RESULT','Purge result');
DEFINE('_BIZ_NUMBER_RECORD_TO_PURGE','Expired');
DEFINE('_BIZ_EXECUTE_PURGE','Execute purge');
DEFINE('_BIZ_PURGE_DONE','Purge done');
DEFINE('_BIZ_PURGE_NOTHING_TO_PURGE','Nothing to purge!');
DEFINE('_BIZ_PURGE_DONE_BUT_WITH_LOCKED','Purge done, however some objects where locked and have not been purged');

// Locks
DEFINE('_BIZ_SAVE_CONFIRM_ERASE','This object has been changed by another user. Do you want to replace the last version with your changes anyway?');
DEFINE('_BIZ_OBJECT_UNLOCKED','Object unlocked');
DEFINE('_BIZ_OBJECT_LOCKED','Object locked');
DEFINE('_BIZ_LOCK_MANAGEMENT','Lock management');

// Date interval
DEFINE('_BIZ_NEXT_7_DAYS','Next 7 days');
DEFINE('_BIZ_NEXT_3_DAYS','Next 3 days');
DEFINE('_BIZ_TOMORROW','Tomorrow');
DEFINE('_BIZ_TODAY','Today');
DEFINE('_BIZ_YESTERDAY','Yesterday');
DEFINE('_BIZ_LAST_3_DAYS','Last 3 days');
DEFINE('_BIZ_LAST_7_DAYS','Last 7 days');
DEFINE('_BIZ_LAST_MONTH','Last month');
DEFINE('_BIZ_LAST_3MONTHS','Last 3 months');
DEFINE('_BIZ_LAST_YEAR','Last year');

// Error Messages
DEFINE('_BIZ_ERROR','Error: ');
DEFINE('_BIZ_ERROR_ON_UNLOCK','Unlock failed: ');
DEFINE('_BIZ_ERROR_ON_LOCK','Lock failed: ');
DEFINE('_BIZ_ERROR_BANNED_DATE_FORMAT','The date format for Banned Date must be YYYY-MM-DD');
DEFINE('_BIZ_ERROR_BAN_BEGIN_DATE_FORMAT','The ban date must be after the begin date');
DEFINE('_BIZ_ERROR_BEG_END_DATE_FORMAT','The end date must be after the begin date');
DEFINE('_BIZ_ERROR_BEGINPERIOD_DATE_FORMAT','The date format for Start Date must be YYYY-MM-DD');
DEFINE('_BIZ_ERROR_BEGIN_DATE_FORMAT','The date format for Begin Date must be YYYY-MM-DD');
DEFINE('_BIZ_ERROR_END_DATE_FORMAT','The date format for End Date must be YYYY-MM-DD');
DEFINE('_BIZ_ERROR_DISPLAY_DATE_FORMAT','The date format for Display Date must be YYYY-MM-DD');
DEFINE('_BIZ_ERROR_DATE_CHECK_WRONG','End date must be greater than the start date');
DEFINE('_BIZ_ERROR_START_DATE_CHECK_WRONG','Start date must be lesser than the end date');
DEFINE('_BIZ_ERROR_PUBLICATION_DATE_FORMAT','The date format for Publication Date must be YYYY-MM-DD');
DEFINE('_BIZ_ERROR_RELEASE_DATETIME_FORMAT','The date/time format for Release Date/Time must be YYYY-MM-DD HH:MM');
DEFINE('_BIZ_ERROR_EXPIRATION_DATE_FORMAT','The date format for Expiration Date must be YYYY-MM-DD');
DEFINE('_BIZ_ERROR_SUBSCRIPTION_DATES','Subscriptions must have a start and end date');
DEFINE('_BIZ_ERROR_SUBSCRIPTION_VALUE','Subscription value cannot be empty');
DEFINE('_BIZ_ERROR_POSITION_FORMAT','Rank must be a number');
DEFINE('_BIZ_ERROR_EXP_PUB_DATE_FORMAT','The publication date must be before the expiration date');
DEFINE('_BIZ_ERROR_EXPIRATION_DATE_TODAY','The expiration date shouldn\\\'t be before today');
DEFINE('_BIZ_ERROR_EMAIL_FORMAT','Please correct email address.');
DEFINE('_BIZ_ERROR_EMAIL_ANSWER_FORMAT','Please correct reply to address.');
DEFINE('_BIZ_ERROR_SAVE','Cannot save');
DEFINE('_BIZ_ERROR_HEADLINE_NOT_COMPLETED','Headline field is mandatory, please fill it in');
DEFINE('_BIZ_ERROR_TITLE_NOT_COMPLETED','Title field is mandatory, please fill it in');
DEFINE('_BIZ_ERROR_USERNAME_LENGTH','The username is too long. It is limited to 50 characters');
DEFINE('_BIZ_ERROR_URL_NOT_COMPLETED','URL field is mandatory, please fill it in');
DEFINE('_BIZ_ERROR_NB_SUSPICIONS','Number of suspicious web user must be less than 10 characters');
DEFINE('_BIZ_ERROR_FIRSTNAME_NOT_COMPLETED','First name field is mandatory, please fill it in');
DEFINE('_BIZ_ERROR_NAME_NOT_COMPLETED','Name field is mandatory, please fill it in');
DEFINE('_BIZ_ERROR_LASTNAME_NOT_COMPLETED','Last name field is mandatory, please fill it in');
DEFINE('_BIZ_ERROR_USERNAME_NOT_COMPLETED','Username field is mandatory, please fill it in');
DEFINE('_BIZ_ERROR_REFERENT_NOT_COMPLETED','You must choose a referent for this comment');
DEFINE('_BIZ_ERROR_REFERENT_CLASS_NOT_COMPLETED','You must choose a referent class for this comment');
DEFINE('_BIZ_ERROR_NICKNAME_NOT_COMPLETED','The user nickname must be speficied');
DEFINE('_BIZ_ERROR_CLASSNAME_NOT_COMPLETED','ClassName field is mandatory, please fill it in');
DEFINE('_BIZ_ERROR_TEXT_NOT_EMPTY','The text field cannot be empty.');
DEFINE('_BIZ_ERROR_SCORE_NUM','Score has to be a number between 0 and 100');
DEFINE('_BIZ_ERROR_PHOTO_FORMAT','Image format not supported');
DEFINE('_BIZ_ERROR_UPLOAD_SIZE','Uploaded file exceeded maximum allowed filesize: %s');
DEFINE('_BIZ_ERROR_UPLOAD_PARTIAL','File was only partially uploaded.');
DEFINE('_BIZ_ERROR_UPLOAD_NO_FILE','No file was actually uploaded');
DEFINE('_BIZ_ERROR_UPLOAD_NO_TMP_DIR','No temporary directory was found to write uploaded file to.');
DEFINE('_BIZ_ERROR_UPLOAD_CANT_WRITE','Could not write uploaded file to disk');
DEFINE('_BIZ_ERROR_UPLOAD_EXTENSION','File upload stopped by extension');
DEFINE('_BIZ_ERROR_SITE_IS_MANDATORY','Site id is mandatory');
DEFINE('_BIZ_ERROR_CHANNEL_IS_MANDATORY','Section id is mandatory');

// Errors associated to fields validation
DEFINE('_BIZ_ERROR_CODE_IS_MANDATORY','Code is mandatory');
DEFINE('_BIZ_ERROR_CODE_TOO_LONG','Code is too long');
DEFINE('_BIZ_ERROR_TITLE_IS_MANDATORY','Title is mandatory');
DEFINE('_BIZ_ERROR_TITLE_TOO_LONG','Title is too long');
DEFINE('_BIZ_ERROR_CHAPTER_TITLE_TOO_LONG','Page title is too long');
DEFINE('_BIZ_ERROR_SUBTITLE_TOO_LONG','Deck is too long');
DEFINE('_BIZ_ERROR_SUPTITLE_TOO_LONG','Lead is too long');
DEFINE('_BIZ_ERROR_KEYWORDS_TOO_LONG','Keywords are too long');
DEFINE('_BIZ_ERROR_CREDITS_TOO_LONG','Credits are too long');
DEFINE('_BIZ_ERROR_AUTHOR_TOO_LONG','Author is too long');
DEFINE('_BIZ_ERROR_NICKNAME_TOO_LONG','Nickname is too long');
DEFINE('_BIZ_ERROR_USERNAME_IS_MANDATORY','Username is mandatory');
DEFINE('_BIZ_ERROR_USERNAME_TOO_LONG','Username is too long');
DEFINE('_BIZ_ERROR_FIRSTNAME_TOO_LONG','Firstname is too long');
DEFINE('_BIZ_ERROR_LASTNAME_TOO_LONG','Lastname is too long');
DEFINE('_BIZ_ERROR_EMAIL_TOO_LONG','Email address is too long');
DEFINE('_BIZ_ERROR_CITY_TOO_LONG','City is too long');
DEFINE('_BIZ_ERROR_COUNTRY_TOO_LONG','Country is too long');
DEFINE('_BIZ_ERROR_POSTAL_CODE_TOO_LONG','Postal code is too long');
DEFINE('_BIZ_ERROR_PHONE_TOO_LONG','Phone number is too long');
DEFINE('_BIZ_ERROR_STATE_TOO_LONG','State is too long');
DEFINE('_BIZ_ERROR_SOURCE_TOO_LONG','Source is too long');
DEFINE('_BIZ_ERROR_SOURCE_ID_TOO_LONG','SourceId is too long');
DEFINE('_BIZ_ERROR_SOURCE_VERSION_TOO_LONG','SourceVersion is too long');
DEFINE('_BIZ_ERROR_SENDER_TOO_LONG','Sender is too long');
DEFINE('_BIZ_ERROR_FROM_TOO_LONG','From is too long');
DEFINE('_BIZ_ERROR_REPLY_TO_TOO_LONG','Reply-to is too long');
DEFINE('_BIZ_ERROR_PUBLICATIONNAME_IS_MANDATORY','Publication name is mandatory');
DEFINE('_BIZ_ERROR_ISSUENUMBER_NUM','Issue number must be equal or greater than 1');
DEFINE('_BIZ_ERROR_PERMALINKS_TOO_LONG','Permalinks is too long');

// Question Messages
DEFINE('_BIZ_QUESTION_CONFIRM_DELETE','Are you sure you want to delete that element?');

// BizObject
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_BIZOBJECT','Choose a parent object for this comment');
DEFINE('_BIZ_CONTRIBUTION_SELECT_BIZOBJECT_LIST','Select a parent object in the list below');
DEFINE('_BIZ_CHOOSE_NEXT_TRANSITION','Choose next transition');
DEFINE('_BIZ_SWITCH_TO_OBJECT_SITE','In order to edit this object, you must switch to this object\'s working site');

// Channel
DEFINE('_BIZ_CHANNEL','Section');
DEFINE('_BIZ_CHANNELS','Sections');
DEFINE('_BIZ_CHANNEL_CHOOSE_CONTENT','Choose content for this section');
DEFINE('_BIZ_CHANNEL_CONTENT','Content for this section');
DEFINE('_BIZ_CHANNEL_SEARCH','Search Section');
DEFINE('_BIZ_NEW_QUERY','New query');
DEFINE('_BIZ_CHOOSE_QUERY','Choose query');
DEFINE('_BIZ_CHANNEL_INDENT','&nbsp;&nbsp;&nbsp;');
DEFINE('_BIZ_CHANNEL_INDENT_SYMBOL',' :: ');
DEFINE('_BIZ_CHANNEL_TREE','Section menu');
DEFINE('_BIZ_SEE_ORGANIZATION_CHANNEL','See the organization of this channel');
DEFINE('_BIZ_CHANNEL_LOAD_NEAREST_CONTENT','Latest');
DEFINE('_BIZ_CHANNEL_MANAGE_DATE','There is no particular content for the specified date. Content for the following date has been loaded: ');
DEFINE('_BIZ_CHANNEL_EMPTY_CONTENT','The content for this date has been cleared');
DEFINE('_BIZ_MANAGE_CHANNEL','Manage section');
DEFINE('_BIZ_DESIGN_CHANNEL','Design');
DEFINE('_BIZ_CHANNEL_MANAGE_MSG','Managing section ');
DEFINE('_BIZ_CHANNEL_MANAGE_CONTENT_OF','Managing content of ');
DEFINE('_BIZ_PREVIEW_CHANNEL_MSG','Preview of section');
DEFINE('_BIZ_CONTENT_CHANNEL','Content');
DEFINE('_BIZ_CHANNEL_ORGANISATION','Section Organisation');
DEFINE('_BIZ_CHANNEL_REQUEST_CHANNEL','Request');
DEFINE('_BIZ_CHANNEL_REQUEST','Requests of the section');
DEFINE('_BIZ_FORCED_CHANNEL','Content forced');
DEFINE('_BIZ_CHANNEL_FORCED','Content forced of the section');
DEFINE('_ADD_FORCED_BIZOBJECT','Add content');
DEFINE('_BIZ_DATE_MANAGEMENT','Date Management');
DEFINE('_BIZ_FORCED_MANAGEMENT','Forced Item Management');
DEFINE('_BIZ_CUSTOM_MANAGEMENT','Custom Management');
DEFINE('_BIZ_MIXED_MANAGEMENT','Mixed management');
DEFINE('_BIZ_ORDER_BY','Order by');
DEFINE('_BIZ_LIMIT','Limit');
DEFINE('_BIZ_BUILD_QUERY','Build query');

// Pages : website/section
DEFINE('_BIZ_SECTION_PROMO_PANEL','Promotional panel');
DEFINE('_BIZ_SECTIONS_HIERARCHY','Sections hierarchy');
DEFINE('_BIZ_SECTION_CONTENT_RULES','Automated content setup');
DEFINE('_BIZ_SECTION_CONTENT_RULES_MANAGEMENT','Rules');
DEFINE('_BIZ_SECTION_CONTENT_RULES_OPTIONS','Options');
DEFINE('_BIZ_SECTION_CONTENT_FORCED','Forced content');

// Article
DEFINE('_BIZ_ARTICLE','Article');
DEFINE('_BIZ_ARTICLES','Articles');
DEFINE('_BIZ_ARTICLE_NO_RESULT','No result returned');
DEFINE('_BIZ_ARTICLE_SEARCH','Search Article');
DEFINE('_BIZ_ARTICLE_SEARCH_MSG_RESULT','Result for your search: %s article%s');
DEFINE('_BIZ_ARTICLE_LINKS_MSG','Choose links');
DEFINE('_BIZ_CREATE_CONTRIBUTION_ARTICLE','Create a comment for this article');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_ARTICLE','Choose an article for this comment');
DEFINE('_BIZ_CONTRIBUTION_SELECT_ARTICLE_LIST','Select an article in the list below');
DEFINE('_BIZ_SECTION','Section');
DEFINE('_BIZ_SECTIONS','Sections');
DEFINE('_BIZ_ADD_CHAPTERS','Add pages');
DEFINE('_BIZ_CHAPTER','Page');
DEFINE('_BIZ_CHAPTERS','Pages');
DEFINE('_BIZ_NEW_CHAPTER','New page');
DEFINE('_BIZ_ABSTRACT','Abstract');
DEFINE('_BIZ_PAGE_TITLE','Page headline');
DEFINE('_BIZ_PAGE_CONTENT','Page content');
DEFINE('_ADD_PAGE_BEFORE','Add page before');
DEFINE('_ADD_PAGE_AFTER','Add page after');
DEFINE('_ADD_PAGE','Add page');
DEFINE('_BIZ_NEW_INSERTS','New insert');
DEFINE('_ADD_INSERTS','Add insert');
DEFINE('_DELETE_INSERTS','Remove insert');
DEFINE('_BIZ_INSERTS_KIND','Type');
DEFINE('_BIZ_INSERTS_CONTENT','Text');
DEFINE('_BIZ_ARTICLE_INSERTS','Inserts');
DEFINE('_BIZ_PAGE_SUBTITLE','Subtitle');
DEFINE('_ADD_INSERTS_AFTER','Add insert');
DEFINE('_BIZ_INSERTS_TITLE','The number (LChiffre + LRepère) or name (LExpert) or title (Other)');
DEFINE('_BIZ_INSERTS_SOURCE','The source (LChiffre + LRepère) or fonction (LExpert)');
DEFINE('_BIZ_CHAPTER_AUTHOR','Author');
DEFINE('_BIZ_CHAPTER_COMPANY','Company');

// News Item
DEFINE('_BIZ_NEWSITEM','News item');
DEFINE('_BIZ_NEWSITEMS','News items');
DEFINE('_BIZ_NEWSITEM_NO_RESULT','No result returned');
DEFINE('_BIZ_NEWSITEM_SEARCH','Search Newsitem');
DEFINE('_BIZ_NEWSITEM_SEARCH_MSG_RESULT','Result for your search: %s newsitem%s');
DEFINE('_BIZ_NEWSITEM_LINKS_MSG','Choose links');
DEFINE('_BIZ_CREATE_CONTRIBUTION_NEWSITEM','Create a comment for this newsitem');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_NEWSITEM','Choose a newsitem for this comment');
DEFINE('_BIZ_CONTRIBUTION_SELECT_NEWSITEM_LIST','Select a newsitem in the list below');

// Forum
DEFINE('_BIZ_FORUM','Forum');
DEFINE('_BIZ_FORUMS','Forums');
DEFINE('_BIZ_FORUM_NO_RESULT','No result returned');
DEFINE('_BIZ_FORUM_SEARCH','Search Forum');
DEFINE('_BIZ_FORUM_SEARCH_MSG_RESULT','Result for your search: %s forum%s');
DEFINE('_BIZ_FORUM_LINKS_MSG','Choose links');
DEFINE('_BIZ_KIND','Kind');
DEFINE('_BIZ_POST','Post');
DEFINE('_BIZ_MODERATION','Moderation');
DEFINE('_BIZ_RESTRICTED_TOPICS','Restricted Topics');
DEFINE('_BIZ_CREATE_CONTRIBUTION_FORUM','Create a comment for this forum');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_FORUM','Choose a forum for this comment');
DEFINE('_BIZ_CONTRIBUTION_SELECT_FORUM_LIST','Select a forum in the list below');

// Item
DEFINE('_BIZ_ITEM','Item');
DEFINE('_BIZ_ITEMS','Items');
DEFINE('_BIZ_ITEM_ALLOWED_FILENAME_EXTENSIONS','Allowed file name extensions');
DEFINE('_BIZ_ITEM_TYPE','Item type');
DEFINE('_BIZ_ITEM_NO_RESULT','No result returned');
DEFINE('_BIZ_ITEM_INVALID_PATH','Invalid path');
DEFINE('_BIZ_ITEM_INVALID_SOURCE','Invalid source');
DEFINE('_BIZ_ITEM_BROWSER_DISPLAY','Browser Display');
DEFINE('_BIZ_ITEM_LIST_DISPLAY','List Display');
DEFINE('_BIZ_ITEM_SEARCH','Search items');
DEFINE('_BIZ_ITEM_SEARCH_MSG_RESULT','Result for your search: %s item%s');
DEFINE('_BIZ_ITEM_NAME','Item Name');
DEFINE('_BIZ_CHOOSE_ITEM','Choose Item');
DEFINE('_BIZ_CHOOSE_PHOTO','Choose Photo');
DEFINE('_BIZ_ENTER_FILE_OR_CANCEL','Enter a file name or click on "Cancel"');
DEFINE('_BIZ_DELETE_ITEM','Delete Item');
DEFINE('_BIZ_SELECT_ITEM','Select Item');
DEFINE('_BIZ_SELECT_ITEM_OR_CANCEL','Select an item or click on "Cancel"');
DEFINE('_BIZ_FILE_EXISTS','The specified file already exists.');
DEFINE('_BIZ_FOLDER_EXISTS','The specified folder already exists.');
DEFINE('_BIZ_IMPOSSIBLE_CREATE_FOLDER','Cannot create folder');
DEFINE('_BIZ_LOAD_ERROR','Upload error');
DEFINE('_BIZ_PARENT_FOLDER','Parent folder');

// Poll
DEFINE('_BIZ_DISPLAY_BEFORE_TOTALVOTE','(Display before total vote)');
DEFINE('_BIZ_KIND_OF_POLL','Kind of poll');
DEFINE('_BIZ_MINIMAL_VOTE','Minimum number of votes');
DEFINE('_BIZ_POLL','Poll');
DEFINE('_BIZ_POLLS','Polls');
DEFINE('_BIZ_POLL_NO_RESULT','No result returned');
DEFINE('_BIZ_POLL_SEARCH','Search poll');
DEFINE('_BIZ_POLL_CHOICE','Poll choice');
DEFINE('_BIZ_POLL_CHOICES','Poll choices');
DEFINE('_BIZ_POLL_QCU','Question with one answer');
DEFINE('_BIZ_POLL_QCM','Question with multiple answers');
DEFINE('_BIZ_POLL_SEARCH_MSG_RESULT','Result for your search: %s poll%s');
DEFINE('_BIZ_VOTE','Vote');
DEFINE('_BIZ_POLL_QUESTION','Poll question');
DEFINE('_BIZ_POLL_QUESTION_TEXT','Question');
DEFINE('_BIZ_SINGLE_CHOICE','Single choice');
DEFINE('_BIZ_MULTIPLE_CHOICE','Multiple choice');

// Contribution
DEFINE('_BIZ_CONTRIBUTION','Comment');
DEFINE('_BIZ_CONTRIBUTIONS','Comments');
DEFINE('_BIZ_CONTRIBUTION_REFERENT','Referent');
DEFINE('_BIZ_CONTRIBUTION_REFERENT_CLASS','Referent Class');
DEFINE('_BIZ_CONTRIBUTION_EMAIL','I want to be alerted by email when there is an answer to this comment');
DEFINE('_BIZ_CONTRIBUTION_NO_RESULT','No result returned');
DEFINE('_BIZ_CONTRIBUTION_SEARCH','Search comments');
DEFINE('_BIZ_CONTRIBUTION_SEARCH_MSG_RESULT','Result for your search: %s comment%s');
DEFINE('_BIZ_PARENT_CONTRIBUTION','Parent comment');
DEFINE('_BIZ_CREATE_CONTRIBUTION_CONTRIBUTION','Create a comment in response to this one');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_CONTRIBUTION','Choose a parent comment for this one');
DEFINE('_BIZ_CONTRIBUTION_SELECT_CONTRIBUTION_LIST','Select a parent comment in the list below');
DEFINE('_BIZ_CONTRIBUTION_STATE','Evaluation');
DEFINE('_BIZ_CONTRIBUTION_NONE','Don\'t invite comments');
DEFINE('_BIZ_CONTRIBUTION_CLOSED','Locked');
DEFINE('_BIZ_CONTRIBUTION_OPEN','Invite comments');
DEFINE('_BIZ_CONTRIBUTION_PLEASE_ENTER_YOUR','Please enter your');
DEFINE('_BIZ_NO_CONTRIBUTION','No comments');
DEFINE('_BIZ_FOLLOW_UPS','Follow ups');
DEFINE('_BIZ_CONTRIBUTION_NO_REFERENT','No referent');
DEFINE('_BIZ_CONTRIBUTION_ORPHAN','This comment is orphaned');

// Shared media
DEFINE('_BIZ_SHARED_MEDIA','Media');

// Photo
DEFINE('_BIZ_CHANGE_PHOTO','Change photo');
DEFINE('_BIZ_PHOTO','Photo');
DEFINE('_BIZ_PHOTOS','Photos');
DEFINE('_BIZ_PHOTOS_NO_RESULT','No result returned');
DEFINE('_BIZ_PHOTOS_SEARCH','Search Photo');
DEFINE('_BIZ_PHOTOS_SEARCH_MSG_RESULT','Result for your search: %s photo%s');
DEFINE('_BIZ_SITE_TITLE_ALREADY_USED','This title is already in use');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_PHOTO','Choose a parent photo for this comment');
DEFINE('_BIZ_CONTRIBUTION_SELECT_PHOTO_LIST','Select a parent photo in the list below');
DEFINE('_BIZ_PHOTOS_SEE_ORIGINAL','See original photo');
DEFINE('_BIZ_MANAGE_CROPPING','Ratios and cropping');
DEFINE('_BIZ_DIMENSIONS','Dimensions');
DEFINE('_BIZ_SELECT_RATIO','Select a ratio');
DEFINE('_BIZ_RATIO_THUMB_SQUARE','Thumbnail: Square');
DEFINE('_BIZ_RATIO_THUMB_HORIZONTAL','Thumbnail: Horizontal');

// Video
DEFINE('_BIZ_CHANGE_VIDEO','Change video');
DEFINE('_BIZ_VIDEO','Video');
DEFINE('_BIZ_VIDEOS','Videos');
DEFINE('_BIZ_VIDEOS_NO_RESULT','No result returned');
DEFINE('_BIZ_VIDEOS_SEARCH','Search video');
DEFINE('_BIZ_VIDEOS_SEARCH_MSG_RESULT','Result for your search: %s video%s');
DEFINE('_BIZ_CONTRIBUTION_CHOOSE_VIDEO','Choose a parent video for this comment');
DEFINE('_BIZ_CONTRIBUTION_SELECT_VIDEO_LIST','Select a parent video in the list below');
DEFINE('_BIZ_VIDEO_URL','Link');
DEFINE('_BIZ_VIDEO_EMBED','Tag EMBED');

// Site
DEFINE('_BIZ_SITE','Web site');
DEFINE('_BIZ_SITES','Sites');
DEFINE('_BIZ_SITE_NO_RESULT','No result returned');
DEFINE('_BIZ_SITE_CANT_DELETE_CURRENT','It is not allowed to delete the current site.');
DEFINE('_BIZ_SITE_SEARCH','Search site');
DEFINE('_BIZ_SITE_SEARCH_MSG_RESULT','Result for your search: %s site%s');
DEFINE('_BIZ_ADD_NEW_LINK','Add new');

// Menu and recipe
DEFINE('_BIZ_ADDITIONAL_INFO','Additional information');
DEFINE('_BIZ_ADDITIONAL_INFO_TITLE','Additional information headline');
DEFINE('_BIZ_ADD_INGREDIENT','Add an ingredient');
DEFINE('_BIZ_ADD_NUTRITIONAL','Add nutritional');
DEFINE('_BIZ_ADD_RECIPE_MSG','Add a recipe');
DEFINE('_BIZ_COOKTIME','Cooking time');
DEFINE('_BIZ_FULL_TEXT_INGREDIENTS','Ingredients (full text)');
DEFINE('_BIZ_GENERATE_FULL_TEXT','Generate full text');
DEFINE('_BIZ_GENERATE_LIST','Generate ingredient list');
DEFINE('_BIZ_INGREDIENTS','Ingredients');
DEFINE('_BIZ_MENU','Menu');
DEFINE('_BIZ_MENU_NO_RESULT','No result returned');
DEFINE('_BIZ_MENU_RECIPES_MSG','Recipes in this menu');
DEFINE('_BIZ_MENU_SEARCH','Search menu');
DEFINE('_BIZ_MENU_SEARCH_MSG_RESULT','Result for your search: %s menu%s');
DEFINE('_BIZ_NUTRITIONALS','Nutritional');
DEFINE('_BIZ_PORTION','Portion');
DEFINE('_BIZ_PREPMETHOD','Preparation method');
DEFINE('_BIZ_PREPTIME','Preparation time');
DEFINE('_BIZ_QUANTITY','Quantity');
DEFINE('_BIZ_RECIPE','Recipe');
DEFINE('_BIZ_RECIPE_NO_RESULT','No result returned');
DEFINE('_BIZ_RECIPE_SEARCH','Search recipe');
DEFINE('_BIZ_RECIPE_SEARCH_MSG_RESULT','Result for your search: %s recipe%s');

//Upload
DEFINE('_BIZ_UPLOAD_ITEM','Upload Item');
DEFINE('_BIZ_UPLOAD_PHOTO','Upload Photo');
DEFINE('_BIZ_UPLOAD_VIDEO','Upload a video');
DEFINE('_BIZ_UPLOAD_FOLDER_SOURCE','Source Folder');
DEFINE('_BIZ_UPLOAD_FOLDER_DESTINATION','Destination Folder');
DEFINE('_BIZ_UPLOAD_XML_FILENAME_PREFIX','XML Filename Prefix');
DEFINE('_BIZ_UPLOAD_NEW_FOLDER','New Folder');
DEFINE('_BIZ_CREATE_FOLDER','Create folder');
DEFINE('_BIZ_FILE_TO_UPLOAD','File to Upload');
DEFINE('_BIZ_DESTINATION','Destination');
DEFINE('_BIZ_IMAGE_FILE','Image file');
DEFINE('_BIZ_IMAGE_NAME','Name of image');
DEFINE('_BIZ_UPLOAD_THEPHOTO','Upload the photo...');
DEFINE('_BIZ_PHOTO_EXISTS','The specified photo already exists.');
DEFINE('_BIZ_VIDEO_FILE','Video file');
DEFINE('_BIZ_VIDEO_NAME','Name of video');
DEFINE('_BIZ_UPLOAD_THEVIDEO','Upload the video...');
DEFINE('_BIZ_VIDEO_EXISTS','The specified video already exists.');

// Links
DEFINE('_BIZ_LINKS','Links');
DEFINE('_BIZ_LINKS_ADD','Add a new link');
DEFINE('_BIZ_LINKS_CHOOSE_ELEMENT','Choose an element for the link');
DEFINE('_BIZ_LINKS_MODIF','Modify content link');
DEFINE('_BIZ_LINKS_SELECT_ELEMENT_LIST','Select an element in the list');
DEFINE('_BIZ_LINKING_TO_ITSELF','You cannot link an object to itself.');

// Slideshow
DEFINE('_BIZ_SLIDESHOW','Slideshow');
DEFINE('_BIZ_SLIDESHOWS','Slideshows');
DEFINE('_BIZ_ADD_PHOTOS_MSG','Add photos');
DEFINE('_BIZ_SLIDESHOW_NO_RESULT','No result returned');
DEFINE('_BIZ_SLIDESHOW_SEARCH','Search a slideshow');
DEFINE('_BIZ_SLIDESHOW_SEARCH_MSG_RESULT','Result for your search: %s slideshow%s');
DEFINE('_BIZ_SLIDESHOW_PHOTOS_MSG','Add photos to slideshow');

// Newsletter
DEFINE('_BIZ_NEWSLETTER','Newsletter');
DEFINE('_BIZ_NEWSLETTERS','Newsletters');
DEFINE('_BIZ_NEWSLETTER_NO_RESULT','No result returned');
DEFINE('_BIZ_NEWSLETTER_SEARCH','Search a newletter');
DEFINE('_BIZ_NEWSLETTER_SEARCH_MSG_RESULT','Result for your search: %s newsletter%s');
DEFINE('_BIZ_NEWSLETTER_MSG_DEFAULT_PROPERTIES','Default properties');
DEFINE('_BIZ_SENDER','Sender');
DEFINE('_BIZ_REPLY_TO','Reply to');
DEFINE('_BIZ_TITLE_MSG','Message title');
DEFINE('_BIZ_NEWSLETTER_MSG_TEMPLATE_USED','Newsletter\'s templates');
DEFINE('_BIZ_HTML_TEMPLATE','HTML');
DEFINE('_BIZ_TEXT_TEMPLATE','Plain text');
DEFINE('_BIZ_NEWSLETTER_SUBSCRIBERS','Subscribers');
DEFINE('_BIZ_NEWSLETTER_EXISTING_SUBSCRIBERS','Existing subscribers');
DEFINE('_BIZ_NEWSLETTER_ADD_SUBSCRIBER','Add a subscriber');
DEFINE('_BIZ_NEWSLETTER_FORCED_CONTENT','Forced content');
DEFINE('_BIZ_NEWSLETTER_CONFIGURATION','Configuration');
DEFINE('_BIZ_NEWLETTERS_TEMPLATES','Email templates');

// Webuser
DEFINE('_BIZ_WEBUSER','Web user');
DEFINE('_BIZ_WEBUSER_ADD','Add Web user');
DEFINE('_BIZ_WEBUSERS','Web users','Web user');
DEFINE('_BIZ_WEBUSER_CLEAR','Clear web user');
DEFINE('_BIZ_WEBUSER_CONTRIBUTIONS','Web user\'s comments');
DEFINE('_BIZ_WEBUSER_ID','Web user ID');
DEFINE('_BIZ_WEBUSER_SEARCH','Search web users');
DEFINE('_BIZ_WEBUSER_SEARCH_MSG_RESULT','Result for your search: %s webuser%s');
DEFINE('_BIZ_WEBUSER_NO_RESULT','No result returned');
DEFINE('_BIZ_FIRSTNAME','First name');
DEFINE('_BIZ_LASTNAME','Last name');
DEFINE('_BIZ_ADDRESS','Address');
DEFINE('_BIZ_ADDRESS1','Address (1)');
DEFINE('_BIZ_ADDRESS2','Address (2)');
DEFINE('_BIZ_ADDRESS3','Address (3)');
DEFINE('_BIZ_PHONE_NUMBER','Phone number');
DEFINE('_BIZ_POSTALCODE','Postal Code');
DEFINE('_BIZ_CITY','City');
DEFINE('_BIZ_COUNTRY','Country');
DEFINE('_BIZ_MANAGE_USER','Manage user');
DEFINE('_BIZ_NBERRORS','Number of errors');
DEFINE('_BIZ_NBPERIODS','Number of periods');
DEFINE('_BIZ_BANNEDDATE','Ban date');
DEFINE('_BIZ_BEGINPERIODDATE','Begin date');
DEFINE('_BIZ_OLDEMAILS','Old emails');
DEFINE('_BIZ_DEMOGRAPHICS','Demographics');
DEFINE('_BIZ_BANNED','Banned');
DEFINE('_BIZ_WAITING','Suspended');
DEFINE('_BIZ_VALID','Valid');
DEFINE('_BIZ_LAST_LOGIN','Last login');

// Collection
DEFINE('_BIZ_COLLECTION','Collection');
DEFINE('_BIZ_COLLECTIONS','Collections');
DEFINE('_BIZ_COLLECTION_SEARCH','Search collections');
DEFINE('_BIZ_COLLECTION_SEARCH_MSG_RESULT','Result for your search: %s collection%s');
DEFINE('_BIZ_COLLECTION_NO_RESULT','No result returned');
DEFINE('_BIZ_ADD_COLLECTION_ELEMENT_MSG','Add a link');
DEFINE('_BIZ_COLLECTION_LINKS_MSG','Choose links');
DEFINE('_BIZ_NEW_COLLECTION','New collection');
DEFINE('_BIZ_CHOOSE_OR_CREATE_COLLECTION','Choose or create a collection');

// Questionnaire
DEFINE('_BIZ_QUESTIONNAIRE','Questionnaire');
DEFINE('_BIZ_QUESTIONNAIRE_SEARCH','Search questionnaires');
DEFINE('_BIZ_QUESTIONNAIRE_SEARCH_MSG_RESULT','Result for your search %s questionnaire%s');
DEFINE('_BIZ_QUESTIONNAIRE_NO_RESULT','No result returned');
DEFINE('_BIZ_ADD_QUESTIONNAIRE_ELEMENT_MSG','Add a link');
DEFINE('_BIZ_QUESTIONNAIRE_ADD_QUESTION','Add questions');
DEFINE('_BIZ_QUESTIONNAIRE_LINKS_MSG','Choose links');
DEFINE('_BIZ_QUESTIONNAIRE_TYPE','Questionnaire type');
DEFINE('_BIZ_QUESTIONNAIRE_CHOICES','Questions');
DEFINE('_BIZ_QUIZ','Quiz');
DEFINE('_BIZ_SUMMARY','Summary');
DEFINE('_BIZ_QUESTIONS','Questions');
DEFINE('_BIZ_QUESTION','Question');
DEFINE('_BIZ_ANSWER','Answer');
DEFINE('_BIZ_ANSWER_MODE','Answer mode');
DEFINE('_BIZ_ANSWER_WEIGHT','Answer weight');
DEFINE('_BIZ_QUESTION_MULTIPLE','Multiple answers');
DEFINE('_BIZ_QUESTION_SINGLE','Unique answer');
DEFINE('_BIZ_QUESTION_FREEFORM','Free form answer');
DEFINE('_BIZ_SHOWHIDE_ANSWER','Show/hide answer list');

// NServer Msg
DEFINE('_BIZ_MSG_RESET','Reset successful');
DEFINE('_BIZ_MSG_INSERT','Insert successful');
DEFINE('_BIZ_MSG_NEW','Create a new item');
DEFINE('_BIZ_MSG_UPDATE','Update successful');
DEFINE('_BIZ_MSG_EDIT','Edit an item');
DEFINE('_BIZ_MSG_LOADING_WAIT','Loading please wait...');
DEFINE('_BIZ_MSG_INSERT_TEXT','Enter a value here');
DEFINE('_BIZ_CONFIRM_RESET_MSG','Do you want to reset the list?');
DEFINE('_BIZ_CONFIRM_RESET_ALL_MSG','Do you want to reset all the lists?');

// Subscriptions
DEFINE('_BIZ_SUBSCRIPTION_ADD','Add a subscription');
DEFINE('_BIZ_SUBSCRIPTION_START','Subscription Start');
DEFINE('_BIZ_SUBSCRIPTION_END','Subscription End');
DEFINE('_BIZ_SUBSCRIPTION_TYPE','Subscription Type');
DEFINE('_BIZ_SUBSCRIPTION_TYPE_BIZ','Tie Subscription to a Biz Object');
DEFINE('_BIZ_SUBSCRIPTION_TYPE_CUSTOM','Tie subscription to a custom value');
DEFINE('_BIZ_SUBSCRIPTION_BUTTON_ADD','Add Subscription');
DEFINE('_BIZ_SUBSCRIPTION_VALUE','Subscription Value');
DEFINE('_BIZ_CUSTOMIZED_SUBSCRIPTION','Custom subscription');

// Search
DEFINE('_BIZ_RECORDS_FOUND','records found');
DEFINE('_BIZ_RECORDS_PAGE','records per page');
DEFINE('_BIZ_SECONDS','seconds');
DEFINE('_BIZ_SHOWING','Showing');
DEFINE('_BIZ_SIMPLESEARCH_TITLE','Search for a bizclass to subscribe too');
DEFINE('_BIZ_FIXED_QUERY','Fixed query');
DEFINE('_BIZ_MANAGEMENT_BY_DATE','Management by date');
DEFINE('_BIZ_SPECIFY_QUERY','Specify query');

/**
 * Specific (generated web site dependant) language resources
 *
 * English version
 */

/* Home */
DEFINE('_WEB_SITE_FULLTITLE','N<span class="green">C</span>M<span class="gray">DEMO</span>');
DEFINE('_WEB_SITENAME','Nstein - Demo Center');
DEFINE('_WEB_SITETITLE','WCM DEMO');

/* Blog */
DEFINE('_WEB_BLOG_TITLE','WCM Demo Blog - Nstein');

/* Forums */

/* Gallery */
DEFINE('_WEB_GALLERY_TITLE','WCM Demo Gallery - Nstein');

/* general */
DEFINE('_WEB_ALL_FIELDS','All fields must be filled');
DEFINE('_WEB_AUTHOR','Author');
DEFINE('_WEB_BACK_SITE','Back to site');
DEFINE('_WEB_BIGGER','Bigger');
DEFINE('_WEB_BY','By');
DEFINE('_WEB_CATEGORIES','Categories');
DEFINE('_WEB_CHANNEL','Channel');
DEFINE('_WEB_CODE_IS_INCORRECT','The code is incorrect');
DEFINE('_WEB_COMPANY','Nstein Technologies');
DEFINE('_WEB_CONCEPTS','Concepts');
DEFINE('_WEB_COPYRIGHT','All rights reserved');
DEFINE('_WEB_DATE','Date');
DEFINE('_WEB_EMAIL','Email');
DEFINE('_WEB_ENTER_CODE','Enter code');
DEFINE('_WEB_DISCUSS','Discuss');
DEFINE('_WEB_FIND','Search');
DEFINE('_WEB_FILE_UNDER','File under');
DEFINE('_WEB_FULLTEXT','Full text');
DEFINE('_WEB_IN','in');
DEFINE('_WEB_KEYWORDS','Keywords');
DEFINE('_WEB_LATEST','Latest');
DEFINE('_WEB_LOCATIONS','Locations');
DEFINE('_WEB_MENU','Menu');
DEFINE('_WEB_METAS','Metas');
DEFINE('_WEB_MORE_ARTICLES_FROM','More articles from ');
DEFINE('_WEB_MOREINFO_ABOUT','More info about this?');
DEFINE('_WEB_MOST_DISCUSSED','Most discussed');
DEFINE('_WEB_MOST_POPULAR','Most popular');
DEFINE('_WEB_NAME','Name');
DEFINE('_WEB_NICKNAME','Nickname');
DEFINE('_WEB_ON','on');
DEFINE('_WEB_ORGANIZATIONS','Organizations');
DEFINE('_WEB_PAGE','Page');
DEFINE('_WEB_PEOPLE','People');
DEFINE('_WEB_PRINT','Print');
DEFINE('_WEB_POPULAR_KEYWORDS','Popular keywords');
DEFINE('_WEB_POSTED_AT','Posted at');
DEFINE('_WEB_POWERED_BY','Powered by Nstein\'s WCM');
DEFINE('_WEB_PUBLICATION_DATE','Publication Date');
DEFINE('_WEB_RELATED_CONTENT','Related content');
DEFINE('_WEB_RELATED','Related');
DEFINE('_WEB_REPLY','Reply');
DEFINE('_WEB_SAID','Said');
DEFINE('_WEB_SEE','See');
DEFINE('_WEB_SEE_ALSO','See also');
DEFINE('_WEB_SEND','Send');
DEFINE('_WEB_SITE_FEATURES','Site\'s features');
DEFINE('_WEB_SITE_VALID','Valid');
DEFINE('_WEB_SMALLER','Smaller');
DEFINE('_WEB_SUBMIT','Submit');
DEFINE('_WEB_TITLE','Title');

// Modules : editorial/article (tabs)
DEFINE('_BIZ_OVERVIEW','Overview');
DEFINE('_BIZ_DESIGN','Design');
DEFINE('_BIZ_COMMENTS','Commenting');
DEFINE('_BIZ_NEW_ARTICLE','New article');

// Modules : shared/versioning
DEFINE('_BIZ_VERSIONS','Versions');
DEFINE('_BIZ_VERSION','Version');
DEFINE('_BIZ_REVISION','Revision');
DEFINE('_BIZ_VERSION_HISTORY','Version history');
DEFINE('_BIZ_ADD_NEW_VERSION','Save and create new version');
DEFINE('_BIZ_VERSION_COMMENT','Comment');
DEFINE('_BIZ_VERSION_RESTORE','Restore');
DEFINE('_BIZ_VERSION_ROLLBACK','Rollback');
DEFINE('_BIZ_NO_VERSION_STORED','no version stored');
DEFINE('_BIZ_VERSION_ADDED','New version successfully created');
DEFINE('_BIZ_VERSION_ADDED_FAILED','Creation of new version failed');
DEFINE('_BIZ_VERSION_RESTORED','Version successfully restored');
DEFINE('_BIZ_VERSION_RESTORE_FAILED','Restoring version failed');
DEFINE('_BIZ_VERSION_ROLLEDBACK','Version successfully rolled-back');
DEFINE('_BIZ_VERSION_ROLLBACK_FAILED','Rolling-back version failed');
DEFINE('_BIZ_VERSION_CREATED_ON_SAVE','Version created automatically on save');

// Modules : editorial/article/Properties
DEFINE('_BIZ_OTHER_SOURCE','Other source');
DEFINE('_BIZ_OTHER_SOURCE_NAME','Source name');

// Modules : editorial/article/Referencing
DEFINE('_BIZ_REFERENCING','Referencing');
DEFINE('_BIZ_PERMALINKS','Permalinks');
DEFINE('_BIZ_DEFAULT_PERMALINK','Default (Friendly URL)');
DEFINE('_BIZ_ADDITIONAL_PERMALINKS','Additional');
DEFINE('_BIZ_OUTBOUND_LINKS','Related content');
DEFINE('_BIZ_OUTBOUND_LINKS_INTERNAL','Internal');
DEFINE('_BIZ_OUTBOUND_LINKS_EXTERNAL','External');
DEFINE('_BIZ_INBOUND_LINKS','Inbounds');

// Modules : editorial/article/categorization
DEFINE('_BIZ_CATEGORIZATION','Categorization');
DEFINE('_BIZ_CATEGORIZATION_IPTC','IPTC');
DEFINE('_BIZ_CATEGORIZATION_TAGS','Tagging');

// Module : editorial/article/footprint

DEFINE('_BIZ_TME','Semantic footprint');
DEFINE('_BIZ_TME_ENTITIES','Entities');
DEFINE('_BIZ_TME_ENTITITES_GL','Places');
DEFINE('_BIZ_TME_ENTITITES_ON','Organizations');
DEFINE('_BIZ_TME_ENTITITES_PN','People');
DEFINE('_BIZ_TME_CONCEPTS','Concepts');
DEFINE('_BIZ_TME_CONCEPTS_COMPLEX','Complex');
DEFINE('_BIZ_TME_CONCEPTS_SIMPLE','Simple');
DEFINE('_BIZ_TME_SENTIMENT','Sentiment analysis');
DEFINE('_BIZ_TME_SENTIMENT_TONE','Tone');
DEFINE('_BIZ_TME_SENTIMENT_TONE_POSITIVE','Positive');
DEFINE('_BIZ_TME_SENTIMENT_TONE_NEUTRAL','Neutral');
DEFINE('_BIZ_TME_SENTIMENT_TONE_NEGATIVE','Negative');
DEFINE('_BIZ_TME_SENTIMENT_SUBJECTIVITY','Subjectivity');
DEFINE('_BIZ_TME_SENTIMENT_SUBJECTIVITY_FACT','Fact');
DEFINE('_BIZ_TME_SENTIMENT_SUBJECTIVITY_OPINION','Opinion');
DEFINE('_BIZ_AD_SERVER','Advertising');
DEFINE('_BIZ_TME_PROCESSING_FAILED','TME processing failed');
DEFINE('_BIZ_UPDATING_SEMANTIC_DATA_FOR','Updating semantic data for ');
DEFINE('_BIZ_TOTAL_COMPUT_TIME','Total computation time: ');

// Module : editorial/shared/comments
DEFINE('_BIZ_CONTRIBUTION_COMMENTS','Comments made on this object');
DEFINE('_BIZ_LAST_COMMENT_TIME','Last comment made at');

/* Semantic data */
DEFINE('_WEB_CATEGORIES_AVAILABLE','Available Categories');
DEFINE('_WEB_CONCEPT','Concept');
DEFINE('_WEB_GEOGRAPHICAL_LOCATION','Geographic Location');
DEFINE('_WEB_NSERVER','TME');
DEFINE('_WEB_PERSON_NAME','Person name');
DEFINE('_WEB_PLACES','Places');
DEFINE('_WEB_ORGANIZATION','Organisation');
DEFINE('_WEB_SEMANTIC_CLOUD','Semantic cloud');
DEFINE('_WEB_SIMILAR_ITEMS','Similar Items');

/* Comments and Contributions */
DEFINE('_WEB_ADD','Add');
DEFINE('_WEB_BEFIRST_COMMENT','Be the first to comment on ');
DEFINE('_WEB_COMMENT','Comment');
DEFINE('_WEB_COMMENTS','Comments ');
DEFINE('_WEB_COMMENT_NOW','Comment Now!');
DEFINE('_WEB_COMMENTS_ON','Comments on ');
DEFINE('_WEB_COMMENT_ON','Comment on ');
DEFINE('_WEB_CONTRIBUTION_PLEASE_ENTER_YOUR','Please enter your');
DEFINE('_WEB_LAST_COMMENTS','Last comments');
DEFINE('_WEB_START_NEW_TOPIC','Start new topic');
DEFINE('_WEB_POST_COMMENT','Post your comment *');

/* rating, most viewed & most popular */
DEFINE('_WEB_MOST_POP_PHOTOS','Most popular photos');
DEFINE('_WEB_MOSTVIEWED_PHOTOS','Most viewed photos');
DEFINE('_WEB_MOSTVIEWED_STORIES','Most viewed stories');
DEFINE('_WEB_TOPSTORIES','Top Stories');
DEFINE('_WEB_RATE','Rate ');
DEFINE('_WEB_RATE_ARTICLE',' Rate this article');
DEFINE('_WEB_RATE_GALLERY',' Rate this gallery');
DEFINE('_WEB_RATE_PHOTO',' Rate this photo');
DEFINE('_WEB_RATE_RECIPE',' Rate this recipe');
DEFINE('_WEB_RATE_THIS',' Rate this!');
DEFINE('_WEB_RATING','Rating');
DEFINE('_WEB_READMORE','Read more');
DEFINE('_WEB_READMORE_ON','Read more on');
DEFINE('_WEB_VIEWED','Viewed');

/* search */
DEFINE('_WEB_NSTEIN_SEARCH','Nsteindaily search');
DEFINE('_WEB_REFINE_SEARCH','Refine search');
DEFINE('_BIZ_REFINE_BUTTON','Refine');
DEFINE('_WEB_SEARCH','Search');

/* object specific */
/* Articles */

/* Forums */
DEFINE('_WEB_FORUMS','Forums');
DEFINE('_WEB_FORUMS_SUBTITLE','Forums');
DEFINE('_WEB_FORUMS_LAST_FOUR_THREADS','Lastest Threads');
DEFINE('_WEB_FORUMS_LAST_TEN_THREADS','Last 10 threads');
DEFINE('_WEB_FORUMS_FULL_THREAD','View full thread');
DEFINE('_WEB_FORUMS_LAST_POSTS','Last posts');
DEFINE('_WEB_FORUMS_ADD_POST','Add post');
DEFINE('_WEB_FORUMS_SUBMIT_POST','Submit post');
DEFINE('_WEB_FORUMS_POST_NOW','Post now!');

/* News article */
DEFINE('_WEB_VIEWCOMPLETE_NEWSITEM','View complete news item');

/* Slideshows */
DEFINE('_WEB_IN_PICTURES','In pictures');
DEFINE('_WEB_VIEW_GALLERY','View gallery');

/* Photos */
DEFINE('_BIZ_PICTURE','Picture');
DEFINE('_WEB_SEE_PHOTO','See this photo');
DEFINE('_BIZ_PHOTO_ALLOWED_FILENAME_EXTENSIONS','Allowed file name extensions for photos:');

/* polls */
DEFINE('_WEB_LATEST_POLLS','Latest Polls');
DEFINE('_WEB_LETUS_KNOW','Let us know!');
DEFINE('_WEB_THANKS_VOTING','Thanks for voting!');
DEFINE('_WEB_VOTE','Vote');

/* Publicity & Advertisement */
DEFINE('_WEB_SAMPLE_PORTFOLIO','Sample Work Portfolio');

/* Related */
DEFINE('_WEB_RELATED_ARTICLE','Related');
DEFINE('_WEB_RELATED_CHANNEL','Related section');
DEFINE('_WEB_RELATED_COLLECTION','Related collection');
DEFINE('_WEB_RELATED_CONTRIBUTION','Related comment');
DEFINE('_WEB_RELATED_FORUM','Related forum');
DEFINE('_WEB_RELATED_ITEM','Related item');
DEFINE('_WEB_RELATED_NEWSITEM','Related newsitem');
DEFINE('_WEB_RELATED_NEWSLETTER','Related newsletter');
DEFINE('_WEB_RELATED_PHOTO','Related photo');
DEFINE('_WEB_RELATED_POLL','Related poll');
DEFINE('_WEB_RELATED_SITE','Related site');
DEFINE('_WEB_RELATED_SLIDESHOW','Related slideshow');
DEFINE('_WEB_SIMILAR_RESULTS','Similar results');

/* Request */
DEFINE('_BIZ_CHANNEL_REQUEST_NAME','Request Name');
DEFINE('_BIZ_CHANNEL_REQUEST_CLASS','Request Class');
DEFINE('_BIZ_CHANNEL_REQUEST_WHERE','Where');
DEFINE('_BIZ_CHANNEL_REQUEST_ORDERBY','Order By');
DEFINE('_BIZ_OPERATOR','');
DEFINE('_BIZ_FIELDS','Fields');
DEFINE('_BIZ_COMPARE','Operator');
DEFINE('_BIZ_ORDER','Order Type');
DEFINE('_BIZ_NEW_CHANNEL_REQUEST_NAME','Please change request name for clone');
DEFINE('_BIZ_CHECK_REQUEST','Check the request validity');
DEFINE('_BIZ_CHANNEL_REQUEST_VALID','The request is valid');
DEFINE('_BIZ_CHANNEL_REQUEST_NOT_VALID','The request is not valid');

/* Import */
DEFINE('_BIZ_DEFAULT_PARAMETERS','Leave the parameters empty if you wish to use their default values');
DEFINE('_BIZ_IMPORT_TYPE','Import type');
DEFINE('_BIZ_IMPORT_PARAMETERS','Import parameters');
DEFINE('_ROOT_FOLDER','Folder to import');
DEFINE('_XSL_FOLDER','Folder with XSLs');
DEFINE('_PHOTO_FOLDER','Folder with photos to import');
DEFINE('_PHOTO_URL','Path to access photos after import');
DEFINE('_BIZ_IMPORT_AFP','NewsML');
DEFINE('_BIZ_IMPORT_NITF','NITF files');
DEFINE('_BIZ_IMPORT_PHOTOS','JPEG photos (With embedded XMP metadata)');
DEFINE('_BIZ_IMPORT_BIZOBJECT','Nstein native XML');
DEFINE('_BIZ_IMPORT_PRINT','Print files');
DEFINE('_BIZ_BEGIN_IMPORT','Starting import with %s as root folder');
DEFINE('_BIZ_END_IMPORT','End of import with %s as root folder');
DEFINE('_BIZ_READ_FOLDER','Reading folder:');
DEFINE('_BIZ_PROCESS_FOLDER','Processing folder:');
DEFINE('_BIZ_PROCESS_FILE','Processing file:');
DEFINE('_BIZ_INVALID_FILE','The file %s%s is invalid');
DEFINE('_BIZ_XSL_NOT_FOUND','The XSL does not exist');
DEFINE('_BIZ_XML_INVALID','The file %s is not a valid XML');
DEFINE('_BIZ_FILE_IMPORT_INCORRECT','File "%s" did not import correctly ::');
DEFINE('_BIZ_USE_XSL','Using XSL: ');
DEFINE('_BIZ_ROOT_FOLDER','Root folder');
DEFINE('_BIZ_TRANSFORM_IMPORT_ARTICLES_PHOTOS','Transform and import articles and photos');
DEFINE('_BIZ_AFP_IMPORT_RESULT','AFP article import results');
DEFINE('_BIZ_NITF_IMPORT_RESULT','NITF article import results');
DEFINE('_BIZ_PHOTO_IMPORT_RESULT','Photo import results');
DEFINE('_BIZ_BIZOBJECT_IMPORT_RESULT','BizObjects import results');
DEFINE('_BIZ_PRINT_IMPORT_RESULT','Print import results');
DEFINE('_BIZ_IMPORT_FROM_FILES','Import from files');
DEFINE('_BIZ_IMPORT_SOURCE_FORMAT','Source format');
DEFINE('_BIZ_IMPORT_FILE_LOCATION','File(s) location');
DEFINE('_BIZ_IMPORT_SOURCE_FOLDER','Source folder');
DEFINE('_BIZ_IMPORT_TRANSFORMATION_SETTINGS','Transformation settings');
DEFINE('_BIZ_IMPORT_XSL_TEMPLATE_LOCATION','XSL templates location');
DEFINE('_BIZ_IMPORT_MEDIA_SETTINGS','Media settings');
DEFINE('_BIZ_IMPORT_EMBEDDED_PHOTOS','Embedded photos');
DEFINE('_BIZ_IMPORT_DESTINATION_FOLDER','Destination folder');
DEFINE('_BIZ_IMPORT_START','Start content importation');
DEFINE('_BIZ_IMPORT_SETTINGS','Import settings');
DEFINE('_BIZ_IMPORT_JOB_STATUS','Import status');
DEFINE('_BIZ_IMPORT_DETAILED','For detailed information, consult');
DEFINE('_BIZ_RESTART','Restart');
DEFINE('_BIZ_IMPORT_CANCELED','Import canceled by user');
DEFINE('_BIZ_IMPORT_NO_DESTINATION_DIRECTORY','Destination directory does not exist and could not be created');
DEFINE('_BIZ_IMPORT_NO_DESTINATION_DIRECTORY_CREATED','Destination directory does not exist, created it');
DEFINE('_BIZ_IMPORT_JPEG','Import JPEG photos using embedded XMP metadata');
DEFINE('_BIZ_IMPORT_DAM','Import from DAM webservice');
DEFINE('_BIZ_IMPORT_NEWSML','Import from a NewsXML source file');
DEFINE('_BIZ_IMPORT_NITF','Import from a NITF source file');
DEFINE('_BIZ_IMPORT_NSTEIN','Import from an NStein native XML source file');
DEFINE('_BIZ_IMPORT_CRITERIA','Import criteria');
DEFINE('_BIZ_IMPORT_FROM_WHEN','Import from when');
DEFINE('_BIZ_IMPORT_FROM_WHERE','Import from where');
DEFINE('_BIZ_DAM_WEB_SERVICE_URL','DAM Webservice URL');
DEFINE('_BIZ_IMPORT_DAME_REPOSITORY','DAM Media Repository Webservices');
DEFINE('_BIZ_USERID','User ID');
DEFINE('_BIZ_MISCELLANEOUS','Miscellaneous');
DEFINE('_BIZ_XSL_FOLDER','XSL folder');
DEFINE('_BIZ_CLASSES','Classes');
DEFINE('_BIZ_LAST_THREE_DAYS','Last three days');
DEFINE('_BIZ_LAST_SEVEN_DAYS','Last seven days');
DEFINE('_BIZ_LAST_MONTH','Last month');
DEFINE('_BIZ_LAST_IMPORT','Last import');
DEFINE('_BIZ_IMPORT_DAM_NEW_PROCESSOR','Using additional processor class');
DEFINE('_BIZ_IMPORT_DAM_PROCESSOR_FAILED','Special logic for class %s failed');
DEFINE('_BIZ_IMPORT_DAM_NO_MAPPING','Could not find mapping information for DAM Class');
DEFINE('_BIZ_IMPORT_DAM_INVALID_TOKEN','Invalid security token');

/* Import Article */
DEFINE('_BIZ_CREATE_ARTICLE','Création d\'article (code : %s)');
DEFINE('_BIZ_UPDATE_ARTICLE','Mise a jour d\'article #%s (code : %s)');

/* Import AFP */
DEFINE('_BIZ_NO_INDEX_FILE_FOR_CHANNEL','No index file found for channel ');
DEFINE('_BIZ_UPDATE_CHANNEL','Updating channel ');
DEFINE('_BIZ_NO_CHANNEL_FOR_FOLDER','No channel found for folder ');
DEFINE('_BIZ_CREATE_CHANNEL','Creating channel ');
DEFINE('_BIZ_INDEX_CHANNEL_UNCHANGED','Index of channel %s [%s] has not changed since last import (%s &gt; %s)');
DEFINE('_BIZ_INDEX_INVALID_XML','The index file %s/index.xml in not a valid XML file');
DEFINE('_BIZ_ARTICLE_XML_ERROR','Article XML error: ');
DEFINE('_BIZ_PROCESS_PHOTO_OF_ARTICLE','Processing photos of article #%s (code: %s)');
DEFINE('_BIZ_ASSOC_PHOTO_ARTICLE','Linking photo #%s to article #%s');
DEFINE('_BIZ_ASSOC_PHOTO_ARTICLE_DONE','Linked photo %s to article %s');
DEFINE('_BIZ_ORIGINAL_PHOTO_NOT_FOUND','Cannot find original picture of photo %s');
DEFINE('_BIZ_PROCESS_PHOTO','Processing photo ');
DEFINE('_BIZ_THUMBNAIL_NOT_FOUND','Cannot find thumbnail %s of picture %s');
DEFINE('_BIZ_CREATE_PHOTO_AFP','Creating photo (code: %s)');
DEFINE('_BIZ_UPDATE_PHOTO_AFP','Updating photo #%s (code: %s)');
DEFINE('_BIZ_CANNOT_COPY_PHOTO_AFP','Unable to copy picture from %s/%s to %s');
DEFINE('_BIZ_CANNOT_COPY_THUMBNAIL_AFP','Unable to copy thumbnail from %s/%s to %s');
DEFINE('_BIZ_NOINIT_PHOTO_XML','Could not init photo from XML: %s');

/* Import Bizobject */
DEFINE('_BIZ_CREATE_BIZOBJECT','Creating object %s (code: %s)');
DEFINE('_BIZ_UPDATE_BIZOBJECT','Updating object %s #%s (code: %s)');

/* Import Photos */
DEFINE('_BIZ_IPTC_NOT_FOUND','Cannot find IPTC APP13 metadata');
DEFINE('_BIZ_LEGEND_TITLE_NOT_FOUND','Title and caption fields not found');
DEFINE('_BIZ_KEYWORDS_NOT_FOUND','Keywords not found');
DEFINE('_BIZ_IMPORT_UPDATE_PHOTO','Updating photo ');
DEFINE('_BIZ_IMPORT_INSERT_PHOTO','Adding photo ');
DEFINE('_BIZ_IMPORT_PROCESS','Process files:');
DEFINE('_BIZ_IMPORT_PHOTO_FILE','Photo file:');
DEFINE('_BIZ_ADD_PHOTO_TO_SLIDESHOW','Adding photo to slideshow ');
DEFINE('_BIZ_NO_SLIDESHOW_FOR_PHOTO_KEYWORDS','Not slideshow having [%s] keywords (from photo %s)');
DEFINE('_BIZ_FOR_PHOTO','For the photo');
DEFINE('_BIZ_REJECT','Rejected');
DEFINE('_BIZ_INVALID_PICTURE','Invalid picture (not a picture?)');
DEFINE('_BIZ_IMPORT_PHOTO_NO_TITLE',' does not have a title.');
DEFINE('_BIZ_IMPORT_PHOTO_OK','Imported photo file:');

/* Print Information */
DEFINE('_BIZ_PUBLICATION','Publication');
DEFINE('_BIZ_ISSUE','Issue');
DEFINE('_BIZ_ADD_ISSUE','Add issue');
DEFINE('_BIZ_ISSUENUMBER','Issue number');
DEFINE('_BIZ_ISSUEDATE','Issue date');
DEFINE('_BIZ_PUBLICATIONNAME','Publication name');
DEFINE('_BIZ_PRINT_INFORMATIONS','Print information');
DEFINE('_BIZ_PAGE_NUMBER','Page number');
DEFINE('_BIZ_ISSUES','Issues');
DEFINE('_BIZ_NO_ISSUES','No issues');
DEFINE('_BIZ_NO_CHANNELS','No channels');
DEFINE('_BIZ_CREATE_PUBLICATION','Creating publication: %s');
DEFINE('_BIZ_UPDATE_PUBLICATION','Updating publication: %s');
DEFINE('_BIZ_CREATE_ISSUE','Creating issue: %s');
DEFINE('_BIZ_UPDATE_ISSUE','Updating issue: %s');

// Search
DEFINE('_BIZ_SEARCH_ADD_TO_SELECTED_BIN','Add to selected bin');
DEFINE('_BIZ_TOGGLE','Toggle');
DEFINE('_BIZ_MODIFIED_AT','Modified on');
DEFINE('_BIZ_ITEMS_FOUND_IN',' items found in ');
DEFINE('_BIZ_START_NEW_SEARCH','Start new search');
DEFINE('_BIZ_WITH_SELECTED_FILTERS','with selected filters');
DEFINE('_BIZ_FILTER_SELECTION','filter selection');
DEFINE('_BIZ_REFINE','Refine results using the filters');
DEFINE('_BIZ_SELECT_ALL','Select all');
DEFINE('_BIZ_CREATED_BY','Created by ');
DEFINE('_BIZ_ON',' on ');
DEFINE('_BIZ_MODIFIED_BY','modified by ');
DEFINE('_BIZ_CANNOT_LOAD_CONFIGURARTION_FILE','Impossible to load search_configuration.xml');
DEFINE('_BIZ_SEARCHES','Searches');
DEFINE('_BIZ_QUERY','Query');
DEFINE('_BIZ_NATIVE_QUERY','Native Query');
DEFINE('_BIZ_MY_SAVED_SEARCHES','My saved searches');
DEFINE('_BIZ_SEARCH_HISTORY','Search history');
DEFINE('_BIZ_REMOVE','Remove');
DEFINE('_BIZ_CURRENT_SEARCH','current search');
DEFINE('_BIZ_DASHBOARD','Add to dashboard ?');
DEFINE('_BIZ_CREATE','Create');
DEFINE('_BIZ_EMPTY_BIN','empty bin');
DEFINE('_BIZ_SELECTED_ITEMS','Selected Item(s)');
DEFINE('_BIZ_ADD_TO_SELECTED_BIN','Add to selected bin');
DEFINE('_BIZ_SELECTED_BIN','selected bin');
DEFINE('_BIZ_ALL_BINS','all bins');
DEFINE('_BIZ_CREATE_BIN','Create bin');
DEFINE('_BIZ_MANAGE','manage');
DEFINE('_BIZ_BIN','bin');
DEFINE('_BIZ_NO_DETAIL','---'); // Displayed in the search results when an attribute is undefined
DEFINE('_BIZ_GRID_VIEW','Grid view');
DEFINE('_BIZ_LIST_VIEW','List view');
DEFINE('_BIZ_TOP_CONCEPTS','Top concepts');
DEFINE('_BIZ_TOP_ENTITIES','Top entities');
DEFINE('_BIZ_TOP_EDITORIAL_TAGS','Top editorial tags');
DEFINE('_BIZ_TOP_SUBJECTS','Subjects');
DEFINE('_BIZ_ASSET_TYPES','Asset types');
DEFINE('_BIZ_DATE_RANGES','Date ranges');
DEFINE('_BIZ_SOURCES','Sources');
DEFINE('_BIZ_FILTERS','Filters');
DEFINE('_BIZ_SHARED','Shared ?');
DEFINE('_BIZ_SHOW_UI','Show UI');

// Widget
DEFINE('_BIZ_DESIGN_TOOLBOX','Design toolbox');
DEFINE('_BIZ_WIDGET_TEMPLATE','Page template');
DEFINE('_BIZ_WIDGET_ADD','Add widget');
DEFINE('_BIZ_WIDGET_ZONE','in zone');
DEFINE('_BIZ_LIMIT_TO','Limit to');
DEFINE('_BIZ_LEFT_ZONE','Left zone');
DEFINE('_BIZ_RIGHT_ZONE','Right zone');
DEFINE('_BIZ_MAIN_ZONE','Main zone');
DEFINE('_BIZ_REMOVE','Remove this box');
DEFINE('_BIZ_SAVE_WIDGETS','Save widgets');
DEFINE('_BIZ_REFRESH_WIDGET_BUGS','Refresh content of this box');
DEFINE('_BIZ_EDIT_SETTINGS','Edit settings');
DEFINE('_BIZ_DOCUMENTS','Documents');
DEFINE('_BIZ_MEDIA','Media');
DEFINE('_BIZ_WIRE_CONTENT','Wire content');
DEFINE('_BIZ_NEWS_CONTENT','News');

// Date Format
DEFINE('_DATE_FORMAT','%Y-%m-%d');
DEFINE('_DATE_TIME_FORMAT','%Y-%m-%d @ %H:%M');

// Publication workflow
DEFINE('_BIZ_VERSION_CREATED_BEFORE_PUBLICATION','Version created automatically before publication');
DEFINE('_BIZ_VERSION_CREATED_ON_DEMAND','Version created by user ');

// Export
DEFINE('_BIZ_EXPORT','Export');
DEFINE('_BIZ_EXPORT_COLLECTION','Export to collection');
DEFINE('_BIZ_EXPORTED_COLLECTION','The items were exported correctly');
DEFINE('_BIZ_COLLECTION_NAME_MANDATORY','The collection name is mandatory');
DEFINE('_BIZ_NO_ITEMS_SELECTED','No items selected');
DEFINE('_BIZ_CHOOSE_EXPORT_OBJECT','Choose the type of bizobject');
DEFINE('_BIZ_CREATE_NEW','Create a new bizobject');
DEFINE('_BIZOBJECTS','Bizobjects');
DEFINE('_BIZ_CHOOSE_BIZOBJECT','Select a bizobject');
DEFINE('_BIZ_EXPORT_SUCCESFULL_TO','Bizobjects successfully exported to ');
DEFINE('_BIZ_EXPORT_ERROR','There was an error with the export. Please try again.');
DEFINE('_BIZ_CHOOSE_CHANNEL','Choose a section');
DEFINE('_BIZ_CHOOSE_COLLECTION','Choose a collection');
DEFINE('_BIZ_CHOOSE_SLIDESHOW','Choose a slideshow');

// move to channel
DEFINE('_BIZ_MOVE_CHANNEL','Move to a section');
DEFINE('_BIZ_MOVE_SUCCESS','Selected items successfully moved to section ');

// FOLDER
DEFINE('_BIZ_FOLDER','Folder');
DEFINE('_BIZ_PARENT_FOLDER','Parent folder');
DEFINE('_BIZ_FOLDERS_HIERARCHY','Folders hierarchy');
DEFINE('_BIZ_FOLDER_PERM','Permanent');
DEFINE('_BIZ_FOLDER_AUTO','Automatic');
DEFINE('_BIZ_FOLDER_TEMP','Temporary');

DEFINE('_BIZ_FOR_SELECTION','For the selection');
DEFINE('_BIZ_SERVICES','Services');

// OTHER
DEFINE('_BIZ_ALERT','ALERT');
DEFINE('_BIZ_ERROR_CHANNELID_IS_MANDATORY','Warning: Main Channel is mandatory (radio button)');