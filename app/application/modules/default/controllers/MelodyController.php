<?php

class MelodyController extends Blipoteka_Controller 
{

	/**
	 * @var Nandu_Melody_Manager
	 */
	protected $_manager;
	
	/**
	 * @var Zend_Config
	 */
	protected $_settings;

	public function init()
	{
		$this->_manager = new Nandu_Melody_Manager();
		$this->_settings = Zend_Registry::get('settings');
	}

	public function indexAction()
	{
		if ($this->getRequest()->isPost()) {
			$species = new Nandu_Species();
			$species->save();
			$this->_manager->initPopulation($species);
			$this->_helper->redirector->gotoRoute(array('speciesId' => $spiecies->id), 'evolve', true);
		}
	}

	public function evolveAction()
	{
		$melodyAId = $this->getRequest()->getParam('a');
		$melodyBId = $this->getRequest()->getParam('b');
		$species = $this->_manager->getSpecies();
		$this->view->species = $species;

		if ($melodyAId && $melodyBId) {
			$melodyA = $this->_manager->getMelody($melodyAId);
			$melodyB = $this->_manager->getMelody($melodyBId);
	
			list ($newA, $newB) = $this->_manager->vote($melodyA, $melodyB);
		} else {
			list ($newA, $newB) = $this->_manager->getPair($species);
		}
		
		$this->view->melodyA = $newA;
		$this->view->melodyB = $newB;
	}

	public function initAction()
	{
		$this->view->species = $this->_manager->initPopulation();
	}
	
}