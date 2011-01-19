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
 * @package    Void_Application_Resource
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Doctrine event listener allowing for, eg. setting
 * right charset after connection has been established.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Application_Doctrine_EventListener_Charset extends Doctrine_EventListener {

	/**
	 * A charset
	 * @var string
	 */
	protected $_charset;

	/**
	 * Constructor
	 * @param string $charset
	 */
	public function __construct($charset) {
		$this->_charset = $charset;
	}

	/**
	 * Set connection charset after connection has been established.
	 * @param Doctrine_Event $event
	 */
	public function postConnect(Doctrine_Event $event) {
		parent::postConnect($event);
		$event->getInvoker()->setCharset($this->_charset);
	}

}
