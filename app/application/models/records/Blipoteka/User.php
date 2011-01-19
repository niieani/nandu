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
 * @package    Blipoteka_User
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * User entity
 *
 * @property integer $user_id Primary key
 * @property integer $city_id Foreign key of a city where user lives in
 * @property string $blip Blip account login associated with this one
 * @property string $email E-mail address
 * @property string $password Salted hash of user's password
 * @property string $name Last name and first name of user
 * @property string $log_date The date and time user last logged in
 * @property integer $lognum How many times user logged in
 * @property bool $is_active Is user's account active?
 * @property bool $auto_accept_requests Automatically accept all borrow requests from user's friends
 * @property bool $gender Gender of user (true for male, false for female, NULL for not specified)
 * @property string $token General usage SHA1 token holder (for activation, password reset etc.)
 * @property string $activated_at Date and time the account was activated by user
 * @property string $created_at Date and time the account was created
 * @property string $updated_at Date and time the record was updated
 * @property Blipoteka_City $city A city where user lives in
 * @property Doctrine_Collection $friends A collection of user's friends
 * @property Doctrine_Collection $books_provided These books were provided by this user
 * @property Doctrine_Collection $books_owned Books owned by this user
 * @property Doctrine_Collection $books_held Books currently held by this user
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_User extends Void_Doctrine_Record {

	/**
	 * Setup record, table name etc.
	 */
	public function setTableDefinition() {
		$this->setTableName('users');

		$this->hasColumn('user_id', 'integer', 4, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('city_id', 'integer', 4, array('notnull' => true));
		$this->hasColumn('blip', 'string', 30, array('notnull' => true, 'unique' => true));
		$this->hasColumn('email', 'string', 128, array('notnull' => true, 'unique' => true));
		$this->hasColumn('password', 'string', 128, array('notnull' => true));
		$this->hasColumn('name', 'string', 64, array('notnull' => true));
		$this->hasColumn('log_date', 'timestamp', null, array('notnull' => false));
		$this->hasColumn('log_num', 'integer', 4, array('notnull' => true, 'default' => 0));
		$this->hasColumn('is_active', 'boolean', null, array('default' => true, 'notnull' => true));
		$this->hasColumn('activated_at', 'timestamp', null, array('notnull' => false));
		$this->hasColumn('auto_accept_requests', 'boolean', null, array('default' => true, 'notnull' => true));
		$this->hasColumn('gender', 'boolean', null, array('notnull' => false));
		$this->hasColumn('token', 'string', 40, array('unique' => true, 'notnull' => false));
	}

	/**
	 * Set up relationships and behaviors
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp() {
		parent::setUp();
		// User may have many friends
		$this->hasMany('Blipoteka_User as friends', array(
			'local' => 'user_id',
			'foreign' => 'friend_id',
			'refClass' => 'Blipoteka_User_FriendRef',
			'equal' => true
		));

		// User may add many books to the system
		$this->hasMany('Blipoteka_Book as books_provided', array('local' => 'user_id', 'foreign' => 'user_id'));
		// User may be owner of many books
		$this->hasMany('Blipoteka_Book as books_owned', array('local' => 'user_id', 'foreign' => 'owner_id'));
		// User may be holder of many books
		$this->hasMany('Blipoteka_Book as books_held', array('local' => 'user_id', 'foreign' => 'holder_id'));

		// Each user lives in a city. We don't allow deletion of cities as long as any entity have this city assigned
		$this->hasOne('Blipoteka_City as city', array('local' => 'city_id', 'foreign' => 'city_id', 'onUpdate' => 'CASCADE', 'onDelete' => 'RESTRICT'));

		// FIXME: this Doctrine behaviour doesn't suit our needs very well -- actually,
		// we are interested only of user's triggered record updates (ie. updated_at
		// shouldn't be touched when, for example, we are increasing log_num)
		$this->actAs('Timestampable');
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
		$created_at = new Zend_Date($invoker->created_at);

		// Activation timestamp restrictions
		if ($invoker->activated_at !== null) {
			$activated_at = new Zend_Date($invoker->activated_at);
			// Make sure a date when book is received is later than a date when book was requested
			if ($activated_at->isEarlier($created_at, Zend_Date::DATES) === true) {
				throw new Doctrine_Record_Exception("The date of activation is earlier than the date of creation", Doctrine_Core::ERR_CONSTRAINT);
			}
		}

		// Logging in timestamp restrictions
		if ($invoker->log_date !== null) {
			$logged_at = new Zend_Date($invoker->log_date);
			// Make sure a date when book is received is later than a date when book was requested
			if ($logged_at->isEarlier($created_at, Zend_Date::DATES) === true) {
				throw new Doctrine_Record_Exception("The date of logging in is earlier than the date of creation", Doctrine_Core::ERR_CONSTRAINT);
			}
		}
	}

	/**
	 * Set up non-standard doctrine record validators
	 */
	protected function setUpValidators() {
		// Validate e-mail address
		$validators = array();
		$validators['email'] = new Void_Validate_Email(array('checkdns' => true));
		$this->setColumnValidators('email', $validators);
	}

}
