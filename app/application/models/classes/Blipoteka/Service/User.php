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

/**
 * User related service class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Service_User extends Blipoteka_Service {
	const DEFAULT_USER_NAME = 'Koziołek Matołek';
	const DEFAULT_CITY_NAME = 'Pacanów';

	/**
	 * Class of the record this service applies to
	 * @var string
	 */
	protected $_recordClass = 'Blipoteka_User';

	/**
	 * Auth adapter (required for password hashing/salting)
	 * @var Void_Auth_Adapter_Interface
	 */
	protected $_authAdapter;

	/**
	 * The constructor
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @param Void_Auth_Adapter_Interface $authAdapter
	 */
	public function __construct(Zend_Controller_Request_Abstract $request = null, Void_Auth_Adapter_Interface $authAdapter = null) {
		parent::__construct($request);

		if ($authAdapter === null && Zend_Registry::isRegistered('auth-adapter')) {
			$authAdapter = Zend_Registry::get('auth-adapter')->getDefaultAdapter();
		}
		if ($authAdapter instanceof Void_Auth_Adapter_Interface) {
			$this->_authAdapter = $authAdapter;
		}
	}

	/**
	 * Process given user password hashing/salting algorithms
	 * if provided and store it in a record.
	 *
	 * @param string $password
	 * @param Blipoteka_User $user
	 * @return Blipoteka_Service_User
	 */
	public function setPassword($password, Blipoteka_User $user) {
		if ($this->_authAdapter instanceof Void_Auth_Adapter_Interface) {
			$this->_authAdapter->setCredential($password);
			$password = $this->_authAdapter->getTreatedCredential();
		}
		$user->password = $password;
		return $this;
	}

	/**
	 * Process given user password hashing/salting algorithms
	 * if provided and update user's password.
	 *
	 * @param string $password
	 * @param Blipoteka_User $user
	 * @return Blipoteka_Service_User
	 */
	public function updatePassword($password, Blipoteka_User $user) {
		$this->setPassword($password, $user);
		$user->save();
		return $this;
	}

	/**
	 * Create user account with reasonable default values.
	 *
	 * @param Blipoteka_User $user
	 * @return bool
	 */
	public function createUser(Blipoteka_User $user, Blipoteka_Form_Account_Signup $form) {
		$user->blip = $form->getValue('login');
		$user->email = $form->getValue('email');
		$user->is_active = false;
		$this->setPassword($form->getValue('password'), $user);
		// If user name not provided, use default
		if ($user->name === null) {
			$user->name = $this->getDefaultUserName();
		}
		// If user city not provided, use default
		if ($user->city_id === null) {
			$user->city = $this->getDefaultCity();
		}

		// Generate token and send e-mail notification
		$subject = 'Potwierdzenie rejestracji w Blipotece';
		$user->addListener(new Blipoteka_Listener_User_Token());
		$user->addListener(new Blipoteka_Listener_User_Notification_Email('activation', $subject));

		// If save successful, nothing to see here, move along.
		if ($user->trySave()) {
			return true;
		}

		// Email errors handling
		$mappings = array(
			'unique' => 'Konto o takim adresie e-mail już istnieje',
		);
		$user->errorStackToForm('email', $mappings, $form, 'email');

		// Login errors handling
		$mappings = array(
			'unique' => 'Konto takiego użytkownika już istnieje',
		);
		$user->errorStackToForm('blip', $mappings, $form, 'login');

		return false;
	}

	/**
	 * Get user entity by identity.
	 *
	 * @todo Don't assume email is an identity field.
	 * @param string $identity
	 * @return Blipoteka_User
	 */
	public function getUserByIdentity($identity) {
		return Doctrine_Core::getTable('Blipoteka_User')->findOneBy('email', $identity);
	}

	/**
	 * Return default city.
	 *
	 * @todo Default city name should be set up by some resource
	 * @return Blipoteka_City
	 */
	protected function getDefaultCity() {
		$name = self::DEFAULT_CITY_NAME;
		return Doctrine_Core::getTable('Blipoteka_City')->findOneByName($name);
	}

	/**
	 * Return default user name.
	 *
	 * @todo Default user name should be set up by some resource
	 * @return string
	 */
	protected function getDefaultUserName() {
		$name = self::DEFAULT_USER_NAME;
		return $name;
	}

	/**
	 * Activates user account pointed out by a token. Return
	 * Blipoteka_User entity if activation was successful, false
	 * if it failed.
	 *
	 * @return bool
	 */
	public function activateRegisteredAccount($token) {
		$user = Doctrine_Core::getTable('Blipoteka_User')->findOneByToken($token);
		// User with such token found, activate
		if ($user instanceof Blipoteka_User) {
			$activated_at = new Zend_Date();
			// Reset token
			$user->token = null;
			// Mark as active
			$user->is_active = true;
			$user->activated_at = $activated_at->toString('YYYY-MM-dd HH:mm:ss');
			$user->save();
			// Authenticate user
			$this->authenticateUser($user);
		}
		return $user;
	}

	/**
	 * Authenticates user using his/her own credential data.
	 *
	 * @param Blipoteka_User $user
	 */
	public function authenticateUser(Blipoteka_User $user) {
		$auth = Zend_Auth::getInstance();

		$treatment = new Void_Auth_Credential_Treatment_None();

		$adapter = $this->_authAdapter;
		$adapter->setIdentity($user->email);
		$adapter->setCredential($user->password);
		$adapter->setCredentialTreatment($treatment);
		$result = $auth->authenticate($adapter);

		return $result;
	}

	/**
	 * Try to sign in user using default adapter.
	 *
	 * @param Blipoteka_Form_Account_Signin $form
	 * @return Blipoteka_User|false
	 */
	public function signin(Blipoteka_Form_Account_Signin $form) {
		$auth = Zend_Auth::getInstance();

		$adapter = $this->_authAdapter;
		$adapter->setIdentity($form->getValue('email'));
		$adapter->setCredential($form->getValue('password'));
		$result = $auth->authenticate($adapter);
		if ($result->isValid()) {
			$user = $this->getUserByIdentity($auth->getIdentity());
			if ($this->isAccountActivated($user) === false) {
				$auth->clearIdentity();
				$form->addError("Najpierw musisz aktywować konto");
			} else {
				if ($this->isAccountActive($user) === false) {
					$auth->clearIdentity();
					$form->addError("Twoje konto zostało zablokowane");
				}
			}
		} else {
			$form->addError("Podano nieprawidłowy adres e-mail lub hasło");
		}
	}

	/**
	 * Checks if user's account was activated
	 * (ie. activated_at is not NULL and token is NULL).
	 *
	 * @param Blipoteka_User $user
	 * @return bool
	 */
	public function isAccountActivated(Blipoteka_User $user) {
		return $user->activated_at !== null && $user->token === null;
	}

	/**
	 * Checks if user's account is active.
	 *
	 * @param Blipoteka_User $user
	 * @return bool
	 */
	public function isAccountActive(Blipoteka_User $user) {
		return $user->is_active;
	}

}
