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
 * @package    Blipoteka_Form_Account
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Signup for a new account form
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Form_Account_Signup extends Zend_Form {

	public function init() {
		$this->setMethod('post');

		$user = Doctrine_Core::getTable('Blipoteka_User')->getRecordInstance();

		$validators = $user->getColumnValidatorsArray('email');
		$validators['email']->setMessage('Nieprawidłowy adres e-mail', Void_Validate_Email::INVALID);
		$email = $this->createElement('text', 'email');
		$email->setLabel('E-mail');
		$email->setFilters(array('StringTrim', 'StringToLower'));
		$email->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => 'Adres e-mail nie może być pusty')));
		$email->addValidators($validators);
		$email->setRequired(true);
		$this->addElement($email);

		$validators = $user->getColumnValidatorsArray('blip');
		$blipValidator = new Blipoteka_Validate_Blip_Login();
		$blipValidator->setSkipCheck(APPLICATION_ENV == 'development');
		$blipValidator->setMessages(array(
			'accountNonExistant' => 'Nie ma takiego konta na Blipie',
			'connectionFailed' => 'Pan Oponka urwał od połączenia, spróbuj później',
			'invalidResponse' => 'Nieprawidłowa odpowiedź serwera Blip'
		));
		$login = $this->createElement('text', 'login');
		$login->setLabel('Login na Blip');
		$login->setFilters(array('StringTrim', 'StringToLower'));
		$login->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => 'Login nie może być pusty')));
		$login->addValidators($validators);
		$login->addValidator($blipValidator);
		$login->setRequired(true);
		$this->addElement($login);

		$minPasswordLength = 8;
		$validators = $user->getColumnValidatorsArray('password');
		$password = $this->createElement('password', 'password');
		$password->setLabel('Hasło');
		$password->setFilters(array('StringTrim'));
		$password->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => 'Hasło nie może być puste')));
		$password->addValidator('StringLength', false, array($minPasswordLength, null,'messages' => array(
			Zend_Validate_StringLength::TOO_SHORT => "Zbyt krótkie hasło (min. $minPasswordLength znaków)"
		)));
		$password->addValidators($validators);
		$password->setRequired(true);
		$this->addElement($password);

		$validators = $user->getColumnValidatorsArray('password');
		$passwordconfirm = $this->createElement('password', 'passwordconfirm');
		$passwordconfirm->setLabel('Powtórz hasło');
		$passwordconfirm->setFilters(array('StringTrim'));
		$passwordconfirm->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => 'Ej. Hasło naprawdę nie może być puste')));
		$passwordconfirm->addValidators($validators);
		$passwordconfirm->addValidator('Identical');
		$passwordconfirm->setRequired(true);
		$this->addElement($passwordconfirm);

		$viewScript = new Zend_Form_Decorator_ViewScript();
		$viewScript->setViewScript('forms/signup.phtml');
		$this->clearDecorators()->addDecorator($viewScript);
	}

	/**
	 * (non-PHPdoc)
	 * @see Zend_Form::isValid()
	 */
	public function isValid($data) {
		$passwordconfirm = $this->getElement('passwordconfirm');
		$passwordconfirm->getValidator('Identical')->setToken($data['password'])->setMessage("Podane hasła nie są zgodne");
		return parent::isValid($data);
	}

}