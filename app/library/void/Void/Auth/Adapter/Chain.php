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
 * @package    Void_Auth_Adapter_Chain
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 * @license    http://www.zym-project.com/license New BSD License
 */

/**
 * Authentication adapter allowing authentication using
 * multiple adapters (at least one must succeed).
 *
 * Based on Zym_Auth_Adapter_Chain.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 * @author Geoffrey Tran <geoffrey.tran@gmail.com>
 */

/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * @see Zend_Auth_Result
 */
require_once 'Zend/Auth/Result.php';

class Void_Auth_Adapter_Chain implements Zend_Auth_Adapter_Interface {
	/**
	 * Authentication adapter instances
	 *
	 * @var array Array of Zend_Auth_Adapters
	 */
	private $_adapters = array();

	/**
	 * Get the last successfully authenticated adapter
	 *
	 * @var Zend_Auth_Adapter_Interface
	 */
	private $_lastSuccessfulAdapter;

	/**
	 * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
	 * attempt an authenication.  Previous to this call, this adapter would have already
	 * been configured with all nessissary information to successfully connect to a database
	 * table and attempt to find a record matching the provided identity.
	 *
	 * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {
		$adapters = $this->getAdapters();

		$results        = array();
		$resultMessages = array();
		foreach ($adapters as $adapter) {
			// Validate adapter
			if (!$adapter instanceof Zend_Auth_Adapter_Interface) {
				/**
				 * @see Void_Auth_Adapter_Exception
				 */
				require_once 'Void/Auth/Adapter/Exception.php';
				throw new Void_Auth_Adapter_Exception(sprintf('Adapter "%s" is not an instance of Zend_Auth_Adapter_Interface', get_class($adapter)));
			}

			$result = $adapter->authenticate();

			// Success
			if ($result->isValid()) {
				$this->_lastSuccessfulAdapter = $adapter;

				return $result;
			}

			// Failure
			$results[]        = $result;
			$resultMessages[] = $result->getMessages();
		}

		$result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null, $resultMessages);

		return $result;
	}

	/**
	 * Get array of authentication adapters
	 *
	 * @return array
	 */
	public function getAdapters() {
		return $this->_adapters;
	}

	/**
	 * Get authentication adapter by name
	 * @param string $name
	 */
	public function getAdapter($name) {
		if (isset($this->_adapters[$name])) {
			return $this->_adapters[$name];
		}
		return null;
	}

	/**
	 * Get default authentication adapter
	 * @return Zend_Auth_Adapter_Interface
	 */
	public function getDefaultAdapter() {
		return $this->getAdapter('default');
	}

	/**
	 * Add adapter to the stack in FIFO order
	 *
	 * @param Zend_Auth_Adapter_Interface $adapter
	 * @param string $name Optional adapter name
	 * @return Void_Auth_Adapter_Chain
	 */
	public function addAdapter(Zend_Auth_Adapter_Interface $adapter, $name = null) {
		$this->_adapters[$name] = $adapter;
		return $this;
	}

	/**
	 * Set array of authentication adapters
	 *
	 * @param array $adapters
	 * @return Void_Auth_Adapter_Chain
	 */
	public function setAdapters(array $adapters) {
		$this->_adapters = $adapters;
		return $this;
	}

	/**
	 * Get last successfully authenticated adapter instance
	 *
	 * @return Zend_Auth_Adapter_Interface
	 */
	public function getLastSuccessfulAdapter() {
		if (!$this->_lastSuccessfulAdapter instanceof Zend_Auth_Adapter_Interface) {
			/**
			 * @see Zend_Auth_Adapter_Exception
			 */
			require_once 'Zend/Auth/Adapter/Exception.php';
			throw new Zend_Auth_Adapter_Exception('No adapters have successfully authenticated');
		}

		return $this->_lastSuccessfulAdapter;
	}

}
