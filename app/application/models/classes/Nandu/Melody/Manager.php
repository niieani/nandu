<?php 
class Nandu_Melody_Manager
{
	const MELODY_CLASS 		= 'Nandu_Melody';
	const NOTE_CLASS 		= 'Nandu_Note';
	const SPECIES_CLASS 	= 'Nandu_Species';

	protected $_tables = array();

	protected $_queries = array();

	/**
	 * Request object
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request;
	
	/**
	 * @var Zend_Config
	 */
	protected $_settings;
	
	/**
	 * @var Nandu_Genetic_Operations
	 */
	protected $_operations;

	/**
	 * @var Nandu_Melody_Manager
	 */
	protected static $_instance;
	
	/**
	 * @return Nandu_Melody_Manager
	 */
	public static function getInstance()
	{
		if ( ! self::$_instance) {
			self::$_instance = new Nandu_Melody_Manager();
		}
		
		return self::$_instance;
	}
	
	public function __construct(Zend_Controller_Request_Abstract $request = null)
	{
		if (!$request instanceof Zend_Controller_Request_Abstract) {
			$request = Zend_Controller_Front::getInstance()->getRequest();
		}
		$this->setRequest($request);
		$this->_operations = new Nandu_Genetic_Operations();
		$this->_settings = Zend_Registry::get('settings');
	}
	
	/**
	 * Get table object
	 * @param string $class
	 * @return Doctrine_Table
	 */
	public function getTable($class)
	{
		if ( ! isset($this->_tables[$class])) {
			$this->_tables[$class] = Doctrine_Core::getTable($class);
		}

		return $this->_tables[$class];
	}

	/**
	 * @return Doctrine_Table
	 */
	public function getMelodyTable()
	{
		return $this->getTable(self::MELODY_CLASS);
	}

	/**
	 * @return Doctrine_Table
	 */
	public function getNoteTable()
	{
		return $this->getTable(self::NOTE_CLASS);
	}

	/**
	 * @return Doctrine_Table
	 */
	public function getSpeciesTable()
	{
		return $this->getTable(self::SPECIES_CLASS);
	}

	/**
	 * @return Doctrine_Query
	 */
	public function getQuery($class)
	{
		if ( ! isset($this->_queries[$class])) {
			$this->_queries[$class] = $this->getTable($class)->createQuery();
		}
		return clone $this->_queries[$class];
	}

	/**
	 * @return Doctrine_Query
	 */
	public function getMelodyQuery()
	{
		return $this->getQuery(self::MELODY_CLASS);
	}

	/**
	 * @return Doctrine_Query
	 */
	public function getNoteQuery()
	{
		return $this->getQuery(self::NOTE_CLASS);
	}

	/**
	 * @return Doctrine_Query
	 */
	public function getSpeciesQuery()
	{
		return $this->getQuery(self::SPECIES_CLASS);
	}

	/**
	 * @return Zend_Controller_Request_Abstract
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	public function setRequest(Zend_Controller_Request_Abstract $request)
	{
		$this->_request = $request;
	}

	public function getMelodyIdFromRequest()
	{
		return $this->getRequest()->getParam('melodyId', 0);
	}

	/**
	 * Get category
	 * @param integer $id
	 * @return Nandu_Melody
	 */
    public function getMelody($id = null)
    {
		if ($id === null) {
			$id = $this->getMelodyIdFromRequest();
		}

		$melody = $this->getMelodyTable()->find($id);

		if ($melody instanceof Nandu_Melody) {
			return $melody;
		} else {
			throw new Blipoteka_Exception(sprintf('Melody #%d not found.', $id));
		}
    }
	public function getSpeciesIdFromRequest()
	{
		return $this->getRequest()->getParam('speciesId', 0);
	}

	/**
	 * Get species
	 * @param integer $id
	 * @return Nandu_Species
	 */
    public function getSpecies($id = null)
    {
		if ($id === null) {
			$id = $this->getSpeciesIdFromRequest();
		}

		$melody = $this->getSpeciesTable()->find($id);

		if ($melody instanceof Nandu_Species) {
			return $melody;
		} else {
			throw new Blipoteka_Exception(sprintf('Species %d not found.', $id, $class));
		}
    }
    public function createMelody(array $notes, Nandu_Melody $firstParent = null, Nandu_Melody $secondParent = null)
    {
    	$melody = new Nandu_Melody();
    	$melody->species_id = $firstParent->species_id;
    	$melody->firstParent = $firstParent;
    	$melody->secondParent = $secondParent;
    	$melody->save();
    	$melody->setNotesFromArray($notes);
    	return $melody;	
    }
    
    public function mutate(Nandu_Melody $melody)
    {
    	$notes = $melody->getNotesAsArray();
    	$newNotes = $this->_operations->mutate($notes);
    	return $this->createMelody($newNotes, $melody);
    }
    
    public function getMelodyAudioFilename(Nandu_Melody $melody)
    {
    	$dir = $this->_settings->music->generator->path;
    	$audio = new Nandu_MIDIfy_AuralizeNumbers($melody->getNotesAsArray(), $dir);
    	return $audio->getFilename();
    }
    
    protected function _performMutation()
    {
    	$number = rand(1, $this->_settings->genetic->mutate->rate);
    	return 1 == $number;
    }
    
    public function evolve(Nandu_Melody $a, Nandu_Melody $b = null)
    {
    	if ( ! $b) {
    		$b = $this->getRandomMelody($a->species, $a->id);
    	}
    	
    	$notesA = $a->getNotesAsArray();
    	$notesB = $b->getNotesAsArray();
    	
    	list($notesNewA, $notesNewB) = $this->_operations->crossOver($notesA, $notesB);
    	
		$newA = $this->createMelody($notesNewA, $a, $b);
		$newB = $this->createMelody($notesNewB, $a, $b);
    	
		
		
    	return array($newA, $newB);
    }
    
    public function vote(Nandu_Melody $better, Nandu_Melody $worse)
    {
    	$worse->delete();
    	
    	$this->evolve($better);
    	
    	if ($this->_performMutation()) {
    		$second = $this->mutate($better);
    	} else {
    		$second = $this->getRandomMelody($better->species, $better->id);
    	}
    	
    	return array($better, $second);
    }
    
    public function getRandomMelody(Nandu_Species $species, $bannedId = false)
    {
    	$q  = $this->getMelodyQuery()
    				->where('species_id = ?', $species->id)
    				->andWhere('deleted_at IS NULL')
    				->orderBy('RAND()')
    				->limit(2);

    	if ($bannedId) {
    		$q->andWhere('id != ?', $bannedId);
    	}
    				
		return $q->fetchOne();
    }
    
    public function getPair(Nandu_Species $species)
    {
    	$a = $this->getRandomMelody($species);
    	$b = $this->getRandomMelody($species, $a->id);
    	return array($a, $b);
    }
    
    public function initPopulation(Nandu_Species $species = null, $count = 8)
    {
    	
    	if (null == $species) {
    		$species = new Nandu_Species();
    		$species->name = "test";
    		$species->save();
    	}
    	
    	$music = new Nandu_Music_Theory();
    	
    	for($i = 0; $i < $count; $i++) {
    		$melody = new Nandu_Melody();
    		$melody->species_id = $species->id;
    		$melody->save();
    		$notes = $music->melodyGen(Nandu_Music_MusicScales::Major(), 2, 8, 16);
    		$melody->setNotesFromArray($notes);
    	}
    	
    	return $species;
    }

}