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
 * @package    Blipoteka_Validate
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

require_once('Blip/blipapi.php');

/**
 * Check if Blip account exists
 *
 * @see http://code.google.com/p/blipapi/
 * @see http://blip.pl/
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Validate_Blip_Login extends Zend_Validate_Abstract {
	const DOES_NOT_EXIST    = 'accountNonExistant';
	const CONNECTION_FAILED = 'connectionFailed';
	const INVALID_RESPONSE  = 'invalidResponse';

	/**
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::DOES_NOT_EXIST    => "Account doesn't exist on Blip",
		self::CONNECTION_FAILED => "Couldn't connect to Blip service",
		self::INVALID_RESPONSE  => "Blip service response incorrect"
	);

	/**
	 * Internal options array
	 */
	protected $_options = array(
		'skipCheck' => false
	);

	/**
	 * Instantiates blip account validator
	 *
	 * The following option keys are supported:
	 * 'skipCheck'   => Bypass a real check. Useful to satisfy validation when offline etc.
	 *
	 * @param array|Zend_Config $options OPTIONAL
	 * @return void
	 */
	public function __construct($options = array()) {
		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		} else if (!is_array($options)) {
			$options = func_get_args();
			$temp['skipCheck'] = array_shift($options);
			$options = $temp;
		}

		$options += $this->_options;
		$this->setOptions($options);
	}

	/**
	 * Defined by Zend_Validate_Interface
	 *
	 * Returns true if $value is a valid Blip login.
	 *
	 * @param  string $value
	 * @return boolean
	 */
	public function isValid($value) {
		// If forced to skip validation, assume valid result
		if ($this->getSkipCheck() === true) {
			return true;
		}
		$this->_setValue($value);
		$result = $this->testBlipAccountExists($value);
		return $result;
	}

	/**
	 * Returns true if $value Blip account exists.
	 * @param string $value
	 * @return bool
	 */
	protected function testBlipAccountExists($value) {
		$api = new BlipApi();
		$user = new BlipApi_User();
		$user->user = $value;
		try {
    		$response = $api->read($user);
		} catch (RuntimeException $e) {
			if ($e->getCode() == 404) {
				$this->_error(self::DOES_NOT_EXIST);
			} else {
				$this->_error(self::CONNECTION_FAILED);
			}
			return false;
		}
		if ($response['status_code'] == 200) {
			return true;
		} else {
			$this->_error(self::INVALID_RESPONSE);
			return false;
		}
	}

	/**
	 * Returns all set Options
	 *
	 * @return array
	 */
	public function getOptions() {
		return $this->_options;
	}

	/**
	 * Set options for the email validator
	 *
	 * @param array $options
	 * @return Blipoteka_Validate_Blip_Login fluid interface
	 */
	public function setOptions(array $options = array()) {
		if (array_key_exists('skipCheck', $options)) {
			$this->setSkipCheck($options['skipCheck']);
		}

		return $this;
	}

	/**
	 * Get whether to skip online test
	 *
	 * @return boolean
	 */
	public function getSkipCheck() {
		return $this->_options['skipCheck'];
	}

	/**
	 * Set whether to skip online test
	 *
	 * @param boolean $skip If true, account existance will not be checked at all
	 * @return Blipoteka_Validate_Blip_Login Fluid Interface
	 */
	public function setSkipCheck($skipCheck) {
		$this->_options['skipCheck'] = (bool) $skipCheck;
		return $this;
	}

}