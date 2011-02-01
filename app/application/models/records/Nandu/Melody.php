<?php
/**
 * Note entity
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
 * @author Michał Buczek <michal@buczek.cc>
 *
 */
class Nandu_Melody extends Void_Doctrine_Record {

	/**
	 * Setup record, table name etc.
	 */
	public function setTableDefinition() {
		$this->setTableName('melodies');

		$this->hasColumn('spiecies_id', 'integer', 8, array('notnull' => true));
	}

	/**
	 * Set up relationships and behaviors
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp() {
		$this->hasOne('Nandu_Spiecies as spiecies', array('local' => 'spiecies_id', 'foreign' => 'id'));
	}

}
