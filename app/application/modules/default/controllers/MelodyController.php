<?php

class MelodyController extends Blipoteka_Controller 
{

	/**
	 * @var Nandu_Melody_Manager
	 */
	protected $_manager;

	public function init()
	{
		$this->_manager = new Nandu_Melody_Manager();
	}

	public function indexAction()
	{
		
	}

	public function evolveAction()
	{
		$melodyAId = $this->getRequest()->getParam('a');
		$melodyBId = $this->getRequest()->getParam('b');

		if ($melodyAId && $melodyBId) {
			$melodyA = $this->_manager->getMelody($melodyAId);
			$melodyB = $this->_manager->getMelody($melodyBId);
	
			list ($newA, $newB) = $this->_manager->vote($melodyA, $melodyB);
		} else {
			list ($newA, $newB) = $this->_manager->getPair();
		}
		
		$this->view->melodyA = $newA;
		$this->view->melodyB = $newB;
	}

	public function initAction()
	{
		$this->view->spiecies = $this->_manager->initPopulation();
	}
	
}