<?php

/**
 * My Application bootstrap file.
 */
use Nette\Application\Routers\Route;
use Extensions\Routes\SmartRoute;


// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';
require __DIR__ . '/shortcuts.php';


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

$configurator->onCompile[] = function($configurator, \Nette\Config\Compiler $compiler) {
        $compiler->addExtension('routes', new Extensions\Routes\RoutesExtension);
};


$container = $configurator->createContainer();


// Setup router
//ALIAS řiká, která segment to má překládat...
$container->router[] = new SmartRoute('<presenter>', array(
	'presenter' => array(SmartRoute::ALIAS)
));

$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
$container->router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');


// Configure and run the application!
$container->application->run();
