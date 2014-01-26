<?php

/**
 * Register an autoloader
 *
 * @see http://docs.phalconphp.com/en/latest/reference/loader.html
 */
$loader = new Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader
    ->registerDirs($config->loader->dirs->toArray())
    ->registerNamespaces($config->loader->namespaces->toArray())
    ->registerPrefixes($config->loader->prefixes->toArray())
    ->register();
