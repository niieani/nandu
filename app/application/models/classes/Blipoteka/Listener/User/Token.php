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
class Blipoteka_Listener_User_Token extends Doctrine_Record_Listener {

	/**
	 * Generate token and update user record.
	 * @see Doctrine_Record_Listener::postInsert()
	 */
	public function postInsert(Doctrine_Event $event) {
		$user = $event->getInvoker();
		$token = $this->createActivationToken($user);
		$user->token = $token;
		$user->save();
	}

	/**
	 * Generates, saves and returns SHA1 activation token.
	 * @return string
	 */
	public function createActivationToken(Blipoteka_User $user) {
		$token = sha1($user->user_id . $user->updated_at . mt_rand());
		return $token;
	}

}
