<?php

/**
 * Blipoteka.pl
 *
 * LICENSE
 *
 * This source file is subject to the Simplified BSD License
 * that is bundled with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://blipoteka.pl/license
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to blipoteka@gmail.com so we can send you a copy immediately.
 *
 * @category   Blipoteka
 * @package    Blipoteka_Scripts
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

if (!defined('APPLICATION_DOCTRINE_SCRIPT')) {
	echo "This script should not be executed directly.\n";
	exit(0);
}

define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'cli');
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

include __DIR__ . DS . '..' . DS . 'public' . DS . 'initenv.php';

$bootstrap = $application->bootstrap('doctrine')->getBootstrap();
$doctrine = $bootstrap->getResource('doctrine');

Doctrine_Core::debug(true);

// PEAR style loading -- prevents Doctrine's premature ejaculation
$doctrineManager = $doctrine->getManager();
$doctrineManager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_PEAR);
$connectionOptions = $doctrineManager->getCurrentConnection()->getOptions();

echo sprintf("Environment: %s, DSN: '%s'\n", APPLICATION_ENV, $connectionOptions['dsn']);
