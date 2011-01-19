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

$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];

if ($argc < 2) {
	printf("Usage: %s <email> \n", $argv[0]);
	exit(-1);
}

$application->bootstrap('mail')->getBootstrap();

$email = $argv[1];
$email = trim($email);

$mail = new Zend_Mail('UTF-8');
$mail->addTo($email);
$mail->setBodyText('Testowa wiadomość z serwisu Blipoteka.pl, nadana dnia ' . new Zend_Date(). '.', 'UTF-8');
$mail->setSubject('Test poczty z serwisu Blipoteka');
$mail->send();
