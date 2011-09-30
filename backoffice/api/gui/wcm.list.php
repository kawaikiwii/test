<?php

/**
 * Project:     WCM
 * File:        wcm.list.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.01 - Relax
 *
 */

/**
 * The wcmList class represents a list/element object
 */
class wcmList extends wcmSysobject
{
    
    /**
     * Path separator
     * Used to compute list path
     */
    const PATH_SEPARATOR = "-";
    
    /**
     * List id
     */
    public $id;

    /**
     * List path
     */
    public $path;

    /**
     * Parent Id
     */
    public $parentId = null;

    /**
     * Code
     */
    public $code;

    /**
     * Label
     */
    public $label;

    /**
     * Save
     */
    public function save()
    {
        parent::save();

        if (!$this->path)
        {
        	$this->path = $this->computePath(); 
          parent::save();
        }       

        return $this->id;
    }

    /**
     * computePath
     *
     * @param $format [id/code/label]
     */
    public function computePath($format='id')
    {
	$path = getConst($this->$format);
	
	if (!$this->parentId)
		return $path;
	else
	{
        	$currentList = new wcmList();
		$currentList->refresh($this->parentId);
		$path = getConst($currentList->$format) . self::PATH_SEPARATOR . $path;        	
        	
		while ($currentList->parentId)
		{
			$currentList->refresh($currentList->parentId);
			$path = getConst($currentList->$format) . self::PATH_SEPARATOR . $path;        	
		}
	}
	return $path;
    }

    /**
     * addList
     *
     * @param $code
     * @param $label
     * @param $parentId
     */
    static function addList($code, $label, $parentId=null)
    {
	$newList = new wcmList();
	
	$newList->code     = $code;
	$newList->label    = $label;
	$newList->parentId = $parentId;
	
	if ($newList->save())
		return $newList->id;
	else
		return false;
    }

    /**
     * dropList
     *
     * @param $id
     */
    static function dropList($id)
    {
	$newList = new wcmList();
	
	if ($newList->refresh($id))
	{
		$objectList = new wcmList();
		$objectList->beginEnum("parentId=".$id);
		while ($objectList->nextEnum())
		{
		    wcmList::dropList($objectList->id);
		}
		
		$newList->delete();
	}
    }

    /**
     * getFinalContent
     *
     * @param $id
     * @param $depth  [-1:infinite/1/2/...]
     * @param $key    [id/code/path]
     *
     * return Flat Assoc Array
     */
    static function getFinalContent($id,$depth=-1,$key='id')
    {
	$resultArray = array();
	
	$newList = new wcmList();
	
	if ($newList->refresh($id))
	{
		$objectList = new wcmList();
		
    		$objectList->beginEnum("parentId=".$id);
		if ($objectList->enumCount() && $depth != 0)
		{
			while ($objectList->nextEnum())
			{
			    $t = wcmList::getFinalContent($objectList->id,$depth-1,$key);
			    $k = key($t);
			    $resultArray[$k] = $t[$k];
			}
		}
		else
		{
			$resultArray[$newList->$key] = getConst($newList->label);
		}
	}
	return $resultArray;
    }

    
    /**
     * getContent
     *
     * @param $id
     * @param $format [assocArray/xml]
     *
     * return Complete List Structure
     */
    static function getContent($id,$xml=false)
    {
	$result = ($xml) ? "<List></List>" : array();
	
	$newList = new wcmList();
	
	if ($newList->refresh($id))
	{
		if ($xml)
			$result = '<List><id>'.$newList->id.'</id><code>'.$newList->code.'</code><path>'.$newList->path.'</path><label>'.getConst($newList->label).'</label>';
		else
			$result = array('id'       => $newList->id,
					'code'     => $newList->code, 
					'path'     => $newList->path, 
					'label'    => getConst($newList->label),
					'subLists' => array());
		
		
		$objectList = new wcmList();
		
		$objectList->beginEnum("parentId=".$id);
		
		while ($objectList->nextEnum())
		{
		    if ($xml)
			$result .= '<SubLists>'.wcmList::getContent($objectList->id,$xml).'</SubLists>';
		    else
			$result['subLists'][] = wcmList::getContent($objectList->id,$xml);
		}
		
		if ($xml)
			$result .= "</List>";
	}
	
	return $result;
    }

    /**
     * getRootContent
     *
     * @param $format [assocArray/xml]
     *
     * return Complete List Structure From Root Lists
     */
    static function getRootContent($xml=false)
    {
	$result = ($xml) ? "<RootList>" : array();
		
	$objectList = new wcmList();
	$objectList->beginEnum("parentId IS NULL");
	while ($objectList->nextEnum())
	{
		if ($xml)
			$result .= wcmList::getContent($objectList->id,$xml);
		else
			$result[] = wcmList::getContent($objectList->id,$xml);
	}
		
	if ($xml)
		$result .= "</RootList>";

	return $result;
    }
    
    static function getArborescenceList($item=null,$cpt=0,&$arr='')
    {
        if(!$item)
        {
          $item = wcmList::getRootContent();
          $arr[]='';
        }
        
        foreach($item as $subitem)
        {
          $arr[$subitem['id']] = str_repeat("-- ",$cpt+1).$subitem['label'];
          if(count($subitem['subLists'])>0) wcmList::getArborescenceList($subitem['subLists'],$cpt+1, $arr);
        }

        return $arr;
    }
    
    
     /**
     * send notification code from parent id
     *
     * @return array of different code available
     * 
     */
	static function getListFromParentCode($code)
    {
    	$liste = new wcmList();
    	$liste->refreshByCode($code);
    	$listCode = array();
    	
    	if ($liste->id)
    	{
	    	$listArray = wcmList::getFinalContent($liste->id);
	    	
	    	if (!empty($listArray))
	    	{
	    		foreach($listArray as $listId=>$ListLabel)
	    		{
	    			$list = new wcmList();
	    			$list->refresh($listId);
	    			if (!empty($list->code))
	    				$listCode[$list->id] = $list->code;	
	    		}
	    	}	
    	}
    	return $listCode;
    }
    
    static function getListLabelsFromParentCode($code)
    {
    	$liste = new wcmList();
    	$liste->refreshByCode($code);
    	$listCode = array();
    	
    	if ($liste->id)
    	{
	    	$listArray = wcmList::getFinalContent($liste->id);
	    	
	    	if (!empty($listArray))
	    	{
	    		foreach($listArray as $listId=>$ListLabel)
	    		{
	    			$list = new wcmList();
	    			$list->refresh($listId);
	    			if (!empty($list->code))
	    				$listCode[$list->id] = $list->label;	
	    		}
	    	}	
    	}
    	return $listCode;
    }
    
    public function refreshByCode($code)
    {
        $sql = 'SELECT id FROM '.$this->getTableName().' WHERE code=?';
        $id = $this->database->executeScalar($sql, array($code));
        return $this->refresh($id);    
    }
    
    static function getListFromParentCodeForDropDownList($code, $depth=1)
    {
    	$liste = new wcmList();
    	$liste->refreshByCode($code);
    	$listCode = array();
    	
    	if ($liste->id)
    	{
	    	$listArray = wcmList::getFinalContent($liste->id, $depth);
	    	
	    	if (!empty($listArray))
	    	{
	    		foreach($listArray as $listId=>$ListLabel)
	    		{
	    			$list = new wcmList();
	    			$list->refresh($listId);
	    			if (!empty($list->code) )
	    				$listCode[$list->code] = getConst($list->label);	
	    		}
	    	}	
    	}
    	return $listCode;
    }
    
	static function getIdFromCode($code)
    {
    	$liste = new wcmList();
    	$liste->refreshByCode($code);
    	if (isset($liste->id)) return $liste->id;
    	else return null;
    }
}