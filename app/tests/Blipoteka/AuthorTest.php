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
 * Author entity test case
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_AuthorTest extends PHPUnit_Framework_TestCase {
	const AUTHOR_NAME = 'Example Author';

	/**
	 * @var Blipoteka_Author
	 */
	private $author;

	/**
	 * Set up an example book with minimum information required
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		$authors = Doctrine_Core::getTable('Blipoteka_Author')->findAll();
		$authors->delete();
		$this->author = new Blipoteka_Author();
		$this->author->name = self::AUTHOR_NAME;
	}

	/**
	 * We expect a normal creation of a record.
	 */
	public function testSave() {
		$this->author->save();
		$this->assertTrue($this->author->exists());
	}

	/**
	 * There can not be two authors of the same name
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testUniqueness() {
		$this->author->save();
		$author = new Blipoteka_Author();
		$author->name = self::AUTHOR_NAME;
		$author->save();
	}

	/**
	 * Too long name should result with a validation error
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testNameLength() {
		$this->author->name = str_repeat('x', 64 + 1);
		$this->author->save();
	}

}
