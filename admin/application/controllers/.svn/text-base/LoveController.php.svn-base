<?php

class LoveController extends Zend_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
        // action body
    }

	public function testAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		
//		Send love to one user
//		$userMapper = new Application_Model_UserMapper();
//		$user = $userMapper->find(1040);
//		$love = new Sendlove_Love();
//		if ($love->sendLove($this->getSession()->nickname, $user->getNickname(), 'to test something')) {
//			echo 'Success';
//		} else {
//			echo 'Error occured';
//		}

//		Send love to all users
//		$love = new Sendlove_Love();
//		if ($love->sendLoveToAll($this->getSession()->nickname, 'simply because ...')) {
//			echo 'Success';
//		} else {
//			echo 'Error occured';
//		}

//		var_dump(Sendlove_Utilities::createPassword());

//		Send love to selected users
//		$love = new Sendlove_Love();
//		if ($love->sendLoveToSelected('Tom', 'testing again', array(1129, 1226))) {
//			echo 'Success';
//		} else {
//			echo 'Error occured';
//		}

//		Get received love from user
		$love = new Sendlove_Love();
		var_dump($love->getLoveReceivedAmount(1129, 15));
		
//		Get sent love from user
		$love = new Sendlove_Love();
		var_dump($love->getLoveSentAmount(1129, 15));

//		Get received messages from user
		$love = new Sendlove_Love();
		var_dump($love->getLoveReceivedMessages(1129, 15));
		
//		Get sent messages from user
		$love = new Sendlove_Love();
		var_dump($love->getLoveSentMessages(1129, 15));
	}
    
}

