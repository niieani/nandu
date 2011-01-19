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
 * @package    Void_Auth_Credential_Treatment
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Authentication treatment using hashing function.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 */
class Void_Auth_Credential_Treatment_Hash implements Void_Auth_Credential_Treatment_Interface {
	/**
	 * Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..).
	 * @see hash_algos() for a list of supported algorithms.
	 * @var string
	 */
	private $_algorithm;

	/**
	 * When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
	 * @var unknown_type
	 */
	private $_raw_output;

	/**
	 * Set up algorithm, secret key and decide if output should be binary.
	 *
	 * @param string $algorithm Hashing algorithm
	 * @param bool $raw_output True, if we want raw output;
	 * @throws Void_Exception
	 */
	public function __construct($algorithm, $raw_output = false) {
		if (extension_loaded('hash')) {
			$this->setAlgorithm($algorithm);
			$this->setRawOutput($raw_output);
		} else {
			throw new Void_Exception("Required PHP 'hash' extension not loaded");
		}
	}

	/**
	 * Choose an algorithm.
	 * @param string $algorithm
	 * @throws Void_Exception
	 */
	public function setAlgorithm($algorithm) {
		if (in_array($algorithm, hash_algos())) {
			$this->_algorithm = $algorithm;
		} else {
			throw new Void_Auth_Credential_Treatment_Exception(sprintf("Algorithm '%s' not supported by hash extension", $algorithm));
		}
	}

	/**
	 * Set output format (raw or hex-encoded)
	 * @param bool $raw_output
	 */
	public function setRawOutput($raw_output = false) {
		$this->_raw_output = $raw_output;
	}

	/**
	 * Process credential.
	 * @see Void_Auth_Credential_Treatment_Interface::treatment()
	 */
	public function treatment($credential) {
		return hash($this->_algorithm, $credential, $this->_raw_output);
	}

}
