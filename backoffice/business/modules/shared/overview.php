<?php
/**
 * Project:     WCM
 * File:        modules/editorial/article/overview.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    echo '<div class="zone">';
    $className = $bizobject->getClass();
    $file = $config['wcm.templates.path'] . 'overview/' . $className . '.tpl';
    $template = (file_exists($file)) ? 'overview/' . $className . '.tpl' : 'overview/bizobject.tpl';
    $generator = new wcmTemplateGenerator();
    echo $generator->executeTemplate($template, array('bizobject' => $bizobject->getAssocArray(false)));
    echo '</div>';
