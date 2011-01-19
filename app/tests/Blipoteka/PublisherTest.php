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

/**
 * Publisher entity test case
 *
 * @publisher Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_PublisherTest extends PHPUnit_Framework_TestCase {
	const PUBLISHER_NAME = 'Example Publisher';
	const PUBLISHER_URL = 'http://www.examplepublisheraddress.com/';

	/**
	 * @var Blipoteka_Publisher
	 */
	private $publisher;

	/**
	 * Set up an example book with minimum information required
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		$publishers = Doctrine_Core::getTable('Blipoteka_Publisher')->findAll();
		$publishers->delete();
		$this->publisher = new Blipoteka_Publisher();
		$this->publisher->name = self::PUBLISHER_NAME;
		$this->publisher->url = self::PUBLISHER_URL;
	}

	/**
	 * We expect a normal creation of a record.
	 */
	public function testSave() {
		$this->publisher->save();
		$this->assertTrue($this->publisher->exists());
	}

	/**
	 * There can not be two publishers of the same name
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testNameUniqueness() {
		$this->publisher->save();
		$publisher = new Blipoteka_Publisher();
		$publisher->name = self::PUBLISHER_NAME;
		$publisher->save();
	}

	/**
	 * There can not be two publishers of the same URL
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testUrlUniqueness() {
		$this->publisher->save();
		$publisher = new Blipoteka_Publisher();
		$publisher->url = self::PUBLISHER_NAME;
		$publisher->save();
	}

	/**
	 * Too long name should result with a validation error
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testNameLength() {
		$this->publisher->name = str_repeat('x', 64 + 1);
		$this->publisher->save();
	}

	/**
	 * Too long URL should result with a validation error
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testUrlLength() {
		$this->publisher->url = str_repeat('x', 128 + 1);
		$this->publisher->save();
	}

}
