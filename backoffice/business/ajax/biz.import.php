<?php

require_once dirname(__FILE__).'/../../initWebApp.php';

switch ($_REQUEST['command'])
{
    case 'jit':
        
        $id = $_REQUEST['id'];
        
        $idParts = explode('_',$id);
        
        $classParts = explode('-',$idParts[0]);
        $class = array_pop($classParts);
        
        if ($_SESSION['wcm']['tmpObjects'][$id]['importPlugin'])
        {
            $import = new $_SESSION['wcm']['tmpObjects'][$id]['importPlugin'];
            $obj = $_SESSION['wcm']['tmpObjects'][$id];
            
            if ($obj instanceof wcmObject)
            {
                $bo = $import->importFromObject($obj);
            } else {
                $bo = $import->importFromXML($obj);
            }
            return ($bo->id)? $bo->id : 0;
        } else {
            if ($_SESSION['wcm']['tmpObjects'][$id] instanceof wcmObject)
            {
                $bo = $_SESSION['wcm']['tmpObjects'][$id];
            } else {
                $bo = new $class;
                $bo->initFromXML($_SESSION['wcm']['tmpObjects'][$id]);
            }
            $bo->versionNumber = 1;
            $bo->id = null;
            
            if ($bo->save())
            {
                echo $bo->id;
            } else {
                $log = wcmProject::getInstance()->logger;
                $log->logError($bo->getErrorMsg());                
                echo '0';
            }
        }
        break;
        
}

?>