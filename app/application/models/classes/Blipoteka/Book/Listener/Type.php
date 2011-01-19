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
 * @package    Blipoteka_Book_Listener
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Blipoteka book entity listener adding 'type_name' field.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Book_Listener_Type extends Doctrine_Record_Listener {

	/**
	 * Add type_name (type of book as string).
	 *
	 * @param Doctrine_Event $event
	 */
	public function postHydrate(Doctrine_Event $event) {
        $data = $event->data;
		$data['type_name'] = $this->hydrateTypeName((int) $data['type']);
		$event->data = $data;
	}

	/**
	 * Transform integer book type to string one.
	 *
	 * @param integer $type
	 * @return string
	 */
	protected function hydrateTypeName($type) {
		switch ($type) {
			case Blipoteka_Book::TYPE_FREE:
				$type_name = 'wędruj';
				break;
			case Blipoteka_Book::TYPE_OWNED:
				$type_name = 'wróć';
				break;
			default:
				$type_name = 'nieznany typ';
				break;
		}
		return $type_name;
	}

}