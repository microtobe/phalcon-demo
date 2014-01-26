<?php

/**
 * Sets the error handler
 */
set_error_handler(function ($code, $error, $file, $line) {
    throw new ErrorException($error, $code, 0, $file, $line);

    return true;
});

/**
 * Using Phalcon\Debug on debug environment
 *
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Debug.html
 */
if (ENVIRONMENT !== PRODUCTION) {
    // $debug = new Phalcon\Debug();
    // $debug->listen();
}

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 *
 * @see http://docs.phalconphp.com/en/latest/reference/di.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_DI.html
 */
$di = new Phalcon\DI\FactoryDefault();

/**
 * Include the application routes
 *
 * @see http://docs.phalconphp.com/en/latest/reference/routing.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Router.html
 */
$di->setShared('router', function () {
    return config('routes');
});

/**
 * The URL component is used to generate all kind of urls in the application
 *
 * @see http://docs.phalconphp.com/en/latest/reference/url.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Url.html
 */
$di->setShared('url', function () use ($config) {
    $url = new Phalcon\Mvc\Url();
    $url->setBaseUri($config->url->baseUri);
    $url-> setStaticBaseUri($config->url->staticBaseUri);

    return $url;
});

/**
 * Setting up the view component
 *
 * @see http://docs.phalconphp.com/en/latest/reference/views.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_View.html
 */
$di->setShared('view', function () use ($config) {
    $view = new Phalcon\Mvc\View();
    $view->setViewsDir($config->view->viewsDir);

    $view->registerEngines(array(
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php',
    ));

    return $view;
});

/**
 * Create an database listener
 */
$di->setShared('dbListener', function () use ($di) {
    return new \DbListener();
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 *
 * @see http://docs.phalconphp.com/en/latest/reference/db.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Db_Adapter_Pdo_Mysql.html
 */
$di->setShared('db', function () use ($config, $di) {
    // Create an Mysql connection
    $connection = new Phalcon\Db\Adapter\Pdo\Mysql($config->database->toArray());

    // Create an EventsManager
    $eventsManager = new Phalcon\Events\Manager();

    // Listen all the database events
    $eventsManager->attach('db', $di->get('dbListener'));

    // Assign the events manager to the connection
    $connection->setEventsManager($eventsManager);

    return $connection;
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 *
 * @see http://docs.phalconphp.com/en/latest/reference/models.html#models-meta-data
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Model_MetaData.html
 */
$di->setShared('modelsMetadata', function () use ($config) {
    if (isset($config->models->metadata)) {
        $metaDataConfig = $config->models->metadata;
        $metadataAdapter = 'Phalcon\Mvc\Model\Metadata\\'.$metaDataConfig->adapter;

        return new $metadataAdapter();
    }

    return new Phalcon\Mvc\Model\Metadata\Memory();
});

/**
 * Start the session the first time some component request the session service
 *
 * @see http://docs.phalconphp.com/en/latest/reference/session.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Session.html
 */
$di->setShared('session', function () {
    $session = new Phalcon\Session\Adapter\Files();
    $session->start();

    return $session;
});

/**
 * Register the flash service with custom CSS classes
 *
 * @see http://docs.phalconphp.com/en/latest/reference/flash.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Flash.html
 */
$di->setShared('flash', function () {
    return new Phalcon\Flash\Direct(array(
        'error'   => 'alert alert-error',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
    ));
});

/**
 * We register the events manager
 *
 * @see http://docs.phalconphp.com/en/latest/reference/dispatching.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Dispatcher.html
 */
$di->setShared('dispatcher', function () use ($di) {
    // Create an EventsManager
    $eventsManager = new Phalcon\Events\Manager();

    // We listen for events in the dispatcher using the exceptions plugin
    $eventsManager->attach('dispatch:beforeException', new \ExceptionsPlugin($di));

    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});

/**
 * Register the default cache component
 *
 * @see http://docs.phalconphp.com/en/latest/reference/cache.html
 */
$di->setShared('cache', function () use ($config, $di) {
    // Cache the files for 2 days using a Data frontend
    $frontCache = new Phalcon\Cache\Frontend\Data($config->cache->frontendOptions->toArray());

    // Create the component that will cache "Data" to a "File" backend
    $cache = new Phalcon\Cache\Backend\File($frontCache, $config->cache->backendOptions->toArray());

    return $cache;
});
