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
 * @package    Blipoteka_Service
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

require_once('mbfunctions.php');

/**
 * User related service class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Listener_User_Notification_Email extends Doctrine_Record_Listener {
	/**
	 * E-mail message entity
	 *
	 * @var Zend_Mail
	 */
	protected $_mail;

	/**
	 * Message template name
	 * @var string
	 */
	protected $_template;

	/**
	 * User's nickname (Blip login)
	 * @var string
	 */
	protected $_nickname;

	/**
	 * User entity
	 * @var Blipoteka_User
	 */
	protected $_user;

	public function __construct($template, $subject) {
		$this->_mail = new Zend_Mail('UTF-8');
		$this->_view = new Zend_View();
		$this->_view->setEncoding('UTF-8');
		$this->_template = $template;
		$this->_subject = $subject;

		$path = Zend_Controller_Front::getInstance()->getModuleDirectory('default') . DS . 'views' . DS . 'mail';
		$this->_view->setScriptPath($path);
	}

	/**
	 * Generate token and update user record.
	 * @see Doctrine_Record_Listener::postInsert()
	 */
	public function postInsert(Doctrine_Event $event) {
		$this->_user = $event->getInvoker();

		$this->assignViewVariables();

		$this->_mail->setBodyHtml($this->renderView('html'));
		$this->_mail->setBodyText($this->renderView('plain'));
		$this->_mail->setSubject($this->_subject);
		$this->_mail->addTo($this->_user->email, $this->_nickname);

		$this->_mail->send();
	}

	protected function assignViewVariables() {
		$this->_nickname = mb_ucfirst($this->_user->blip);
		switch ($this->_template) {
			case 'activation':
				$activationUrl = $this->_view->serverUrl($this->_view->url(array('token' => $this->_user->token), 'account-activate'));
				$this->_view->activationUrl = $activationUrl;
				$this->_view->nickname = $this->_nickname;
				break;
		}

	}

	protected function renderView($type) {
		$scriptName = $this->_template . '-' . $type . '.phtml';
		if (is_file($this->_view->getScriptPath($scriptName))) {
			$body = $this->_view->render($scriptName);
		} else {
			throw new Blipoteka_Exception('Could not find view for an email message');
		}
		return $body;
	}

}
