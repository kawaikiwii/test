<?php
/**
 * Project:     WCM
 * File:        business/modules/relationship/multi.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
$bizobject = wcmMVC_Action::getContext();
$config = wcmConfig::getInstance();
$session = wcmSession::getInstance();

$prefix = getArrayParameter($params, 'prefix', '_wcm_rel_');
$searchEngine = getArrayParameter($params, 'searchEngine', $config['wcm.search.engine']);
$resultSetStyle = getArrayParameter($params, 'resultSetStyle', 'grid');
$kind = getArrayParameter($params, 'kind', wcmBizrelation::IS_RELATED_TO);
$pk = '_br_'.$kind;
?>
<div class="relation-builder">
<?php
    wcmGUI::renderHiddenField('_list[]',$pk);

	$relations = wcmBizrelation::getBizobjectRelations($bizobject, $kind, $params['destinationClass'], null, false);
	
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

    // display search placeholder
    echo '<div class="search" id="switchPaneManager">';
    
    //wcmModule('business/relationship/wcmSearchMulti',  $params);
    
		// argument 2 = cookie enable/disable
    	$tabs = new wcmAjaxTabs('multirelations', false);
        $tabs->addTab(  uniqid().'wcmMultiSearch',
                            _WCM_SEARCH,
                            true,
                            null,
                            wcmModuleURL('business/relationship/wcmSearchMulti',  $params)
                         );
                                
		if (!empty($params['createModule']) && $params['destinationClass'] != 'location' && $params['destinationClass'] != 'video')
		{
		$tabs->addTab(  uniqid().'create',
                            _UPLOAD_MEDIA,
                            false,
                            null,
                            wcmModuleURL('business/subForms/subForm',
                                array('module' => $params['createModule'],
                                      'options' => array('uid' => $params['uid'], 'nativeClass' => $bizobject->getClass())

                            ))
                         );
		}     
		
		if (( !empty($params['createModuleContact']) || !empty($params['createModulePlace'])) && ($bizobject->getClass() == 'prevision'))
		{
			$tabs->addTab(  uniqid().'createContact',
                            _CREATE_CONTACT,
                            false,
                            null,
                            wcmModuleURL('business/subForms/subForm',
                                array('module' => $params['createModuleContact'],
                                      'options' => array('uid' => $params['uid'], 'nativeClass' => $bizobject->getClass())

                            ))
                         );
                        
            $tabs->addTab(  uniqid().'createPlace',
                            _CREATE_PLACE,
                            false,
                            null,
                            wcmModuleURL('business/subForms/subForm',
                                array('module' => $params['createModulePlace'],
                                      'options' => array('uid' => $params['uid'], 'nativeClass' => $bizobject->getClass())

                            ))
                         );
                         
            $tabs->addTab(  uniqid().'createPersonality',
                            _CREATE_PERSONALITY,
                            false,
                            null,
                            wcmModuleURL('business/subForms/subForm',
                                array('module' => $params['createModulePersonality'],
                                      'options' => array('uid' => $params['uid'], 'nativeClass' => $bizobject->getClass())

                            ))
                         );
		}  
       	$tabs->render();
                            
    echo '</div>';
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
