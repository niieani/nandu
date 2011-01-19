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
 * @package    Blipoteka_Book
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Book entity
 *
 * @property integer $book_id Primary key
 * @property integer $type Type (actually: a model of distribution) of the book. May be owned or free.
 * @property integer $user_id Foreign key of user being provider of the book
 * @property integer $owner_id Foreign key of user being owner of the book
 * @property integer $holder_id Foreign key of user being current holder of the book
 * @property integer $status What's going on with the book? (awaits courier, being read, delivered etc.)
 * @property string $title Title of the book in Polish language
 * @property string $original_title Title of the book in original language
 * @property integer $city_id Foreign key of edition's city
 * @property integer $publisher_id Foreign key of book's publisher
 * @property integer $year Year of the book's edition
 * @property integer $pages Number of pages
 * @property string $isbn ISBN-10 or ISBN-13 number
 * @property string $description Description of the book
 * @property string $status_name Book status as string
 * @property string $type_name Book type as string
 * @property bool $auto_accept_requests Automatically accept borrow requests from any user
 * @property string $created_at Date and time the book was added to library
 * @property Blipoteka_User $user A user who provided the book
 * @property Blipoteka_User $owner A user who is the owner of the book
 * @property Blipoteka_User $holder A user who is the current holder of the book
 * @property Blipoteka_Publisher $publisher A publisher of the book
 * @property Doctrine_Collection $authors Author(s) of the book
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Book extends Void_Doctrine_Record {

	/**
	 * The book is available for lending
	 * @var integer
	 */
	const STATUS_AVAILABLE = 0;

	/**
	 * The book is borrowed and currently read by someone
	 * @var integer
	 */
	const STATUS_BORROWED = 1;

	/**
	 * The book has been requested, but needs courier assignment
	 * @var integer
	 */
	const STATUS_COURIER = 2;

	/**
	 * The book is being delivered by a courier
	 * @var integer
	 */
	const STATUS_DELIVERED = 3;

	/**
	 * The book has been lost (due to some accident)
	 * @var integer
	 */
	const STATUS_LOST = 4;

	/**
	 * The book is temporarily unavailable
	 * @var integer
	 */
	const STATUS_UNAVAILABLE = 5;

	/**
	 * Owner of this book wishes to get it back
	 * @var integer
	 */
	const TYPE_OWNED = 0;

	/**
	 * Owner of this book releases it to the public
	 * @var integer
	 */
	const TYPE_FREE = 1;

	/**
	 * Setup record, table name etc.
	 */
	public function setTableDefinition() {
		$this->setTableName('books');

		$this->hasColumn('book_id', 'integer', 4, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('type', 'integer', 1, array('notnull' => true, 'default' => self::TYPE_OWNED));
		$this->hasColumn('user_id', 'integer', 4, array('notnull' => false));
		$this->hasColumn('owner_id', 'integer', 4, array('notnull' => false));
		$this->hasColumn('holder_id', 'integer', 4, array('notnull' => false));
		$this->hasColumn('status', 'integer', 1, array('notnull' => true, 'default' => self::STATUS_AVAILABLE));
		$this->hasColumn('title', 'string', 256, array('notnull' => true));
		$this->hasColumn('original_title', 'string', 256, array('notnull' => false));
		$this->hasColumn('city_id', 'integer', 4, array('notnull' => false));
		$this->hasColumn('publisher_id', 'integer', 4, array('notnull' => true));
		$this->hasColumn('year', 'integer', 2, array('notnull' => false));
		$this->hasColumn('pages', 'integer', 2, array('notnull' => false));
		$this->hasColumn('isbn', 'string', 13, array('notnull' => false));
		$this->hasColumn('description', 'string', 2048, array('notnull' => false));
		$this->hasColumn('auto_accept_requests', 'boolean', null, array('notnull' => true, 'default' => false));
	}

	/**
	 * Set up relationships and behaviors
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp() {
		parent::setUp();
		// We assume each book has at least one author and we consider him/her a master (default) author.
		// If this author gets deleted, all books by him/her are deleted as well. Also, a book may have
		// many additional authors.
		$this->hasMany('Blipoteka_Author as authors', array(
			'local' => 'book_id',
			'foreign' => 'author_id',
			'refClass' => 'Blipoteka_Book_Author'
		));

		// Each book is provided by one user. NULL means unknown (deleted account, etc.)
		$this->hasOne('Blipoteka_User as user', array('local' => 'user_id', 'foreign' => 'user_id', 'onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL'));
		// Each book is possesed by one user. NULL means unknown (deleted account, etc.)
		$this->hasOne('Blipoteka_User as owner', array('local' => 'owner_id', 'foreign' => 'user_id', 'onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL'));
		// Each book may be held by one user. NULL means the book is not being borrowed
		$this->hasOne('Blipoteka_User as holder', array('local' => 'holder_id', 'foreign' => 'user_id', 'onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL'));

		// Each book may have one city when it was printed. NULL means unknown.
		$this->hasOne('Blipoteka_City as city', array('local' => 'city_id', 'foreign' => 'city_id', 'onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL'));

		// Each book entity must have an exactly one publisher.
		// If the publisher gets deleted, all books published by him/her are deleted as well.
		$this->hasOne('Blipoteka_Publisher as publisher', array('local' => 'publisher_id', 'foreign' => 'publisher_id', 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'));
		$this->actAs('Timestampable', array('updated' => array('disabled' => true)));

		// Add default record listeners
		$this->addListener(new Blipoteka_Book_Listener_Status());
		$this->addListener(new Blipoteka_Book_Listener_Type());
	}

	/**
	 * Set a type of the book.
	 *
	 * @param integer $type A type of book
	 * @param Blipoteka_User $user A user who tries to set a type of book
	 * @throws Blipoteka_Book_Exception
	 */
	public function setType($type, Blipoteka_User $user) {
		// Don't allow to change a type of a book from owned to free,
		// unless this user is an owner of the book
		if ($type === self::TYPE_FREE && $this->type === self::TYPE_OWNED) {
			if ($this->owner instanceof Blipoteka_User && $this->owner->equalsByPrimaryKey($user, true) === false) {
				throw new Blipoteka_Book_Exception('', Blipoteka_Book_Exception::ERR_TYPE_CHANGE_FORBIDDEN);
			}
		}
		$this->type = $type;
	}

	/**
	 * Get a list of book statuses.
	 * @return array
	 */
	public function getStatusList() {
		return Void_Util_Class::getConstants(get_class($this), 'STATUS');
	}

	/**
	 * Get a list of book types.
	 * @return array
	 */
	public function getTypeList() {
		return Void_Util_Class::getConstants(get_class($this), 'TYPE');
	}

	/**
	 * Check if given status is a valid one
	 * @return bool
	 */
	protected function isValidStatus($status) {
		return Void_Util_Class::isValidConstant(get_class($this), 'STATUS', $status, false);
	}

	/**
	 * Check if given type is a valid one
	 * @return bool
	 */
	protected function isValidType($type) {
		return Void_Util_Class::isValidConstant(get_class($this), 'TYPE', $type, false);
	}

	/**
	 * Check if saved data is right.
	 * Please note: restrictions applied here are not validators: any exception
	 * thrown below indicates incorrect usage of model, i.e., an application bug.
	 *
	 * @throws Doctrine_Record_Exception
	 * @see Doctrine_Record::preSave()
	 */
	public function preSave($event) {
		$invoker = $event->getInvoker();

		// One cannot be a current owner and a holder of a book at the same time.
		if ($invoker->owner_id !== null && $invoker->owner_id === $invoker->holder_id) {
			throw new Doctrine_Record_Exception("Owner and holder of a book can't be the same person", Doctrine_Core::ERR_CONSTRAINT);
		}
		// Check for a valid status
		if ($this->isValidStatus($invoker->status) === false) {
			throw new Doctrine_Record_Exception(sprintf("Tried to set invalid book status (%d)", $invoker->status), Doctrine_Core::ERR_CONSTRAINT);
		}
		// Check for a valid type
		if ($this->isValidType($invoker->type) === false) {
			throw new Doctrine_Record_Exception(sprintf("Tried to set invalid book type (%d)", $invoker->type), Doctrine_Core::ERR_CONSTRAINT);
		}
	}

	/**
	 * Preparation of a record.
	 * Please note: restrictions applied here are not validators: any exception
	 * thrown below indicates incorrect usage of model, i.e., an application bug.
	 *
	 * @throws Doctrine_Record_Exception
	 * @see Doctrine_Record::preInsert()
	 */
	public function preInsert($event) {
		$invoker = $event->getInvoker();

		// A book has to be added by some user
		if ($invoker->user_id === null) {
			throw new Doctrine_Record_Exception("Tried to add a book not bound to any user", Doctrine_Core::ERR_CONSTRAINT);
		}

		// At the moment of insertion, a book cannot be held by anyone
		if ($invoker->holder_id !== null) {
			throw new Doctrine_Record_Exception("Tried to add a book being held by somebody", Doctrine_Core::ERR_CONSTRAINT);
		}

		// When a book is added to a user's pool, he or she becomes an owner automatically
		$invoker->owner_id = $invoker->user_id;
	}

	/**
	 * Set up non-standard doctrine record validators
	 */
	protected function setUpValidators() {
		// Validate ISBN number
		$validators = array();
		$validators['isbn'] = new Zend_Validate_Isbn();
		$this->setColumnValidators('isbn', $validators);

		// Validate year. It's safe to assume no one will possess a book printed by 1900, is it?
		// Also, we check if a date of edition is not set in future.
		$validators = array();
		$validators['int'] = new Zend_Validate_Int();
		$validators['year'] = new Void_Validate_GreaterThanOrEqual(1900);
		$validators['past'] = new Void_Validate_LessThanOrEqual(date('Y'));
		$this->setColumnValidators('year', $validators);

		// Validate number of pages. We assume 16 pages minimum (it seems to be a reasonable
		// number; from a formal point of view, if it's less than 48 pages, it's a brochure).
		$validators = array();
		$validators['pages'] = new Void_Validate_GreaterThanOrEqual(16);
		$this->setColumnValidators('pages', $validators);
	}

}
