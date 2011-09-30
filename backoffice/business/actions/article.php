<?php
/**
 * Project:     WCM
 * File:        article.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
/**
 * This class implements the action controller for the channel
 */
class articleAction extends wcmMVC_BizAction
{
    /**
     * Save last edited design zones
     */
    private function saveZones()
    {
        // Retrieve new design zones?
        $zones = getArrayParameter($_REQUEST, '_zones', null);
        $this->context->updateZones($zones);
    }
    
    /**
     * Save chapters from content tab
     */
    private function saveChapters()
    {
        if(isset($_REQUEST['chapter_title'])) 
        {
            $chapters = array();
            
            $chaptersTitle = getArrayParameter($_REQUEST, 'chapter_title');
            $chaptersSubtitle = getArrayParameter($_REQUEST, 'chapter_subtitle');
            $chaptersText = getArrayParameter($_REQUEST, 'chapter_text');
            $chaptersAuthor = getArrayParameter($_REQUEST, 'chapter_author');
            $chaptersLink = getArrayParameter($_REQUEST, 'chapter_link');
            $chaptersPhoto = getArrayParameter($_REQUEST, 'chapter_photo');
            $chaptersPhotoCredit = getArrayParameter($_REQUEST, 'chapter_photo_credit');
            $chaptersPhotoCaption = getArrayParameter($_REQUEST, 'chapter_photo_caption');
            $chaptersCompany = getArrayParameter($_REQUEST, 'chapter_company');
            if(is_array($chaptersTitle))
            {
                foreach($chaptersTitle as $key => $title)
                {
                        $chapters[] = array('title' => $title, 'subtitle' => getArrayParameter($chaptersSubtitle, $key), 'text' => getArrayParameter($chaptersText, $key), 'image_url' => getArrayParameter($chaptersPhoto, $key), 'image_credits' => getArrayParameter($chaptersPhotoCredit, $key), 'image_caption' => getArrayParameter($chaptersPhotoCaption, $key), 'author' => getArrayParameter($chaptersAuthor, $key), 'link' => getArrayParameter($chaptersLink, $key), 'company' => getArrayParameter($chaptersCompany, $key));
                }
            }
            $this->context->updateChapters($chapters);
        }
    }
    
    /**
	 * Save chapters from content tab
	 */
	private function saveInserts()
	{
		$inserts = array();

		if(isset($_REQUEST['inserts_kind'])) 
		{

			$insertsKind = getArrayParameter($_REQUEST, 'inserts_kind');
			$insertsText = getArrayParameter($_REQUEST, 'inserts_text');
			$insertsTitle = getArrayParameter($_REQUEST, 'inserts_title');
			$insertsSource = getArrayParameter($_REQUEST, 'inserts_source');
			if(is_array($insertsKind))
			{
				foreach($insertsKind as $key => $title)
				{
					$inserts[] = array('kind' => getArrayParameter($insertsKind, $key), 'text' => getArrayParameter($insertsText, $key), 'title' => getArrayParameter($insertsTitle, $key), 'source' => getArrayParameter($insertsSource, $key));
				}
			}
		}
		else
		{
			$inserts[] = array('kind' => '', 'text' => '');
		}
		$this->context->updateInserts($inserts);
    }
    
    /**
     * beforeSaving is called on checkin and on save before the store
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);

        $this->saveZones();
        $this->saveChapters();
        $this->saveInserts();
    }
}
