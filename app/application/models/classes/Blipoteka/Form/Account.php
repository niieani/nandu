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
 * @package    Blipoteka_Form
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * User's profile settings form.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Form_Account extends Zend_Form {

	public function init() {
		$this->setMethod('post');

		$user = Doctrine_Core::getTable('Blipoteka_User')->getRecordInstance();

		$viewScript = new Zend_Form_Decorator_ViewScript();
		$viewScript->setViewScript('forms/account.phtml');
		$this->clearDecorators()->addDecorator($viewScript);
	}

}