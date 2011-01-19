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
 * Book history entry test case
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Book_HistoryTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var Zend_Date
	 */
	private $requested_at;

	/**
	 * @var Zend_Date
	 */
	private $received_at;

	/**
	 * @var Blipoteka_Book_History
	 */
	private $history;

	/**
	 * Initialization.
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		$this->requested_at = new Zend_Date();
		$this->received_at = new Zend_Date();
		$this->received_at->addDay(14)->addHour(1)->addMinute(15);

		$this->history = new Blipoteka_Book_History();
		$this->history->borrower_id = 1;
		$this->history->lender_id = 2;
		$this->history->book_id = 1;
		$this->history->requested_at = $this->requested_at->get(Zend_Date::W3C);
		$this->history->received_at = $this->received_at->get(Zend_Date::W3C);
	}

	/**
	 * We expect lender_id and borrower_id to have different values.
	 * @expectedException Doctrine_Record_Exception
	 */
	public function testConstraintUsers() {
		$this->history->lender_id = $this->history->borrower_id;
		$this->history->save();
	}

	/**
	 * We expect received_at to be a later date than requested_at
	 * @expectedException Doctrine_Record_Exception
	 */
	public function testConstraintTimestamps() {
		$this->history->received_at = $this->history->requested_at;
		$this->history->save();
	}

	/**
	 * Test entry with receival date specified.
	 */
	public function testEntryReceived() {
		$this->history->save();
		$this->assertTrue($this->history->exists());
	}

	/**
	 * Test entry without receival date specified.
	 */
	public function testEntryNotReceived() {
		$this->history->received_at = null;
		$this->history->save();
		$this->assertTrue($this->history->exists());
	}

}