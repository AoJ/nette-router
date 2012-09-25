<?php

namespace Extensions\Routes;

use Nette\Http\Request;
use Nette\Application\Application;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RoutesExtension extends \Nette\Config\CompilerExtension
{

	public $defaults = array();


	public function loadConfiguration ()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig(
			array(
				'aliases' => array(),
				'translations' => array(),
				'defaultLang' => ''
			)
		);

		//create empty instance for storing static variables
		$builder->addDefinition('urlResolve')
				->setClass('Extensions\Routes\UrlResolve')
				->setFactory(
			'\Extensions\Routes\RoutesExtension::UrlResolveFactory',
			array($config['aliases'], $config['translations'], $config['defaultLang'])
		)
				->setAutowired(true)
				->addTag('run');
	}


	/**
	 * @static
	 * @param array $aliases
	 * @param array $translations
	 * @param array $defaultLang
	 * @return UrlResolve
	 */
	static function UrlResolveFactory ($aliases, $translations, $defaultLang)
	{
		$urlResolve = new UrlResolve();

		$urlResolve::$aliases = (array)$aliases;
		$urlResolve::$translations = (array)$translations;
		$urlResolve::$defaultLang = (string)$defaultLang;

		return $urlResolve;
	}

}