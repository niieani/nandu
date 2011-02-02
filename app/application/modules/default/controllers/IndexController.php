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
			$this->view->headTitle('Main');
	}

	/**
	 * Terms action
	 *
	 * @return void
	 */
	public function termsAction() {
		$this->view->headTitle('Terms of use');
	}

	/**
	 * Contact action
	 *
	 * @return void
	 */
	public function contactAction() {
		$this->view->headTitle('Contact');
	}

	/**
	 * Just junk
	 *
	 * @return void
	 */
	public function junkAction() {
		$this->_helper->jsonOutput(true);
	}

}
