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
 * @package    Blipoteka
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Generic Blipoteka application exception class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Exception extends Void_Exception {

	/**
	 * The constructor
	 *
	 * @param string $msg
	 * @param integer $code
	 * @param Exception $previous
	 */
	public function __construct($msg = '', $code = 0, Exception $previous = null) {
		// If exception message is empty and an error code is provided,
		// check, if a class has a custom method to get an error message.
		$msg = ($msg === '' ? $this->getErrorMessage($code) : $msg);

		parent::__construct($msg, (int) $code, $previous);
	}

	/**
	 * Return a custom error message defined by an exception class
	 *
	 * @param int Error code
	 * @return string Error message
	 */
	public function getErrorMessage($code = 0) {
		$code = (int) $code;
		return isset($this->_errorMessages[$code]) ? $this->_errorMessages[$code] : 'Unknown error';
	}
}