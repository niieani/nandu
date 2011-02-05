<?php 
class Nandu_Melody_Manager
{
	const MELODY_CLASS 		= 'Nandu_Melody';
	const NOTE_CLASS 		= 'Nandu_Note';
	const SPIECIES_CLASS 	= 'Nandu_Spiecies';

	protected $_tables = array();

	protected $_queries = array();

	/**
	 * Request object
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request;

	public function __construct(Zend_Controller_Request_Abstract $request = null)
	{
		if (!$request instanceof Zend_Controller_Request_Abstract) {
			$request = Zend_Controller_Front::getInstance()->getRequest();
		}
		$this->setRequest($request);
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
	public function getSpieciesTable()
	{
		return $this->getTable(self::SPIECIES_CLASS);
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
	public function getSpieciesQuery()
	{
		return $this->getQuery(self::SPIECIES_CLASS);
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
    
    public function vote(Nandu_Melody $better, Nandu_Melody $worse)
    {
    	$worse->delete();
    	$second = $this->getRandomMelody($better->id);
    	
    	return array($better, $second);
    }
    
    public function getRandomMelody($bannedId = false)
    {
    	$q  = $this->getMelodyQuery()
    				->where('spiecies_id = ?', 1)
    				->andWhere('deleted_at IS NULL')
    				->orderBy('RAND()')
    				->limit(2);

    	if ($bannedId) {
    		$q->andWhere('id != ?', $bannedId);
    	}
    				
		return $q->fetchOne();
    }
    
    public function getPair()
    {
    	$a = $this->getRandomMelody();
    	$b = $this->getRandomMelody($a->id);
    	return array($a, $b);
    }
    
    public function initPopulation(Nandu_Spiecies $spiecies = null, $count = 8)
    {
    	
    	if (null == $spiecies) {
    		$spiecies = new Nandu_Spiecies();
    		$spiecies->save();
    	}
    	
    	$music = new Nandu_Music_Theory();
    	
    	for($i = 0; $i < $count; $i++) {
    		$melody = new Nandu_Melody();
    		$melody->spiecies_id = $spiecies->id;
    		$melody->save();
    		$notes = $music->melodyGen(Nandu_Music_MusicScales::Major(), 2, 8, 16);;
    		$melody->setNotesFromArray($notes);
    	}
    	
    	return $spiecies;
    }

}