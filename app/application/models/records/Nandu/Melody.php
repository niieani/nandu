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
		$this->hasColumn('first_parent_id', 'integer', 8, array('notnull' => false));
		$this->hasColumn('second_parent_id', 'integer', 8, array('notnull' => false));
	}

	/**
	 * Set up relationships and behaviors
	 * @see Doctrine_Record::setUp()
	 */
	public function setUp() {
		$this->hasOne('Nandu_Spiecies as spiecies', array('local' => 'spiecies_id', 'foreign' => 'id'));
		$this->hasMany('Nandu_Note as notes', array('local' => 'id', 'foreign' => 'melody_id'));
		$this->hasOne('Nandu_Melody as firstParent', array('local' => 'first_parent_id', 'foreign' => 'id'));
		$this->hasOne('Nandu_Melody as secondParent', array('local' => 'second_parent_id', 'foreign' => 'id'));
		$this->actAs('SoftDelete');
	}
	
	public function getNotesAsArray()
	{
		
		$notesList = Doctrine_Query::create()
						->from('Nandu_Note')
						->where('melody_id = ?', $this->id)
						->execute();
						
		$notes = array();
		foreach ($notesList as $note) {
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

	public function getAudioFilename()
	{
		return Nandu_Melody_Manager::getInstance()->getMelodyAudioFilename($this);
	}
	
}
