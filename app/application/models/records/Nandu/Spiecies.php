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
class Nandu_Spiecies extends Void_Doctrine_Record {

	/**
	 * Setup record, table name etc.
	 */
	public function setTableDefinition() {
		$this->setTableName('spiecies');
    	
    	$this->hasColumn('name', 'string', 64, array('notnull' => true, 'default' => 'default', 'unique' => true));
        $this->hasColumn('user_id', 'integer', 8, array('notnull' => true, 'default' => 0));
        $this->hasColumn('tonality', 'integer', 8, array('notnull' => true, 'default' => 0));
        $this->hasColumn('tempo', 'integer', 8, array('notnull' => true, 'default' => 100));
        $this->hasColumn('instrument', 'integer', 8, array('notnull' => true, 'default' => 12));
        $this->hasColumn('reference_note', 'integer', 8, array('notnull' => true, 'default' => 60));
        $this->hasColumn('scale_type', 'integer', 8, array('notnull' => true, 'default' => 0));
        
//		$this->hasColumn('species_id', 'integer', 8, array('notnull' => true));
	}

	/**
	 * Set up relationships and behaviors
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp() {
		$this->hasMany('Nandu_Melody as melodies', array('local' => 'id', 'foreign' => 'spiecies_id'));
	}

}
