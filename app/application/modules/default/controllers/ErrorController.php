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
 * @package    Blipoteka_Error
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * The error controller.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class ErrorController extends Blipoteka_Controller {

	/**
	 * Set a different layout for the error controller.
	 *
	 * @return void
	 */
	public function init() {
		parent::init();
		$this->_helper->layout->setLayout('error');
	}

	/**
	 * Default error action
	 *
	 * @return void
	 */
	public function errorAction() {
		$errors = $this->_getParam('error_handler');

		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				// Controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->message = '404 Nie znaleziono strony';
				break;
			default:
				// Application error
				$this->getResponse()->setHttpResponseCode(500);
				$this->view->message = '500 Błąd aplikacji';
				break;
		}

		$this->view->exception = $errors->exception;
		$this->view->request   = print_r($errors->request->getParams(), true);
	}

	/**
	 * Not found (404) error action
	 *
	 * @return void
	 */
	public function notFoundAction() {
		$this->getResponse()->setHttpResponseCode(404);
		$this->_helper->layout->setLayout('error-plain');
	}

	/**
	 * Forbidden (403) error action
	 *
	 * @return void
	 */
	public function forbiddenAction() {
		$this->getResponse()->setHttpResponseCode(403);
		$this->_helper->layout->setLayout('error-plain');
	}
}
