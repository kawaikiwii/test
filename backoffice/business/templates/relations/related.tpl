{assign var="className" value=$bizobject.className}

{php}
	$className = $this->get_template_vars('className');
	$config = wcmConfig::getInstance();
	
	if (file_exists($config["wcm.templates.path"].'relations/related/'.$className.'.tpl'))
	{
		$this->assign('fileToInclude', $className.'.tpl');
	}
	else $this->assign('fileToInclude', 'default.tpl');
{/php}

{include file="`$config.wcm.templates.path`relations/related/`$fileToInclude`"}