<?php
namespace kinq\Appication\Routers;

use Nette\Object;
use Nette;
use Nette\Application\Request;

class UrlResolve extends Object
{

	/** @var string */
	public static $defaultLang = 'cs';

	/** @var array */
	public static $translations = array();

	/** @var array */
	public static $aliases = array();


	/**
	 * vygeneruje z presenteru segment url
	 * @static
	 * @param $presenter
	 * @param \Nette\Application\Request $request
	 * @return string
	 */
	public static function presenterToUrl($presenter, Request $request)
	{
		$lang = isset($request->parameters['lang']) ? $request->parameters['lang'] : self::$defaultLang;
		$presenter = self::presenterToAlias($presenter);

		return isset(self::$translations[$lang][$presenter]) ? self::$translations[$lang][$presenter] : $presenter;
	}


	/**
	 * Převede segment z url na presenter
	 * nejprve se ho pokusí přeložit, poté najít v aliasech
	 * @static
	 * @param string $url
	 * @param \Nette\Application\Request $request
	 * @return string
	 */
	public static function urlToPresenter($url, Request $request)
	{
		$url = strtolower($url);
		$lang = isset($request->parameters['lang']) ? $request->parameters['lang'] : self::$defaultLang;

		//translate url segment
		$table = array_flip(isset(self::$translations[$lang]) ? self::$translations[$lang] : array());
		$url = isset($table[$url]) ? $table[$url] : $url;

		//check alias url
		return self::aliasToPresenter($url);
	}


	/**
	 * převede při generování url presenter na jeho alias
	 * @static
	 * @param string $presenter
	 * @return string
	 */
	public static function presenterToAlias($presenter)
	{
		$aliases = array_flip(self::$aliases);
		if (isset($aliases[$presenter])) return $aliases[$presenter];

		//try alias only first chars
		/*foreach ($aliases as $alias) {
			if (($find = substr_replace($presenter, $alias, 0, strlen($alias))) !== $presenter)
				return $find;
		}*/
		return $presenter;
	}


	/**
	 * převede segment z url na jeho alias
	 * @static
	 * @param $urlAlias
	 * @return mixed
	 */
	public static function aliasToPresenter($urlAlias)
	{
		if (isset(self::$aliases[$urlAlias])) return self::$aliases[$urlAlias];

		/*foreach (self::$aliases as $alias) {
			if (($find = substr_replace($urlAlias, $alias, 0, strlen($alias))) !== $urlAlias)
				return $find;
		}*/
		return $urlAlias;
	}
}