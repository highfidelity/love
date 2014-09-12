<?php

class Zend_Controller_Action_Helper_Api extends Zend_Controller_Action_Helper_Abstract
{
	public function direct(array $post = array(), $uri = null)
	{
		$client = new Zend_Http_Client();
    	$client->setMethod(Zend_Http_Client::POST);
		$client->setParameterPost($post);
		$client->setUri($uri);
		return $client->request()->getBody();
	}

	public function endpoint($name)
	{
		return Zend_Controller_Action_HelperBroker::getStaticHelper('config')->direct('api')->$name->endpoint;
	}

	public function key($name)
	{
		return Zend_Controller_Action_HelperBroker::getStaticHelper('config')->direct('api')->$name->key;
	}

	public function name($name)
	{
		return Zend_Controller_Action_HelperBroker::getStaticHelper('config')->direct('api')->$name->name;
	}

}
