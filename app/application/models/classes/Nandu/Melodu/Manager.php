<?php 
class Nandu_Melody_Manager
{
	const MELODY_CLASS 	= 'Bip_Category';
	const NOTE_CLASS 	= 'Bip_Category_Directory';
	const SPIECIES_CLASS 		= 'Bip_Category_Filter';

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

	public function getSpieciesIdFromRequest()
	{
		return $this->getRequest()->getParam('spieciesId', 0);
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
			throw new Blipoteka_Exception(sprintf('Melody %d not found.', $id, $class));
		}
    }

	/**
	 * Get spiecies
	 * @param integer $id
	 * @return Nandu_Spiecies
	 */
    public function getSpiecies($id = null)
    {
		if ($id === null) {
			$id = $this->getSpieciesIdFromRequest();
		}

		$melody = $this->getSpieciesTable()->find($id);

		if ($melody instanceof Nandu_Spiecies) {
			return $melody;
		} else {
			throw new Blipoteka_Exception(sprintf('Spiecies %d not found.', $id, $class));
		}
    }

}