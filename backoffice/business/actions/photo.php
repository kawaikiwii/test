<?php
/**
 * Project:     WCM
 * File:        photo.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * This class implements the action controller for the photo
 */
class photoAction extends wcmMVC_BizAction
{
    /**
     * Save contents from content tab
     */
    private function saveContents()
    {
        if (isset($_REQUEST['content_photo_title']))
        {
            $contents = array ();

            $contentsTitle = getArrayParameter($_REQUEST, 'content_photo_title');
            $contentsDescription = getArrayParameter($_REQUEST, 'content_photo_description');
            $contentsText = getArrayParameter($_REQUEST, 'content_photo_text');
			$credits = getArrayParameter($_REQUEST, 'content_photo_credits');
			$specialUses = getArrayParameter($_REQUEST, 'content_photo_specialUses');

            $contents = array ('title'=>$contentsTitle,
            					'description'=>$contentsDescription,
            					'text'=>$contentsText,
								'credits'=>$credits,
								'specialUses'=>$specialUses);
			
            $this->context->updateContents($contents);
        }
    }

    /**
     * beforeSaving - process information before sending object
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);
        $this->saveContents();

        if ( isset ($_REQUEST['original']))
        {
            if (substr($_REQUEST['original'], 0, 5) == '_tmp_')
            {
                //DEFINE('_THUMB_WIDTH', 250);
               
			    $config = wcmConfig::getInstance();
				$creationDate = dateOptionsProvider::fieldDateToArray(date('Y-m-d H:i:s'));

				$photoDir = $config['wcm.webSite.repository'].'illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';

                $originalFileName = $_REQUEST['original'];
				
				$newFileName = photo::getFinalPicName($originalFileName, $photoDir);
				
                rename($photoDir.$originalFileName, $photoDir.$newFileName);
				
				$_REQUEST['original'] = $newFileName;
				
                /*try
                {
                    $img = new wcmImageHelper($photoDir.$newFileNames['original']);
					//$img->thumb($photoDir.$newFileNames['thumb'], _THUMB_WIDTH, _THUMB_HEIGHT);
                    $img->resize($photoDir.$newFileNames['thumb'], _THUMB_WIDTH, 100000, true);
					list($width, $height, $type, $attr) = getimagesize($photoDir.$newFileNames['thumb']);
					
					@chmod($photoDir.$newFileNames['original'], 0775);
					@chmod($photoDir.$newFileNames['thumb'], 0775);
					
                    $_REQUEST['original'] = $newFileNames['original'];
                    $_REQUEST['thumbnail'] = $newFileNames['thumb'];
                    $_REQUEST['thumbWidth'] = _THUMB_WIDTH;
                    $_REQUEST['thumbHeight'] = $height;
                }
                catch(Exception $e)
                {
                    @unlink($photoDir.$originalFileName);
                    wcmMVC_Action::setError(_BIZ_INVALID_PICTURE);
                }*/
            }
        }
    }

    /**
     * onSave
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function onSave($session, $project)
    {
        $this->beforeSaving($session, $project);
        if (!$this->context->save($_REQUEST))
        {
            wcmMVC_Action::setError(_BIZ_ERROR.$this->context->getErrorMsg());
            return;
        }

        // Add statistics
        $session->addStat(wcmSession::STAT_SAVE_OBJECT, $this->context);

        // Create a new version?
        if ( isset ($_REQUEST['_comment']))
        {
            $this->onCreateVersion($session, $project);
        }

        // Redirect to 'view' URL
        if ( isset ($_REQUEST['_redirect']))
        {
            $this->redirect(wcmModuleURL('business/subForms/uploadPhoto',
            array ('uid'=>$_REQUEST['_redirect'], 'photoId' => $this->context->id)
            ));
        }
        else
            $this->redirect(self::computeObjectURL($this->context->getClass(), $this->context->id));
    }
	
	
	
	/**
     * Deletes object from database
     *
     * @return true on success or false otherwise
     *
     */
    public function onDelete($session, $project)
    {
        $this->removePictures();
		return parent::onDelete($session, $project);
    }
	
	
	
	/**
     * Deletes all pictures associated to this Photo object
     *
     */
	public function removePictures()
    {
        if($this->id == 0) return false;

        $config = wcmConfig::getInstance();
		$creationDate = dateOptionsProvider::fieldDateToArray($this->context->createdAt);
		$dir = $config['wcm.webSite.repository'].'illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';
		
		$fileName = $this->context->original;
		
		if ($this->context->formats)
		{
			$formats = unserialize($this->context->formats);

			foreach($formats as $format => $sizes)
			{
				@unlink($dir . str_replace('original', $format, $fileName));
			}
		}
		@unlink($dir . $fileName);
		// Old pictures may have a special thumbnail name
		if (file_exists($dir . 'thumb-'.$fileName)) { @unlink($dir . 'thumb-'.$fileName); }
		
		return true;
    }
}
