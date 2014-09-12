<?php

class CampaignsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
 /*       $dn = new Zend_Session_Namespace();
        $userid = $dn->userid;
        $myvar = $_SESSION['Default']['userid'];
        $session_name = session_name();
        $cookie_session = $_COOKIE[session_name()];*/
        $this->view->campaignURL = $this->_helper->config('application')->loveLoginUrl . 'campaign.php'; /*?d=' . $userid 
            . '&myvar='.$myvar
            . '&session_name='.$session_name
            . '&cookie_session='.$cookie_session
            ;*/
        
    }
 
}

