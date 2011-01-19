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
 * @package    Blipoteka_Author
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Author entity
 *
 * @property integer $author_id Primary key
 * @property string $name Surname and name of the author
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Author extends Void_Doctrine_Record {

	/**
	 * Setup record, table name etc.
	 */
	public function setTableDefinition() {
		$this->setTableName('authors');

		$this->hasColumn('author_id', 'integer', 4, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('name', 'string', 64, array('notnull' => true, 'unique' => true));
	}

	/**
	 * Set up relationships and behaviors
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp() {
		// An author may have written many books
		$this->hasMany('Blipoteka_Book as books', array(
			'local' => 'author_id',
			'foreign' => 'book_id',
			'refClass' => 'Blipoteka_Book_Author'
		));
	}

}
