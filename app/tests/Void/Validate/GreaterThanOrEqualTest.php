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
 * @package    Void_Tests
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Greater than or equal validator test case
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Validate_GreaterThanOrEqualTest extends PHPUnit_Framework_TestCase {

	/**
	 * Equal value is considered valid.
	 */
	public function testEquality() {
		$value = 5;
		$validator = new Void_Validate_GreaterThanOrEqual(5);
		$this->assertTrue($validator->isValid($value));
	}

	/**
	 * Greater value is considered valid.
	 */
	public function testGreaterThan() {
		$value = 6;
		$validator = new Void_Validate_GreaterThanOrEqual(5);
		$this->assertTrue($validator->isValid($value));
	}

	/**
	 * Lower value is considered invalid.
	 */
	public function testLessThan() {
		$value = 4;
		$validator = new Void_Validate_GreaterThanOrEqual(5);
		$this->assertFalse($validator->isValid($value));
	}

}
