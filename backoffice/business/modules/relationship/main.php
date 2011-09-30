﻿<?php
/**
 * Project:     WCM
 * File:        business/modules/relationship/main.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
$bizobject = wcmMVC_Action::getContext();
$config = wcmConfig::getInstance();
$session = wcmSession::getInstance();

/*
Setup our parameters
array(
    'prefix' => '_wcm_rel_', // a prefix to avoid ID collision when using two instances
    'kind' => wcmBizrelation::IS_COMPOSED_OF,
    'destinationClass' => 'photo',
    'classFilter' => 'photo',
    'resultSetStyle' => 'grid',
    'resultSetDiv' => 'relation_resultset',
    'searchEngine' => $config['wcm.search.engine'],
    'createTab' => true,
    'createModule' => 'business/subForms/uploadPhoto')
*/
$prefix = getArrayParameter($params, 'prefix', '_wcm_rel_');
$searchEngine = getArrayParameter($params, 'searchEngine', $config['wcm.search.engine']);
$resultSetStyle = getArrayParameter($params, 'resultSetStyle', 'grid');
$kind = getArrayParameter($params, 'kind', wcmBizrelation::IS_RELATED_TO);
$pk = '_br_'.$kind;

$onlyUniverse = getArrayParameter($params, 'onlyUniverse', false);
$allowedUniverse = getArrayParameter($params, 'allowedUniverse', '');
    
?>
<div class="relation-builder">
<?php
    wcmGUI::renderHiddenField('_list[]',$pk);


    $relations = wcmBizrelation::getBizobjectRelations($bizobject,$kind,$params['destinationClass'], null, false);

    $tpl = new wcmTemplateGenerator();
    $tpl->assign('prefix',$prefix);
    $tpl->assign('relations',$relations);
    $tpl->assign('pk',$pk);
	
	//$tpl->assign('targetId',$relations);
	
    
	/* 
	 * Choice of template file
	 * 
	 */
	if ($params['destinationClass'] == 'location') $tplFile = $config['wcm.templates.path'] . '/relations/location.tpl';
	else if ($params['destinationClass'] == 'video') $tplFile = $config['wcm.templates.path'] . '/relations/video.tpl';
	else $tplFile = $config['wcm.templates.path'] . '/relations/main.tpl';
    
	
	$html = $tpl->fetch($tplFile);
	
    echo $html;

	wcmGUI::renderHiddenField('schedule_event_targetId', $bizobject->id);

    $photo = new photo();
    // verify write permission before add the photo module
    if ($session->isAllowed($photo, wcmPermission::P_WRITE)){
        // display search placeholder
        echo '<div class="search" id="switchPaneManager">';
        if (getArrayParameter($params,'createTab',false))
        {
            $tabs = new wcmAjaxTabs(uniqid().'relationship', true);
            $tabs->addTab(  uniqid().
                            'wcmSearch',
                            _WCM_SEARCH,
                            true,
                            null,
                            wcmModuleURL('business/relationship/wcmSearch',  $params)
                         );
			if ($params['destinationClass'] != 'location' && $params['destinationClass'] != 'video')
			{
				$tabs->addTab(  uniqid().'create',
	                            _UPLOAD_MEDIA,
	                            true,
	                            null,
	                            wcmModuleURL('business/subForms/subForm',
	                                array('module' => $params['createModule'],
	                                      'options' => array('uid' => $params['uid'], 'nativeClass' => $bizobject->getClass())
	
	                            ))
	                         );
			}
            $tabs->render();
        }
        else
        {
           wcmModule('business/relationship/wcmSearch',  $params);
        }
        echo '</div>';
    }
?>
</div>
<script type="text/javascript">
wcmActionController.registerCallback('save', function() {

    $$('#<?php echo $prefix; ?>relations li').each(function(s)
    {
        if (s.id.endsWith('IMPORT'))
        {

            var boid = s.id.split('-').pop();
            alert(boid);


            box = new Element('div');
            box.id = s.id + '_IMPORTcover';
            box.setOpacity(0.7);
            box.setStyle({
                backgroundColor: '#f00',
                height: $(s.id).offsetHeight + 'px',
                width: $(s.id).offsetWidth + 'px',
                top: $(s.id).offsetTop + 'px',
                left: $(s.id).offsetLeft + 'px',
                position: 'absolute'
            });
            box.update('IMPORTING...');
            $('relations').appendChild(box);

            // TODO I never got this to work with POST for some reason (Alex Dowgailenko)
            new Ajax.Request(wcmBaseURL+'business/ajax/controller.php', {
                method: 'get',
                parameters: {
                    ajaxHandler: 'biz.import',
                    command: 'jit',
                    id: boid
                },
                onComplete: function(transport)
                {
                    if (transport.responseText != 0)
                    {

                        alert('Imported!');
                    } else {
                        return "Not imported successfully";
                    }
                }
            });
        }

    });
});
</script>