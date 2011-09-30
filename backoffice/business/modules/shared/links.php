<?php

/**
 * Project:     WCM
 * File:        links.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();
    $kind = (count($params) > 0) ? array_shift($params) : wcmBizrelation::IS_RELATED_TO;
    $className = (count($params) > 0) ? array_shift($params) : '';
    $pk = '_br_'.$kind;
?>
<div class="relation-builder">
    <?php wcmGUI::renderHiddenField('_list[]',$pk); ?>
    <div id="relations" class="relations">
        <ul id="sortable_relations">
        <?php
            $relation = new wcmBizrelation();
            $where = 'kind='.$kind;
            if ($className) $where .= ' AND destinationClass="'.$className.'"';
            $relation->beginEnum($where, "rank", null, null, array($bizobject->getClass() => $bizobject->id));
            while ($relation->nextEnum())
            {
                echo wcmXML::processXSLT($relation->toXML(), WCM_DIR . '/xsl/list/renderObject.xsl', array('pk' => $pk));
            }
        $relation->endEnum();
        ?>
        </ul>
    </div>
<?php
    $filter = ($className) ? 'className:'.$className : '(className:article AND className:photo)';
    wcmModule('business/search/relation', array($filter));
?>
</div>
<script type="text/javascript">
    linksManager = new jsRelationManager({pk: '<?php echo $pk; ?>'});
</script>