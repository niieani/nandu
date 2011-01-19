<?php

/**
 * Blipoteka.pl
 *
 * LICENSE
 *
 * This source file is subject to the Simplified BSD License
 * that is bundled with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://blipoteka.pl/license
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to blipoteka@gmail.com so we can send you a copy immediately.
 *
 * @category   Blipoteka
 * @package    Blipoteka_Index
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * The index controller.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class IndexController extends Blipoteka_Controller {

	/**
	 * Index action
	 *
	 * @return void
	 */
	public function indexAction() {
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$this->view->headTitle('Strona główna');
			$service = new Blipoteka_Service_Book();
			$paginator = $service->getBookListPaginator();
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$this->view->books = $paginator;
		} else {
			$this->_forward('login', null, null, array('skip-auth' => 1));
		}
	}

	/**
	 * Contact action
	 *
	 * @return void
	 */
	public function contactAction() {
		$this->view->headTitle('Kontakt');
	}

	/**
	 * Login action
	 *
	 * @return void
	 */
	public function loginAction() {
		$form = new Blipoteka_Form_Account_Signin(array('action' => $this->view->url(array(), 'signin')));
		$session = new Zend_Session_Namespace('signin');
		if ($session->form instanceof Blipoteka_Form_Account_Signin) {
			$this->view->form = $session->form;
		} else {
			$this->view->form = $form;
		}
		$session->form = $form;
		$this->_helper->layout->setLayout('layout-login');
	}

	/**
	 * Terms action
	 *
	 * @return void
	 */
	public function termsAction() {
		$this->view->headTitle('Regulamin');
	}

}
