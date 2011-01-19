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
 * Blipoteka book entity listener adding 'status_name' field.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Book_Listener_Status extends Doctrine_Record_Listener {

	/**
	 * Add status_name (status of book as string).
	 *
	 * @param Doctrine_Event $event
	 */
	public function postHydrate(Doctrine_Event $event) {
        $data = $event->data;
		$data['status_name'] = $this->hydrateStatusName((int) $data['status']);
		$event->data = $data;
	}

	/**
	 * Transform integer book status to string one.
	 *
	 * @param integer $status
	 * @return string
	 */
	protected function hydrateStatusName($status) {
		switch ($status) {
			case Blipoteka_Book::STATUS_AVAILABLE:
				$status_name = 'dostępna';
				break;
			case Blipoteka_Book::STATUS_BORROWED:
				$status_name = 'wypożyczona';
				break;
			case Blipoteka_Book::STATUS_COURIER:
				$status_name = 'czeka na kuriera';
				break;
			case Blipoteka_Book::STATUS_DELIVERED:
				$status_name = 'przekazywana';
				break;
			case Blipoteka_Book::STATUS_LOST:
				$status_name = 'zgubiona';
				break;
			case Blipoteka_Book::STATUS_UNAVAILABLE:
				$status_name = 'niedostępna';
				break;
			default:
				$status_name = 'nieznany status';
				break;
		}
		return $status_name;
	}

}