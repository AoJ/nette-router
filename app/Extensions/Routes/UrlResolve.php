<?php

namespace Extensions\Routes;

use Nette\Object;
use Nette;
use Nette\Application\Request;
use Nette\Utils\Strings;

class UrlResolve extends Object
{
	CONST LANG_KEY = 'lang';

	/** @var string */
	public static $defaultLang = 'cs';

	/** @var array */
	public static $translations = array();

	/** @var array */
	public static $aliases = array();


	/**
	 * vygeneruje z presenteru segment url
	 * Site:default > uvodni-stranka
	 * @static
	 * @param $link
	 * @param \Nette\Application\Request $request
	 * @return string
	 */
	public static function linkToUrl($link, Request $request)
	{
		$lang = self::getLang($request);
		$alias = self::linkToAlias($link);

		return self::translateLink($lang, $alias);
	}


	/**
	 * Převede segment z url na presenter
	 * nejprve se ho pokusí přeložit, poté najít v aliasech
	 * uvodni-stranka > Site:default
	 * @static
	 * @param string $url
	 * @param \Nette\Application\Request $request
	 * @return string
	 */
	public static function urlToLink($url, Request $request)
	{
		//translate url segment
		$url = self::translateUrl(self::getLang($request), $url);

		//check alias url
		return self::aliasToLink($url);
	}


	/**
	 * převede při generování url presenter na jeho alias
	 * Site:default > contact
	 * @static
	 * @param string $link
	 * @return string
	 */
	public static function linkToAlias($link)
	{
		foreach (self::$aliases as $alias => &$segment)
		{
			if($segment == $link) return $alias;
		}

		return $link;
	}


	/**
	 * převede segment z url na jeho alias
	 * contact > Site:default
	 * @static
	 * @param $urlAlias
	 * @return mixed
	 */
	public static function aliasToLink($urlAlias)
	{
		$lower = strtolower($urlAlias);
		foreach (self::$aliases as $trans => &$transLink)
		{
			if (strtolower($trans) == $lower) return $transLink;
		}
		return $urlAlias;
	}


	/**
	 * translate internal url to localized url
	 * contact > kontakt
	 * @static
	 * @param string $lang
	 * @param string $link
	 * @return string
	 */
	protected static function translateLink($lang, $link)
	{
		$table = isset(self::$translations[$lang]) ? self::$translations[$lang] : array();
		foreach ($table as $trans => &$transLink)
		{
			if ($transLink == $link) return $trans;
		}
		return $link;
	}


	/**
	 * translate localized url to internal
	 * kontakt > contact
	 * @static
	 * @param string $lang
	 * @param string $url
	 * @return string
	 */
	protected static function translateUrl($lang, $url)
	{
		$table = isset(self::$translations[$lang]) ? self::$translations[$lang] : array();

		$segment = strtolower($url);
		return isset($table[$segment]) ? $table[$segment] : $url;
	}


	/**
	 * @static
	 * @param Request $request
	 * @return string
	 */
	protected static function getLang(Request $request)
	{
		return isset($request->parameters[self::LANG_KEY]) ? $request->parameters[self::LANG_KEY] : self::$defaultLang;
	}
}