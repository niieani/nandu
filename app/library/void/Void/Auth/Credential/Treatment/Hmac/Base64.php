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
 * @package    Void_Auth_Credential_Treatment_Hmac
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Authentication treatment using HMAC hash encoded as BASE64.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 */
class Void_Auth_Credential_Treatment_Hmac_Base64 extends Void_Auth_Credential_Treatment_Hmac {

	/**
	 * Set up algorithm, secret key and decide if output should be binary.
	 *
	 * @param string $algorithm Hashing algorithm
	 * @param string $key Secret key (salt)
	 * @param bool $raw_output True, if we want raw output;
	 * @throws Void_Exception
	 */
	public function __construct($key, $algorithm, $raw_output = true) {
		parent::__construct($key, $algorithm, $raw_output);
	}

	/**
	 * Process credential.
	 * @see Void_Auth_Credential_Treatment_Interface::treatment()
	 */
	public function treatment($credential) {
		return base64_encode(parent::treatment($credential));
	}

}
