<?php
/**
 * Project:     WCM
 * File:        poll.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
/**
 * This class implements the action controller for the poll
 */
class pollAction extends wcmMVC_BizAction
{

    /**
     * Save poll choices from content tab
     */
    private function saveChoices()
    {
        if(isset($_REQUEST['pollChoice_text'])) 
        {
            $choices = array();
            
            $choicesText = getArrayParameter($_REQUEST, 'pollChoice_text');
            if(is_array($choicesText))
            {
                foreach($choicesText as $key => $text) {
                        $choices[] = array('text' => getArrayParameter($choicesText, $key));
                }
            }
            $this->context->updateChoices($choices);
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
        
        $this->saveChoices();
    }
}
