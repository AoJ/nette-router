<?php

//use Nette;

/**
 * Nette\Debug\barDump shortcut
 * @param mixed
 * @param string
 */
function _dBar($var, $title = NULL)
{
	\Nette\Diagnostics\Debugger::barDump($var, $title);
}


/**
 * Nette\Debug\barDump shortcut
 * dump to Bar all args
 * @param mixed
 * @param string
 */
function _dBars() {
	$args = func_get_args();
	foreach ($args as $arg) _dBar($arg);
}


/**
 * Nette\Debug\dump shortcut
 * dump all args
 * @param mixed
 * @return void
 */
function _d($var)
{
	$args = func_get_args();
	foreach($args as $arg) \Nette\Diagnostics\Debugger::dump($arg);
}

/**
 * dump + deie
 * @param mixed $var
 */
function _de($var) {
	_d($var);
	die();
}

function barDump($var, $title = NULL)
{
	return _dBar($var, $title);
}

function timer($name = null) {
	$t = \Nette\Diagnostics\Debugger::Timer($name);
	if ($t > 0) _dBar($t, $name);
	return $t;
}

function time_left($name) {
	_dBar(number_format((microtime(TRUE) - \Nette\Diagnostics\Debugger::$time) * 1000, 1, '.', ' '), $name);
}

/**
 * @deprecated
 * @param string $message zpráva
 * $param array $params parametry pro formátováno řetězce
 * @param int $count počitatelnost
 * @return string
 */
function _t($message, $params = false, $count = null) {
	return \Nette\Environment::getService('translator')->translateParams($message, $params, $count);
}

function _regex($name) {
	return Katrine\RegularExpressionsHelper::getRegex($name);
}