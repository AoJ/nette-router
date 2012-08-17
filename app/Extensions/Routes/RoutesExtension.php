<?php

namespace Extensions\Routes;

use Nette\Http\Request;
use Nette\Application\Application;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RoutesExtension extends \Nette\Config\CompilerExtension
{

	public $defaults = array();


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig(array(
				'aliases' => array(),
				'translations' => array(),
				'defaultLang' => ''
			)
		);

		UrlResolve::$aliases = (array)$config['aliases'];
		UrlResolve::$translations = (array)$config['translations'];
		UrlResolve::$defaultLang = $config['defaultLang'];

		//create empty instance for storing static variables
		$builder->addDefinition('routesExtension.urlResolver')
			->setClass('Extensions\Routes\UrlResolve')
			->setAutowired(true);

	}

}