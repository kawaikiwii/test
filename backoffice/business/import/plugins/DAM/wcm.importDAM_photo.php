<?php

class wcmImportDAM_photo
{
    
    /**
     * The array of parameters for this import
     *
     * @var array
     */
    protected $parameters;
    
    /**
     * Photo object
     *
     * @var photo
     */
    protected $bizObj;
    
    /**
     * DOMDocument of the bizobject
     *
     * @var DOMDocument
     */
    protected $bizDom;
    
    /**
     * The logger being used for this import
     *
     * @var wcmLogger
     */
    protected $logger;
    
    protected $sessionToken;
    protected $securityToken;
    
    public function __construct(array $params, wcmObject $bizObj, DOMDocument $bizDom, wcmLogger $logger)
    {
        $this->parameters = $params;
        $this->bizObj = $bizObj;
        $this->bizDom = $bizDom;
        
        $this->logger = $logger;
    }
    
    public function fetchObject($argClass, $argId)
    {
        
        $this->logger->logMessage('Fetching: '.$argClass.'_'.$argId);
        
        $service = new SoapClient($this->parameters['managementWsdl']);
        
        $ts = mktime(0,0,0,date('m'),date('d')+1,date('Y'));
        $expire = date('Y',$ts).'-'.date('m',$ts).'-'.date('d',$ts);
        // $expire = date('Y').'-'.date('m').'-'.(date('d') + 1);
        $params = array(
            'sessionToken' => $this->parameters['sessionToken'],
            'userId' => $this->parameters['userId'],
            'expirationDate' => $expire,
            );
        $token = $service->GenerateMediaRepositorySecurityToken($params)->GenerateMediaRepositorySecurityTokenResult;
        
        if (!$token) $this->logger->logWarning(_BIZ_IMPORT_DAM_INVALID_TOKEN);
        
        $service = new SoapClient($this->parameters['mediaWsdl']);
        
        $params = array(
            'mediaId' => $argClass.'_'.$argId,
            'processName' => 'original',
            'version' => 1,
            'securityToken' => $token);
        
        return $service->GetMedia($params)->GetMediaResult;
    }    
    
    public function process()
    {
        $config = wcmConfig::getInstance();
        $id = $this->bizDom->getElementsByTagName('Id')->item(0)->textContent;
        
        $filename = $this->bizDom->getElementsByTagName('OriginalName')->item(0)->textContent;
        $binaryData = $this->fetchObject($this->parameters['damClassname'], $id);
        
        $dir = WCM_DIR.'/'.$config['wcm.backOffice.photosPath'];
        
        $fp = fopen($dir.$filename,'wb');
        fwrite($fp, $binaryData);
        fclose($fp);
        
        $this->bizObj->original = $config['wcm.backOffice.photosPath'].'/'.$filename;
        
        // make thumbnail
        $img = new wcmImageHelper($dir.$filename);
        $fileParts = explode('.',$filename);
        $ext = array_pop($fileParts);
        $fileNameSansExt = join('.',$fileParts);
        
        $thumbFile = $fileNameSansExt.'-vignette.'.$ext;
        $img->thumb($dir.$thumbFile, 100, 75);
        $thumb = WCM_DIR.'/'.$config['wcm.backOffice.photosPath'].'/'.$fileNameSansExt.'-vignette.'.$ext;
        $this->bizObj->thumbnail = $config['wcm.backOffice.photosPath'].'/'.$thumbFile;
        
        if (!$this->bizObj->title) $this->bizObj->title = $filename;
        
        return true;
    }
}

?>