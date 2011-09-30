<?php
/**
 * Project:     WCM
 * File:        modules/editorial/collection/export.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * This module is used by the search export features.
 * It allows users to export selected items to a collection.
 * They can choose from existing collections or create a new one 
 *      New collections ::
 *          The user can add information in two fields: title and description
 *      Existing collections ::
 *          Choose from a select box
 *          Possibility to add to a collection or
 *          Replace content from existing collection by selected content
 */
// Initialize the system
require_once dirname(__FILE__) . '/../../../initWebApp.php';

$response = getArrayParameter($_REQUEST, 'response', null);

switch ($response)
{
    // add & replace basically work the same way expect for replace, we don't need
    // current relatedList
    case 'ADD':
    case 'REPLACE':
        $name = getArrayParameter($_REQUEST, 'name', null);
        $description = getArrayParameter($_REQUEST, 'description', null);
        $id = getArrayParameter($_REQUEST, 'id', null);
        // array containing select objects (className_id => true)
        $bizobjects = getArrayParameter($_SESSION, 'tempBin', null);
        if (!$bizobjects)
        {
            // @todo: empty selection -> display message
            return;
        }

        $collection = new collection();
        // create biz relations
        $rels = array();
        $relatedObjects = array();
        $relCheck = array();
        // if it's an existing collection, get the collection and it's relations (ADD)
        if ($id && $id != 'new')
        {
            $collection->refresh($id);
            if ($response == 'ADD')
            {
                foreach ($collection->getRelations() as $bizRel)
                {
                    echo $bizRel->destinationClass . '<br />';
                    if ($bizRel->kind == wcmBizrelation::IS_COMPOSED_OF)
                    {
                        $rels['destinationId'][] = $bizRel->destinationId;
                        $rels['destinationClass'][] = $bizRel->destinationClass;
                        $rels['title'][] = isset($bizRel->title) ? $bizRel->title : '';
                        $relCheck[] = $bizRel->destinationId . $bizRel->destinationClass;
                    } 
                }
            }
        }
        // otherwise, just create the collection
        else
        {
            $collection->title = $name;
            $collection->description = $description;
            $collection->siteId = wcmSession::getInstance()->getSiteId();
        }
        foreach($bizobjects as $key => $val)
        {
            $obj = explode('_', $key);
            $relatedObjects[] = new $obj[0](wcmProject::getInstance(), $obj[1]);
        }

        // add the relations
        foreach($relatedObjects as $relatedObject)
        {
            // only add the new relations if they don't exist
            if (!in_array($relatedObject->id . $relatedObject->getClass(), $relCheck))
            {
                $rels['destinationId'][] = $relatedObject->id;
                $rels['destinationClass'][] = $relatedObject->getClass();
                $rels['title'][] = getObjectLabel($relatedObject);
            }
        }
        $collection->updateBizrelations('_br_' . wcmBizrelation::IS_COMPOSED_OF, $rels);
        $collection->save();

        // display end message
        wcmGUI::openFieldset();
        echo '<li>' . _BIZ_EXPORTED_COLLECTION . '</li>';
        wcmGUI::closeFieldset();
        break;

    // display relations form
    default:
        $bizobjects = getArrayParameter($_SESSION, 'tempBin', null);
        if (!$bizobjects)
        {
            // @todo: empty selection -> display message
            return;
        }
        $collections = wcmBizobject::getBizobjects('collection', 'siteId=' . wcmSession::getInstance()->getSiteId());
        $collArray[0] = '('._SELECT.')';
        $collArray['new'] = _BIZ_CREATE . ' ' . _BIZ_NEW_COLLECTION;
        foreach ($collections as $collection)
        {
            $collArray[$collection->id] = $collection->title;
        }
        wcmGUI::openForm('exportCollection');
        wcmGUI::openFieldset();
        wcmGUI::renderDropdownField('_wcmCollectionId', $collArray, null, _BIZ_CHOOSE_OR_CREATE_COLLECTION, array('onchange' => '_wcmUpdateExport(this.value)'));
        wcmGUI::renderOpenTag('div', array('id' => '_wcmExportDiv', 'style' => 'display: none;'));
        wcmGUI::renderTextField('_wcmCollectionName', '', _BIZ_NAME . ' *', array('class' => 'type-req'));
        wcmGUI::renderTextArea('_wcmCollectionDescription', '', _BIZ_DESCRIPTION);
        wcmGUI::renderCloseTag('div');
        wcmGUI::closeFieldset();
        wcmGUI::closeForm();
        break;
}