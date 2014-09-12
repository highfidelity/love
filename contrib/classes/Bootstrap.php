<?php

class Bootstrap
{

	public function _initAddRoutes()
	{
		$this->application->addRoute('/.*?(javascript).*?/i', 'Javascript', '');
		$this->application->addRoute('/.*?(css).*?/i', 'Css', '');
		$this->application->addRoute('/.*?(image).*?/i', 'Image', '');
	}

}
