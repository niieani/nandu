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
 * User's friends reference class
 *
 * @property integer $user_id Foreign key of a user
 * @property integer $friend_id Foreign key of user's friend
 * @property Blipoteka_User $user A user
 * @property Blipoteka_User $friend A friend of a user
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_User_FriendRef extends Doctrine_Record {

	/**
	 * Setup record, table name etc.
	 */
	public function setTableDefinition() {
		$this->setTableName('users_friends');

		$this->hasColumn('user_id', 'integer', 4, array('primary' => true));
		$this->hasColumn('friend_id', 'integer', 4, array('primary' => true));
	}

	/**
	 * Set up relationships and behaviors
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp() {
		$this->hasOne('Blipoteka_User as user', array('local' => 'user_id', 'foreign' => 'user_id', 'onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
		$this->hasOne('Blipoteka_User as friend', array('local' => 'friend_id', 'foreign' => 'user_id', 'onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'));
	}

}
