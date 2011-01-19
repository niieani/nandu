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
 * @package    Blipoteka
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Generic Blipoteka service class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
abstract class Blipoteka_Service {
	/**
	 * Any service record class must inherit this class
	 * @var string
	 */
	const SERVICE_PARENT_CLASS = 'Void_Doctrine_Record';

	/**
	 * Class of the record this service applies to
	 * @var string
	 */
	protected $_recordClass = '';

	/**
	 * Users table
	 * @var Doctrine_Table
	 */
	protected $_table;

	/**
	 * Default query object
	 * @var Doctrine_Query
	 */
	protected $_query;

	/**
	 * Request object
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request;

	/**
	 * The constructor
	 */
	public function __construct(Zend_Controller_Request_Abstract $request = null) {
		if (is_subclass_of($this->_recordClass, self::SERVICE_PARENT_CLASS)) {
			$this->_table = Doctrine_Core::getTable($this->_recordClass);
			$this->_query = Doctrine_Query::create()->from($this->_recordClass);
		} else {
			throw new LogicException('The $_recordClass class should be a class being child of ' .  self::SERVICE_PARENT_CLASS);
		}

		if ($request instanceof Zend_Controller_Request_Abstract) {
			$this->_request = $request;
		} else {
			$this->_request = Zend_Controller_Front::getInstance()->getRequest();
		}
	}

}
