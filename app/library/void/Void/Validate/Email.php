<?php

/**
 * Void
 *
 * LICENSE
 *
 * This source file is subject to the Simplified BSD License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://tekla.art.pl/license/void-simplified-bsd-license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to argasek@gmail.com so I can send you a copy immediately.
 *
 * @category   Void
 * @package    Void_Validate
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

require_once('is_email.php');

/**
 * Zend_Validate style e-mail address validation based on an
 * excellent work by Dominic Sayers.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Validate_Email extends Zend_Validate_Abstract {
	const INVALID            = 'emailAddressInvalid';

	/**
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::INVALID => "'%value%' is not a valid email address",
	);

	/**
	 * Internal options array
	 */
	protected $_options = array(
        'checkdns'   => false,
        'errorlevel' => false
	);

	/**
	 * Instantiates e-mail validator
	 *
	 * The following option keys are supported:
	 * 'checkdns'   => A DNS check for A and MX records will be made
	 * 'errorlevel' => A validation result will be based on an integer error or warning number rather than true or false
	 *
	 * @param array|Zend_Config $options OPTIONAL
	 * @return void
	 */
	public function __construct($options = array()) {
		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		} else if (!is_array($options)) {
			$options = func_get_args();
			$temp['checkdns'] = array_shift($options);
			if (!empty($options)) {
				$temp['errorlevel'] = array_shift($options);
			}
			$options = $temp;
		}

		$options += $this->_options;
		$this->setOptions($options);
	}

	/**
	 * Defined by Zend_Validate_Interface
	 *
	 * Returns true if and only if $value is a valid email address
	 * according to RFCs 5321, 5322 and others
	 *
	 * @link   http://www.ietf.org/rfc/rfc5321.txt RFC 5321
	 * @link   http://www.ietf.org/rfc/rfc5322.txt RFC 5322
	 * @link   http://www.ietf.org/rfc/rfc4291.txt RFC 4291
	 * @link   http://www.ietf.org/rfc/rfc5952.txt RFC 5952
	 * @link   http://www.ietf.org/rfc/rfc1123.txt RFC 1123
	 * @link   http://www.ietf.org/rfc/rfc3696.txt RFC 3696
	 * @see    is_email()
	 * @param  string $value
	 * @return boolean
	 */
	public function isValid($value) {
		$checkDns = $this->getCheckDns();
		$errorLevel = $this->getErrorLevel();
		$is_email = is_email($value, $checkDns, $errorLevel);
		if ($errorLevel === false) {
			$result = $is_email;
		} else {
			// If error level is set to E_WARNING, we accept unlikely,
			// but technically valid addresses and return true (i.e. valid)
			if ($errorLevel === E_WARNING) {
				$result = $is_email < ISEMAIL_ERROR;
			} else {
				$result = $is_email === ISEMAIL_VALID;
			}
		}
		if ($result === false) {
			$this->_setValue($value);
			$this->_error(self::INVALID);
		}
		return $result;
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
	 * @return Zend_Validate_Email fluid interface
	 */
	public function setOptions(array $options = array()) {
		if (array_key_exists('checkdns', $options)) {
			$this->setCheckDns($options['checkdns']);
		}

		if (array_key_exists('errorlevel', $options)) {
			$this->setErrorLevel($options['errorlevel']);
		}

		return $this;
	}

	/**
	 * Returns the value of checkdns option
	 *
	 * @return boolean
	 */
	public function getCheckDns() {
		return $this->_options['checkdns'];
	}

	/**
	 * Set whether a DNS check for A and MX records will be made
	 *
	 * @see is_email()
	 * @param boolean $checkDns A DNS check for A and MX records will be made
	 * @return Zend_Validate_Email Fluid Interface
	 */
	public function setCheckDns($checkDns) {
		$this->_options['checkdns'] = (bool) $checkDns;
		return $this;
	}

	/**
	 * Returns the value of errorlevel option
	 *
	 * @return boolean
	 */
	public function getErrorLevel() {
		return $this->_options['errorlevel'];
	}

	/**
	 * Set whether a DNS check for A and MX records will be made
	 *
	 * @see is_email()
	 * @param boolean|integer $errorLevel A DNS check for A and MX records will be made
	 * @return Zend_Validate_Email Fluid Interface
	 */
	public function setErrorLevel($errorLevel) {
		$this->_options['errorlevel'] = (bool) $errorLevel;
		return $this;
	}

}