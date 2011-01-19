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
 * @package    Blipoteka_Controller_Plugin
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Authorization checking front controller plugin
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {

	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		if ($this->isAuthorizationRequired($request) === false) {
			return;
		}

		$authorized = false;
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity() === true) {
			$authorized = true;
		}

		if ($authorized === false) {
			$request->setModuleName('default');
			$request->setControllerName('error');
			$request->setActionName('forbidden');
		}
	}

	/**
	 * Check if running authorization checks is required.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return bool
	 */
	private function isAuthorizationRequired(Zend_Controller_Request_Abstract $request) {
		$controller = $request->getControllerName();
		$action = $request->getActionName();

		// Any error controller call does not require any authorization
		if ($controller === 'error') return false;

		// An index/index action call does not require any authorization
		if ($controller === 'index' && $action === 'index') return false;

		// We require authentication for all actions, unless otherwise stated.
		if ($request->getUserParam('skip-auth') !== null && $request->getUserParam('skip-auth') == true) {
			return false;
		}

		return true;
	}

}