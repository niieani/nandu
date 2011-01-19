<?php

/**
 * Blipoteka.pl
 *
 * LICENSE
 *
 * This source file is subject to the Simplified BSD License that is
 * bundled with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://blipoteka.pl/license
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to blipoteka@gmail.com so we can send you a copy immediately.
 *
 * @category   Blipoteka
 * @package    Blipoteka_Tests
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

// Define application environment
define('APPLICATION_ENV', 'testing');

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public'. DIRECTORY_SEPARATOR . 'initenv.php';

// Bootstrap and run the application
try {
	$application->bootstrap();
} catch (Exception $e) {
	if (PHP_SAPI !== 'cli') header('Content-Type: text/plain; charset=UTF-8');
	exit('Application error: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL);
}
