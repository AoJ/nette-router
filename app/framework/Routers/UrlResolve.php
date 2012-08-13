<?php
namespace kinq\Appication\Routers;
use 	Nette\Object
,		Nette
, 		Nette\Application\Request
,		kinq
;

class UrlResolve extends Object {

	protected static $defaultLang = 'cs';

	public static $translations = array(
		'cs' => array(
			'gods' => 'zbozi',
			'category' => 'kategorie',
			'test' => 'kontakt',
			'Kamaradi' => 'Aoj'
		),
		'en' => array(
			'gods' => 'gods',
			'test' => 'contanct',
			'Friends' => 'Aoj'
		),
	);

	public static $aliases = array(
            'Testik' => 'Page:Default:test',
			'Nplik' => 'Page:default',
			'Ahoj' => 'Page:default',
			'test' => 'Page:test'
        );


	public static function presenterToUrl($presenter, Request $request) {
		$lang = isset($request->parameters['lang']) ? $request->parameters['lang'] : self::$defaultLang;
		$presenter = self::presenterToAlias($presenter);

		if (!isset(self::$translations[$lang])) return $presenter;
		$table = array_flip(self::$translations[$lang]);
		return isset($table[$presenter]) ? $table[$presenter] : $presenter;
	}

	public static function urlToPresenter($url, Request $request) {

		//translate url segment
		$lang = isset($request->parameters['lang']) ? $request->parameters['lang'] : self::$defaultLang;
		$url = isset(self::$translations[$lang][$url]) ? self::$translations[$lang][$url] : $url;

		//check alias url
		return self::aliasToPresenter($url);
	}

	public static function presenterToAlias($presenter) {
		$aliases = array_flip(self::$aliases);
		if(isset($aliases[$presenter])) return $aliases[$presenter];

		//try alias only first chars
		/*foreach ($aliases as $alias) {
			if (($find = substr_replace($presenter, $alias, 0, strlen($alias))) !== $presenter)
				return $find;
		}*/
		return $presenter;
	}

	public static function aliasToPresenter($urlAlias) {
		if(isset(self::$aliases[$urlAlias])) return self::$aliases[$urlAlias];

		/*foreach (self::$aliases as $alias) {
			if (($find = substr_replace($urlAlias, $alias, 0, strlen($alias))) !== $urlAlias)
				return $find;
		}*/
		return $urlAlias;
	}
}