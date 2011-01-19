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
 * @package    Blipoteka_City
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * City entity
 *
 * @property integer $city_id Primary key
 * @property string $name City name
 * @property string $asciiname City name in ASCII (no national characters)
 * @property double $lat Latitude
 * @property double $lng Longitude
 * @property string $feature_code GeoNames feature code
 * @property string $admin1_code A first part of GeoNames administrative code
 * @property string $admin1_code A second part of GeoNames administrative code
 * @property string $modified_at Date of the last update of the data
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_City extends Void_Doctrine_Record {

	/**
	 * Setup record, table name etc.
	 */
	public function setTableDefinition() {
		$this->setTableName('cities');

		$this->hasColumn('city_id', 'integer', 4, array('primary' => true, 'autoincrement' => false));
		$this->hasColumn('name', 'string', 64, array('notnull' => true));
		$this->hasColumn('asciiname', 'string', 64, array('notnull' => true));
		$this->hasColumn('lat', 'decimal', 9, array('notnull' => true, 'scale' => 5));
		$this->hasColumn('lng', 'decimal', 9, array('notnull' => true, 'scale' => 5));
		$this->hasColumn('feature_code', 'string', 10, array('notnull' => true));
		$this->hasColumn('admin1_code', 'string', 20, array('notnull' => true));
		$this->hasColumn('admin2_code', 'string', 80, array('notnull' => false));
		$this->hasColumn('modified_at', 'date', null, array('notnull' => true));
	}

	/**
	 * Set up relationships and behaviors
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp() {
		// Many users may reside in one city
		$this->hasMany('Blipoteka_User as users', array('local' => 'city_id', 'foreign' => 'user_id'));
		// Many books may have been printed in this city
		$this->hasMany('Blipoteka_Book as books', array('local' => 'city_id', 'foreign' => 'book_id'));
	}

}
