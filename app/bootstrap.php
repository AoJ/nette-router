<?php

/**
 * My Application bootstrap file.
 */
use Nette\Application\Routers\Route;


// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';


// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setDebugMode($configurator::AUTO);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
		->addDirectory(APP_DIR)
		->addDirectory(LIBS_DIR)
		->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();



\kinq\Appication\Routers\UrlResolve::$translations = array(
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


\kinq\Appication\Routers\UrlResolve::$aliases = array(
	'Testik' => 'Page:Default:test',
	'Nplik' => 'Page:default',
	'Ahoj' => 'Page:default',
	'test' => 'Page:wau'
);

$smartRouter = function($mask, $metadata)
{
	$new_route = new kinq\Application\Routers\FilterRoute($mask, $metadata);
	foreach ($metadata as $part => $value)
	{
		if (is_array($value) && in_array('TRANS', $value))
		{
			$new_route->addFilter($part,
				'\kinq\Appication\Routers\UrlResolve::urlToPresenter',
				'\kinq\Appication\Routers\UrlResolve::presenterToUrl'
			);
		}
	}
	return $new_route;
};


// Setup router
//TRANS řiká, která segment to má překládat...
$container->router[] = $smartRouter('<presenter>', array(
	'presenter' => array('TRANS')
));
$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
$container->router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');


// Configure and run the application!
$container->application->run();
