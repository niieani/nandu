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
 * Authentication-related resource class handling
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Application_Resource_Auth extends Zend_Application_Resource_ResourceAbstract {

	/**
	 * Auth object
	 * @var Zend_Auth
	 */
	protected $_auth;

	/**
	 * Auth adapter chain
	 * @var Void_Auth_Adapter_Interface
	 */
	protected $_authAdapterChain;

	/**
	 * (non-PHPdoc)
	 * @see Zend_Application_Resource_Resource::init()
	 */
	public function init() {
		return $this->getAuth();
	}

	public function getAuth() {
		if ($this->_auth === null) {
			$this->getAuthAdapterChain();
			$this->_auth = Zend_Auth::getInstance();
		}

		return $this->_auth;
	}

	/**
	 * Get auth adapter chain
	 * @throws Zend_Application_Resource_Exception
	 * @return Void_Auth_Adapter_Chain
	 */
	public function getAuthAdapterChain() {
		if ($this->_authAdapterChain === null) {
			$options = $this->getOptions();
			if (!isset($options['adapter']['default']['class'])) {
				throw new Zend_Application_Resource_Exception("Auth resource requires adapter.default.class option to be specified");
			}
			$adapter = $this->_authAdapterChain($options['adapter']);
			Zend_Registry::set('auth-adapter', $adapter);
		}
		return $this->_authAdapterChain;
	}

	/**
	 * Set up auth adapter chain
	 * @param array $adapters
	 */
	protected function _authAdapterChain(array $adapters) {
		// Create adapter chain
		$authAdapter = new Void_Auth_Adapter_Chain();
		// Add adapters to the adapter chain
		foreach ($adapters as $name => $adapter) {
			// Load auth adapter class
			$adapterClass = $adapter['class'];
			if (!Zend_Loader_Autoloader::autoload($adapterClass)) {
				throw new Zend_Application_Resource_Exception("Specified auth adapter '{$adapterClass}' could not be found");
			}
			// Instantiate auth adapter
			$adapter = $this->_authAdapter($adapter);
			if (!$adapter instanceof Void_Auth_Adapter_Interface) {
				throw new Zend_Application_Resource_Exception("Specified auth adapter '{$adapterClass}' needs to implement Void_Auth_Adapter_Interface");
			}
			$authAdapter->addAdapter($adapter, $name);
		}
		return $authAdapter;
	}

	/**
	 * Set up given auth adapter
	 * @param array $options
	 * @return Void_Auth_Adapter_Interface
	 */
	protected function _authAdapter(array $adapter) {
		// An authentication plugin may provide getRequiredResources method.
		// If so, call it and bootstrap required resources prior to instantiating the adapter.
		if (method_exists($adapter['class'], 'getRequiredResources')) {
			foreach ($adapter['class']::getRequiredResources() as $resource) $this->getBootstrap()->bootstrap($resource);
		}
		// Use various setup mechanism for various authentication adapters
		switch ($adapter['class']) {
			case 'Void_Auth_Adapter_Doctrine':
				$authAdapter = $this->_authAdapterDoctrine($adapter);
				break;
			default:
				$authAdapter = new $adapter['class'];
		}
		return $authAdapter;
	}

	/**
	 * Return doctrine based auth adapter
	 * @param array $adapter Adapter options
	 */
	protected function _authAdapterDoctrine(array $adapter) {
		$treatment = null;
		if (isset($adapter['treatment'])) {
			$treatment = $this->_credentialTreatment($adapter['treatment']);
		}

		$model = $this->getOptionValue($adapter, 'model');
		$identity = $this->getOptionValue($adapter, 'identity');
		$credential = $this->getOptionValue($adapter, 'credential');
		$authAdapter = new $adapter['class']($model, $identity, $credential, $treatment);

		return $authAdapter;
	}

	/**
	 * Return auth credential treatment object
	 * @param array $treatment Configuration
	 * @return Void_Auth_Credential_Treatment_Interface
	 * @throws Zend_Application_Resource_Exception
	 */
	protected function _credentialTreatment(array $treatment) {
		if (!isset($treatment['class'])) {
			throw new Zend_Application_Resource_Exception("Treatment requires class option to be specified");
		}
		$treatmentClass = $treatment['class'];
		$algorithm = $this->getOptionValue($treatment, 'algorithm', 'md5');
		$secret = $this->getOptionValue($treatment, 'secret', '');
		$raw_output = (bool) $this->getOptionValue($treatment, 'raw_output', false);
		// Use various setup mechanism for various credential treatment mechanisms
		switch ($treatmentClass) {
			case 'Void_Auth_Credential_Treatment_Hmac':
			case 'Void_Auth_Credential_Treatment_Hmac_Base64':
				$credentialTreatment = new $treatmentClass($secret, $algorithm, $raw_output);
				break;
			case 'Void_Auth_Credential_Treatment_Hash':
				$credentialTreatment = new $treatmentClass($algorithm, $raw_output);
				break;
			default:
				$credentialTreatment = new $treatmentClass;
		}

		return $credentialTreatment;
	}

	/**
	 * Return option's value if option exists and is not null, otherwise default value.
	 * @param array $options Options array
	 * @param string $name Name of an option (key in options array)
	 * @param mixed $default Default value returned if option not found
	 */
	private function getOptionValue(array $options, $name, $default = null) {
		return (isset($options[$name]) ? $options[$name] : $default);
	}

}
