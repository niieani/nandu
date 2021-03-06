#!/usr/bin/env php
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

define('APPLICATION_DOCTRINE_SCRIPT', true);

include __DIR__ . DIRECTORY_SEPARATOR . 'doctrine-console-common.php';

$defaultGeoNamesPath = APPLICATION_PATH . DS . 'models' . DS . 'fixtures' . DS . 'geonames' . DS;

$city = new Blipoteka_City();
$script = new Blipoteka_Scripts_GeoNames($city, $defaultGeoNamesPath);
$script->run();
