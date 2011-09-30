<?php
/**
 * Project:     WCM
 * File:        wcm.smartyModifier.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class holds a set of Smarty modifiers
 * They call be called through a generic |wcm
 * modifier. e.g. {$bizobject|@wcm:name:param1:param2}
 * where 'name' is the function name
 *
 * Note: this class is extended by smartyModifier in
 * the business directory to allow overwrite and extension
 * for profesionnal services, partners and customers
 */
abstract class wcmSmartyModifiers
{
 	
	/**
     * Returns the permissions on a given object
     *
     * Usage: {$elem|wcm:permission:permission_value}
     *
     * @param array $bizobject Assoc array of the bizobject
     *
     * @return permissions on given bizobject 
     */
	public function permission($bizobject, $permission)
    {
        $object = new $bizobject["className"]($bizobject["id"]);
		return wcmSession::getInstance()->isAllowed($object, $permission);
    }
    
    /**
     * Returns the content of a channel of collection
     *
     * Usage: {$channel|@wcm:content[:$limit[:$date]]}
     *
     * @param array $bizobject Assoc array of the bizobject
     * @param int $limit Maximum number of items to return (default is 10)
     * @param date $date Date used to compute channel or collection content (default is today's date)
     *
     * @return An assoc array representing channel's content (mixed of bizobject and bizrelation assoc arrays)
     */
    public function content($bizobject, $limit=10, $date = null)
    {
        // set default value for date parameter?
        if ($date === null) $date = date('Y-m-d');

        $className = $bizobject['className'];
        switch($className)
        {
            case 'channel':
            case 'collection':
                $result = array();
                $channel = $bizobject['this'];
                foreach($channel->getContent($limit, $date, false, false) as $item)
                {
                    if ($item !== null)
                    {
                        $result[] = ($item instanceOf wcmObjectAssocArray) ? $item : $item->getAssocArray(false);
                    }
                }
                return $result;

            default:
                return null;
        }
    }
    
    /**
     * Compute the permalink (on the web site) of a bizobject
     *
     * @param array $bizobject Assoc array of the bizobject
     *
     * @return string URL of the bizobject
     */
    public function permalink($bizobject)
    {
        $className = $bizobject['className'];
        switch($className)
        {
                case 'site':
                        return 'site' . $bizobject['id'].'/';

                case 'chapter':
                        $article = new article(null, $bizobject['articleId']);
                        return $this->permalink($article->getAssocArray());

                case 'contribution':
                        $comment = $bizobject['this'];
                        if (!($comment instanceof wcmBizclass) && $comment->getReferent())
                            return $this->permalink($comment->getReferent()->getAssocArray());
                    
                case 'channel':
                        return 'site' . $bizobject['siteId'] . '/' . safeFileName(strtolower($bizobject['title'])) . '.html';

                case 'newsitem':
                case 'article':
                case 'slideshow':
                case 'forum':
                default:
                        return 'site' . $bizobject['siteId'] . '/' . $className . '/' . $bizobject['id'] . '/'
                                . safeFileName(strtolower($bizobject['title'])) . '.html';
                 
        }

        return null;
    }

    /**
     * Compute the URL (on the web site) of a bizobject
     *
     * @param array $bizobject Assoc array of the bizobject
     *
     * @return string URL of the bizobject
     */
    public function url($bizobject)
    {
        $url = $this->permalink($bizobject);
        if (!$url) return null;

        $config = wcmConfig::getInstance();
        return $config['wcm.webSite.url'] . $url;
    }
    
    /**
     * Returns a photo assocArray corresponding to the first bizobject's photo
     * or null if the bizobject has no photo.
     *
     * @param array $bizobject Assoc array of the bizobject
     *
     * @return string An assoc array representing first photo or null
     */
    public function firstPhoto($bizobject)
    {
        if (!$bizobject && !is_array($bizobject)) return null;

        $sql = 'SELECT destinationId FROM #___relation WHERE sourceClass=? AND sourceID=? AND destinationClass=? AND kind=?';
        $db = wcmProject::getInstance()->datalayer->getConnectorByReference('biz')->getBusinessDatabase();
        $id = $db->executeScalar($sql, array($bizobject['className'], $bizobject['id'], 'photo', bizrelation::IS_COMPOSED_OF));

        if (!$id) return null;

        $photo = new photo(null, $id);
        $result = $photo->getAssocArray(false);
        unset($photo);

        return $result;
    }

    /**
     * Returns an array of bizrelation associated to a bizobject
     *
     * @param array $bizobject Assoc array of the bizobject
     * @param int   $kind Kind of relation (or bizrelation::IS_RELATED_TO by default)
     *
     * @return array An array of bizrelation's getAssocArray
     */
    public function relations($bizobject, $kind = null)
    {
        if (!$kind) $kind = bizrelation::IS_RELATED_TO;

        $relations = array();

        $bizrelation = new bizrelation();
        $where = "sourceClass='" . $bizobject['className'] . "' AND sourceId=" . $bizobject['id'] . " AND kind=" . $kind;
        if ($bizrelation->beginEnum($where, "rank"))
        {
            while ($bizrelation->nextEnum())
            {
                $relations[] = $bizrelation->getAssocArray(false);
            }
            $bizrelation->endEnum();
        }

        return $relations;
    }

    /**
     * Return the URL of a bizPhoto
     * 
     * @param array     $bizobject  Assoc array of the bizobject
     * @param String    $format     Format of the image to generate (Default to 'original') 
     * @param Bool      $forceGen   set to yes if you want to force the generation (Default to false)
     *
     * @return string URL of the photo
     */
    public function photoUrl($bizobject, $format = 'original', $forceGen = false)
    {
        $config = wcmConfig::getInstance();
        $cmanager = new croppingManager($bizobject['this'], WCM_DIR . '/business/xml/photosratios/default.xml');

        if(!$path = $cmanager->getPhotoUrl($format, $forceGen))
                if($format == 'thumbnail')
                        $path = $bizobject['thumbnail'];
                else
                        $path = $bizobject['original'];

        return $config['wcm.backOffice.url'] . $path;
    }
    
    /**
     * Return a specific entry from the result of URL parsing (parse_url() method)
     * 
     * @param string $url  URL to parse
     * @param string $key  Key to retrieve (e.g. 'path', 'protocol', ...)
     *
     * @return mixed Either result of php function parse_url($url) or specific key when $key is set
     */
    public function parseUrl($url, $key)
    {
        $parsed = parse_url($url);
        return ($key) ? $parsed[$key] : $parsed;
    }
}
