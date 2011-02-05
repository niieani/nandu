<?php
/**
 * Melody entity
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
		$this->hasMany('Nandu_Note as notes', array('local' => 'id', 'foreign' => 'melody_id'));
		$this->actAs('SoftDelete');
	}
	
	public function getNotesAsArray()
	{
		$notes = array();
		foreach ($this->notes as $note) {
			$notes[] = $note->pitch;
		}
		
		return $notes;
	}
	
	public function setNotesFromArray(array $notes) 
	{
		$this->notes->delete();
		foreach ($notes as $note) {
			$n = new Nandu_Note();
			$n->pitch = $note;
			$n->melody_id = $this->id;
			$n->save();
		}
		
	}

}
