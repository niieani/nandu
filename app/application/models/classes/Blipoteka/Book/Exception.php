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
 * @package    Blipoteka_Book
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Blipoteka book-related exception class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Blipoteka_Book_Exception extends Blipoteka_Exception {
	/**
	 * One cannot change a type of a book from TYPE_OWNED to
	 * TYPE_FREE because he/she is not an owner of the book.
	 *
	 * @var integer
	 */
	const ERR_TYPE_CHANGE_FORBIDDEN = 0;

	/**
	 * @
	 */
	protected $_errorMessages = array(
		self::ERR_TYPE_CHANGE_FORBIDDEN           => 'To make a book free, you need to be an owner of this book'
	);

}