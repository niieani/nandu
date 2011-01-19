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
 * Book entity test case
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_BookTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var Blipoteka_Book
	 */
	private $book;

	/**
	 * Set up an example book with minimum information required
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		$this->book = new Blipoteka_Book();
		$this->book->title = 'Przykładowa, bardzo interesująca książka';
		$this->book->city_id = 756135;
		$this->book->publisher_id = 1;
		$this->book->user_id = 1;
	}

	/**
	 * We expect user_id to be not NULL when inserting.
	 * @expectedException Doctrine_Record_Exception
	 */
	public function testPreInsertUserIdNotNull() {
		$this->book->user_id = null;
		$this->book->save();
	}

	/**
	 * We expect holder_id to be NULL when inserting.
	 * @expectedException Doctrine_Record_Exception
	 */
	public function testPreInsertHolderIdNull() {
		$this->book->holder_id = 2;
		$this->book->save();
	}

	/**
	 * We expect owner_id to be set to the same value as user_id when inserting (and after insertion, too).
	 * @depends testPreInsertUserIdNotNull
	 */
	public function testPreInsertOwnerEqualsUser() {
		$this->book->save();
		$this->assertEquals($this->book->user_id, $this->book->owner_id, 'A book after insertion should have the same user and owner');
	}

	/**
	 * We expect holder_id to be not the same as owner_id (unless owner_id is NULL) when inserting or updating.
	 * @expectedException Doctrine_Record_Exception
	 */
	public function testPreSaveNotNullOwnerCannotEqualHolder() {
		$this->book->holder_id = 1;
		$this->book->save();
	}

	/**
	 * We expect holder_id can be the same as owner_id when updating only when owner_id is NULL.
	 * @depends testPreInsertOwnerEqualsUser
	 */
	public function testPreSaveNullOwnerCanEqualHolder() {
		$this->book->save();
		$this->book->owner_id = null;
		$this->book->holder_id = null;
		$this->book->save();
		$this->assertNull($this->book->owner_id);
		$this->assertNull($this->book->holder_id);
	}

	/**
	 * A status should be valid
	 * @expectedException Doctrine_Record_Exception
	 */
	public function testPreSaveValidStatus() {
		$this->book->status = -1;
		$this->book->save();
	}

	/**
	 * A type should be valid
	 * @expectedException Doctrine_Record_Exception
	 */
	public function testPreSaveValidType() {
		$this->book->type = -1;
		$this->book->save();
	}

	/**
	 * A title length should be valid
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testValidateTitleLength() {
		$this->book->title = str_repeat('x', 512);
		$this->book->save();
	}

	/**
	 * A year must be 1900 or greater
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testValidateMinYear() {
		$this->book->year = 1899;
		$this->book->save();
	}

	/**
	 * A year must be a current year or earlier
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testValidateMaxYear() {
		$this->book->year = (integer) date('Y') + 1;
		$this->book->save();
	}

	/**
	 * A number of pages must be 16 or greater
	 * @expectedException Doctrine_Validator_Exception
	 */
	public function testValidateMinPages() {
		$this->book->pages = 15;
		$this->book->save();
	}

	/**
	 * Book type change from OWNED to FREE is forbidden
	 * in case when user is not an owner of the book.
	 *
	 * @expectedException Blipoteka_Book_Exception
	 */
	public function testTypeChangeOwnedToFreeMismatchedOwner() {
		$this->book->owner_id = 1;
		$this->book->type = Blipoteka_Book::TYPE_OWNED;
		$this->book->save();

		$user = new Blipoteka_User();
		$user->user_id = 2;

		$this->book->setType(Blipoteka_Book::TYPE_FREE, $user);
	}

	/**
	 * Book type change from FREE to OWNED is always allowed.
	 */
	public function testTypeChangeFreeToOwnedMismatchedOwner() {
		$this->book->owner_id = 1;
		$this->book->type = Blipoteka_Book::TYPE_FREE;
		$this->book->save();

		$user = new Blipoteka_User();
		$user->user_id = 2;

		$this->book->setType(Blipoteka_Book::TYPE_OWNED, $user);
	}

	/**
	 * Book type change should work without problem in
	 * either direction when owners match.
	 */
	public function testTypeChangeOwnedToFree() {
		$this->book->owner_id = 1;
		$this->book->type = Blipoteka_Book::TYPE_OWNED;
		$this->book->save();

		$user = new Blipoteka_User();
		$user->user_id = 1;

		// Same user, type OWNED -> FREE
		$this->book->setType(Blipoteka_Book::TYPE_FREE, $user);
		$this->book->save();
		$this->assertEquals($this->book->type, Blipoteka_Book::TYPE_FREE);

	}

	/**
	 * Book type change should work without problem in
	 * either direction when owners match.
	 */
	public function testTypeChangeFreeToOwned() {
		$this->book->owner_id = 1;
		$this->book->type = Blipoteka_Book::TYPE_FREE;
		$this->book->save();

		$user = new Blipoteka_User();
		$user->user_id = 1;

		// Same user, type FREE -> OWNED
		$this->book->setType(Blipoteka_Book::TYPE_OWNED, $user);
		$this->book->save();
		$this->assertEquals($this->book->type, Blipoteka_Book::TYPE_OWNED);
	}

}
