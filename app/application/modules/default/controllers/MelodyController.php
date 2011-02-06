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
			$species->name = $this->getRequest()->getParam('name');
			$species->tempo = $this->getRequest()->getParam('tempo');
			$species->instrument = $this->getRequest()->getParam('instrument');
			$species->reference_note = $this->getRequest()->getParam('reference_note');
			$species->scale_type = $this->getRequest()->getParam('scale_type');
			$species->save();
			$this->_manager->initPopulation($species);
			$this->_helper->redirector->gotoRoute(array('speciesId' => $species->id), 'evolve', true);
		}
    	//$species = $this->_manager->getSpecies(1);
        //$this->view->species = $species;
        $this->view->allSpecies = $this->_manager->getSpeciesTable()->findAll();;
        $this->view->evolving = false;
	}

	public function evolveAction()
	{
		$melodyAId = $this->getRequest()->getParam('a');
		$melodyBId = $this->getRequest()->getParam('b');
		$species = $this->_manager->getSpecies();
		$this->view->species = $species;
		$this->view->allSpecies = $this->_manager->getSpeciesTable()->findAll();;

		if ($melodyAId && $melodyBId) {
			$melodyA = $this->_manager->getMelody($melodyAId);
			$melodyB = $this->_manager->getMelody($melodyBId);
	
			list ($newA, $newB) = $this->_manager->vote($melodyA, $melodyB);
		} elseif ($melodyAId) {
    	    $newA = $this->_manager->getMelody($melodyAId);
    	    $newB = $this->_manager->getMelodyB($species, $melodyAId);
		} else {
			list ($newA, $newB) = $this->_manager->getPair($species);
		}
		
		$this->view->melodyA = $newA;
		$this->view->melodyB = $newB;
    	$this->view->melodyAfilename = $newA->getAudioFilename();
        $this->view->melodyBfilename = $newB->getAudioFilename();
    	$this->view->evolving = true;
	}

	public function initAction()
	{
		$this->view->species = $this->_manager->initPopulation();
	}
	
}