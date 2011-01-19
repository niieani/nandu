<?php

/**
 * Void
 *
 * LICENSE
 *
 * This source file is subject to the Simplified BSD License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://tekla.art.pl/license/void-simplified-bsd-license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to argasek@gmail.com so I can send you a copy immediately.
 *
 * @category   Void
 * @package    Void
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * An abstract class singleton implementation for PHP 5.3.
 *
 * @author Andrea Giammarchi <andrea.giammarchi@gmail.com>
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
abstract class Void_Singleton {
	/**
	 * Singleton instance (actually a child of Void_Singleton, not Void_Singleton instance itself).
	 * It has to be redefined in a child class to make it work properly!
	 *
	 * @var Void_Singleton
	 */
	protected static $_instance;

	final private function __construct() {
		// If called twice, throw an Exception
		if (static::$_instance !== null) {
			throw new Void_Exception("An instance of " . get_called_class() . " already exists.");
		}

		// Init method via magic static keyword ($this injected)
		static::init();
	}

	/**
	 * No clone allowed, both internally and externally
	 *
	 * @throws Void_Exception
	 */
	final private function __clone() {
		throw new Void_Exception("An instance of " . get_called_class() . " is a singleton object and cannot be cloned.");
	}

	/**
	 * The common sense method to retrieve the instance
	 */
	final public static function getInstance() {
		// Ternary operator is that fast!
		return static::$_instance !== null ? static::$_instance : static::$_instance = new static();
	}

	/**
	 * Constructor-like method replacement. It has to be implemented
	 * by each inheriting class on it's own.
	 *
	 */
	abstract protected function init();

}
