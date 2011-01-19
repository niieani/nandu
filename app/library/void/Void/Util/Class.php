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
 * @package    Void_Util
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * A generic class utilities.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Util_Class {

	/**
	 * Check if a given class constant value is valid.
	 *
	 * @param string $class A class
	 * @param string $prefix A constant prefix
	 * @param mixed $value A value
	 * @param bool $strict If true, make a type check when comparing values
	 *
	 * @return bool
	 */
	public static function isValidConstant($class, $prefix, $value, $strict = true) {
		$reflection = new ReflectionClass($class);
		$constants = $reflection->getConstants();
		foreach ($constants as $name => $constant) {
			if (substr($name, 0, strlen($prefix)) === $prefix && ($strict ? $value === $constant : $value == $constant)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get class constants (optionally filtered by
	 *
	 * @param string $class A class
	 * @param string $prefix A constant prefix
	 * @param mixed $value A value
	 * @param bool $strict If true, make a type check when comparing values
	 *
	 * @return bool
	 */
	public static function getConstants($class, $prefix = '') {
		$reflection = new ReflectionClass($class);
		$constants = $reflection->getConstants();
		// Return all class constants if no prefix given
		if ($prefix === '') return $constants;
		// Filter constants by prefix
		foreach ($constants as $name => $constant) {
			if (substr($name, 0, strlen($prefix)) !== $prefix) {
				unset($constants[$name]);
			}
		}
		return $constants;
	}
}
