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
 * @package    Void_Application
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Doctrine wrapper class.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */

class Void_Application_Doctrine {
	/**
	 * @var Doctrine_Manager
	 */
	private $_manager;

	/**
	 * @var Doctrine_Cli
	 */
	private $_cli;

	public function __construct(Doctrine_Manager $manager, Doctrine_Cli $cli) {
		$this->_manager = $manager;
		$this->_cli = $cli;
	}

	/**
	 * Returns
	 * @return Doctrine_Manager
	 */
	public function getManager() {
		return $this->_manager;
	}

	/**
	 *
	 * @return Doctrine_Cli
	 */
	public function getCli() {
		return $this->_cli;
	}

}
