<?php
/**
 * Project:     WCM
 * File:        slideshow.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class implements the action controller for the slideshow
 */
class slideshowAction extends wcmMVC_BizAction
{
    /**
     * Save last edited design zones
     */
    /*private function saveZones()
    {
        // Retrieve new design zones?
        $zones = getArrayParameter($_REQUEST, '_zones', null);
        $this->context->updateZones($zones);
    }*/

    private function saveContents()
	{
		if ( isset ($_REQUEST['content_slideshow_title']))
		{
			$contentsTitle = getArrayParameter($_REQUEST, 'content_slideshow_title');
			$contentsDescription = getArrayParameter($_REQUEST, 'content_slideshow_description');
			$contentsText = getArrayParameter($_REQUEST, 'content_slideshow_text');
			$titleSigns = getArrayParameter($_REQUEST, 'content_slideshow_titleSigns');
			$titleWords = getArrayParameter($_REQUEST, 'content_slideshow_titleWords');
			$descriptionSigns = getArrayParameter($_REQUEST, 'content_slideshow_descriptionSigns');
			$descriptionWords = getArrayParameter($_REQUEST, 'content_slideshow_descriptionWords');
			$textSigns = getArrayParameter($_REQUEST, 'content_slideshow_textSigns');
			$textWords = getArrayParameter($_REQUEST, 'content_slideshow_textWords');

			$contents = array (	'title' =>  $contentsTitle,
							   	'description' => $contentsDescription,
								'text' => $contentsText,
								'titleSigns' => $titleSigns,
								'titleWords' => $titleWords,
								'descriptionSigns' => $descriptionSigns,
								'descriptionWords' => $descriptionWords,
								'textSigns' => $textSigns,
								'textWords' => $textWords);
			$this->context->updateContents($contents);
		}
	}

    /**
     * is called on checkin and on save before the store
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);

		// Initial version. Now we save the contents.
        //$this->saveZones();
        $this->saveContents();
    }
}