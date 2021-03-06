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
 * Authentication treatment interface.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 */
interface Void_Auth_Credential_Treatment_Interface {

	/**
	 * A method doing an actual processing of $credential,
	 * returning processed $result.
	 *
	 * @param mixed $credential
	 * @return mixed $result
	 */
	public function treatment($credential);

}
