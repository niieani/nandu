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
 * @package    Blipoteka_Publisher
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Publisher entity
 *
 * @property integer $publisher_id Primary key
 * @property string $name Name of the publisher
 * @property string $url URL of a web page of the publisher
 * @property Doctrine_Collection $books Books published by the publisher
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Publisher extends Void_Doctrine_Record {

	/**
	 * Setup record, table name etc.
	 */
	public function setTableDefinition() {
		$this->setTableName('publishers');

		$this->hasColumn('publisher_id', 'integer', 4, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('name', 'string', 64, array('notnull' => true, 'unique' => true));
		$this->hasColumn('url', 'string', 128, array('notnull' => false, 'unique' => true, 'default' => null));
	}

	/**
	 * Set up relationships and behaviors
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp() {
		// Many books may have been published by this publisher
		$this->hasMany('Blipoteka_Book as books', array('local' => 'publisher_id', 'foreign' => 'book_id'));
	}

}
