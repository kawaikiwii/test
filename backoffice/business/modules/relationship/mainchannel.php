<?php
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

$_SESSION['current_channel'][$bizobject->id] = $bizobject;

$prefix = getArrayParameter($params, 'prefix', '_wcm_rel_');
$searchEngine = getArrayParameter($params, 'searchEngine', $config['wcm.search.engine']);
$resultSetStyle = getArrayParameter($params, 'resultSetStyle', 'grid');
$kind = getArrayParameter($params, 'kind', wcmBizrelation::IS_COMPOSED_OF);
$pk = '_br_'.$kind;
?>
<div class="relation-builder">
<?php
    wcmGUI::renderHiddenField('_list[]',$pk);
?>
<div id="<?php echo $prefix;?>relations" class="relations">

 <?php 
 
    $mixedresults = $bizobject->getContent(12);
    
    $tpl = new wcmTemplateGenerator();
    $tpl->assign('prefix',$prefix);
    $tpl->assign('mixedresults',$mixedresults);
    $tpl->assign('pk',$pk);
    $tplFile = $config['wcm.templates.path'] . '/relations/mainchannel.tpl';
    $html = $tpl->fetch($tplFile);
    
    echo $html;
?>
</div>
<?php
 // display search placeholder
    echo '<div class="search">';
    if (getArrayParameter($params,'createTab',false))
    {
        $tabs = new wcmAjaxTabs(uniqid().'relationship', true);
        $tabs->addTab(  uniqid().
                        'wcmSearch', 
                        'WCM Search', 
                        true, 
                        null, 
                        wcmModuleURL('business/relationship/wcmSearch',  $params)
                     );
        $tabs->addTab(  uniqid().'create',
                        'Upload Media',
                        true,
                        null,
                        wcmModuleURL('business/subForms/subForm', 
                            array('module' => $params['createModule'],
                                  'options' => array('uid' => $params['uid'])
                                                        
                        ))
                     );
        $tabs->render();
    }
    else
    {
       wcmModule('business/relationship/wcmSearch',  $params);
    }
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

