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
 * @package    Void_Auth_Adapter
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Authentication adapter using Doctrine.
 * Based on Eveyron_Zend_Auth_Adapter_Doctrine.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 * @author Eveyron <eveyron@eveyron.com>
 */
class Void_Auth_Adapter_Doctrine implements Void_Auth_Adapter_Interface {
	/**
	 * $_modelName - the model name to check
	 *
	 * @var string
	 */
	protected $_modelName = null;

	/**
	 * $_identityColumn - the column to use as the identity
	 *
	 * @var string
	 */
	protected $_identityColumn = null;

	/**
	 * $_credentialColumns - columns to be used as the credentials
	 *
	 * @var string
	 */
	protected $_credentialColumn = null;

	/**
	 * $_identity - Identity value
	 *
	 * @var string
	 */
	protected $_identity = null;

	/**
	 * $_credential - Credential value
	 *
	 * @var string
	 */
	protected $_credential = null;

	/**
	 * $_credentialTreatment - Object used to apply treatment to the credential
	 *
	 * @var Void_Auth_Credential_Treatment_Interface
	 */
	protected $_credentialTreatment = null;

	/**
	 * $_authenticateResultInfo
	 *
	 * @var array
	 */
	protected $_authenticateResultInfo = null;

	/**
	 * $_resultRecord - Doctrine record of database authentication query
	 *
	 * @var array
	 */
	protected $_resultRecord = null;

	/**
	 * $_ambiguityIdentity - Flag to indicate same Identity can be used with
	 * different credentials. Default is FALSE and need to be set to true to
	 * allow ambiguity usage.
	 *
	 * @var boolean
	 */
	protected $_ambiguityIdentity = false;

	/**
	 * $_strict - whether to use strict comparison for identity or not
	 *
	 * @var bool
	 */
	protected $_strict = true;

	/**
	 * $_zendAuthCredentialMatchColumn - internal use only
	 *
	 * @var string
	 */
	protected $_zendAuthCredentialMatchColumn = 'zend_auth_credential_match';

	/**
	 * $_query - Doctrine_Query to provide additional conditions
	 *
	 * @var Doctrine_Query
	 */
	protected $_query;

	/**
	 * List of required Zend_Application_Resource resources
	 * @var array();
	 */
	protected static $_requiredResources = array('doctrine');

	/**
	 * Constructor
	 *
	 * @param string $modelName [optional]
	 * @param string $identityColumn [optional]
	 * @param string $credentialColumn [optional]
	 * @param Void_Auth_Credential_Treatment $credentialTreatment [optional]
	 * @param Doctrine_Query $query [optional]
	 * @return void
	 */
	public function __construct($modelName = null, $identityColumn = null, $credentialColumn = null, Void_Auth_Credential_Treatment_Interface $credentialTreatment = null, $query = null) {
		if (null !== $modelName) {
			$this->setModelName($modelName);
		}

		if (null !== $identityColumn) {
			$this->setIdentityColumn($identityColumn);
		}

		if (null !== $credentialColumn) {
			$this->setCredentialColumn($credentialColumn);
		}

		if (null !== $credentialTreatment) {
			$this->setCredentialTreatment($credentialTreatment);
		}

		if (null !== $query) {
			$this->setQuery($query);
		}
	}

	/**
	 * setModelName() - set the model name to be used in the select query
	 *
	 * @param  string $modelName
	 * @return Void_Auth_Adapter_Doctrine Provides a fluent interface
	 */
	public function setModelName($modelName) {
		$this->_modelName = $modelName;
		return $this;
	}

	/**
	 * setIdentityColumn() - set the column name to be used as the identity column
	 *
	 * @param  string $identityColumn
	 * @return Void_Auth_Adapter_Doctrine Provides a fluent interface
	 */
	public function setIdentityColumn($identityColumn) {
		$this->_identityColumn = $identityColumn;
		return $this;
	}

	/**
	 * setCredentialColumn() - set the column name to be used as the credential column
	 *
	 * @param  string $credentialColumn
	 * @return Void_Auth_Adapter_Doctrine Provides a fluent interface
	 */
	public function setCredentialColumn($credentialColumn) {
		$this->_credentialColumn = $credentialColumn;
		return $this;
	}

	/**
	 * setCredentialTreatment() - allows a developer to pass an instance of class
	 * implementing Void_Auth_Credential_Treatment_Interface used to transform or
	 * treat the input credential data.
	 *
	 * In many cases, passwords and other sensitive data are encrypted, hashed, encoded,
	 * obscured, or otherwise treated through some function or algorithm. By specifying a
	 * treatment object with this method, a developer may apply arbitrary processing
	 * upon input credential data.
	 *
	 * @param  Void_Auth_Credential_Treatment_Interface $treatment
	 * @return Void_Auth_Adapter_Doctrine Provides a fluent interface
	 */
	public function setCredentialTreatment(Void_Auth_Credential_Treatment_Interface $treatment) {
		$this->_credentialTreatment = $treatment;
		return $this;
	}

	/**
	 * setIdentity() - set the value to be used as the identity
	 *
	 * @param  string $value
	 * @return Void_Auth_Adapter_Doctrine Provides a fluent interface
	 */
	public function setIdentity($value) {
		$this->_identity = $value;
		return $this;
	}

	/**
	 * setCredential() - set the credential value to be used, optionally can specify a treatment
	 * to be used, should be supplied in parameterized form, such as 'MD5(?)' or 'PASSWORD(?)'
	 *
	 * @param  string $credential
	 * @return Void_Auth_Adapter_Doctrine Provides a fluent interface
	 */
	public function setCredential($credential) {
		$this->_credential = $credential;
		return $this;
	}

	/**
	 * setAmbiguityIdentity() - sets a flag for usage of identical identities
	 * with unique credentials. It accepts integers (0, 1) or boolean (true,
	 * false) parameters. Default is false.
	 *
	 * @param  int|bool $flag
	 * @return Void_Auth_Adapter_Doctrine
	 */
	public function setAmbiguityIdentity($flag) {
		if (is_integer($flag)) {
			$this->_ambiguityIdentity = (1 === $flag ? true : false);
		} elseif (is_bool($flag)) {
			$this->_ambiguityIdentity = $flag;
		}
		return $this;
	}

	/**
	 * getAmbiguityIdentity() - returns TRUE for usage of multiple identical
	 * identies with different credentials, FALSE if not used.
	 *
	 * @return bool
	 */
	public function getAmbiguityIdentity() {
		return $this->_ambiguityIdentity;
	}

	/**
	 * Returns $strict.
	 *
	 * @see Void_Auth_Adapter_Doctrine::$strict
	 * @return boolean
	 */
	public function getStrict() {
		return $this->_strict;
	}

	/**
	 * Sets $strict.
	 *
	 * @param boolean $strict
	 * @see Void_Auth_Adapter_Doctrine::$strict
	 */
	public function setStrict($flag) {
		$this->_strict = $flag;
	}

	/**
	 * Returns $query.
	 *
	 * @see Void_Auth_Adapter_Doctrine::$query
	 * @return Doctrine_Query
	 */
	public function getQuery() {
		return $this->_query;
	}

	/**
	 * Sets $query.
	 *
	 * @param Doctrine_Query $query
	 * @see Void_Auth_Adapter_Doctrine::$query
	 * @return Void_Auth_Adapter_Doctrine
	 */
	public function setQuery(Doctrine_Query $query) {
		$this->_query = $query;
		return $this;
	}

	/**
	 * getResultRecordObject() - Returns the result row as a stdClass object
	 *
	 * @param  string|array $returnColumns
	 * @param  string|array $omitColumns
	 * @return stdClass|boolean
	 */
	public function getResultRecordObject($returnColumns = null, $omitColumns = null) {
		if (!$this->_resultRecord) {
			return false;
		}

		$returnObject = new stdClass();

		if (null !== $returnColumns) {

			$availableColumns = array_keys($this->_resultRecord);
			foreach ( (array) $returnColumns as $returnColumn) {
				if (in_array($returnColumn, $availableColumns)) {
					$returnObject->{$returnColumn} = $this->_resultRecord[$returnColumn];
				}
			}
			return $returnObject;

		} elseif (null !== $omitColumns) {

			$omitColumns = (array) $omitColumns;
			foreach ($this->_resultRecord as $resultColumn => $resultValue) {
				if (!in_array($resultColumn, $omitColumns)) {
					$returnObject->{$resultColumn} = $resultValue;
				}
			}
			return $returnObject;

		} else {

			foreach ($this->_resultRecord as $resultColumn => $resultValue) {
				$returnObject->{$resultColumn} = $resultValue;
			}
			return $returnObject;

		}
	}

	/**
	 * Performs an authentication attempt
	 *
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {
		$this->_authenticateSetup();
		$query = $this->_authenticateCreateSelect();
		$resultIdentities = $this->_authenticateQuerySelect($query);

		if (($authResult = $this->_authenticateValidateResultSet($resultIdentities)) instanceof Zend_Auth_Result) {
			return $authResult;
		}

		if (true === $this->getAmbiguityIdentity()) {
			$validIdentities = array ();
			foreach ($resultIdentities as $identity) {
				if (1 === (int) $identity[$this->_zendAuthCredentialMatchColumn]) {
					$validIdentities[] = $identity;
				}
			}
			$resultIdentities = $validIdentities;
		}

		$authResult = $this->_authenticateValidateResult(array_shift($resultIdentities));
		return $authResult;
	}

	/**
	 * Update credential for specified identity
	 * @param string $credential
	 * @return Void_Auth_Adapter_Doctrine
	 */
	public function updateCredential() {
		$this->_authenticateSetup();
		// Run optional treatment before passing in credential to the query
		$credential = $this->getTreatedCredential();
		$query = $this->_updateCredentialCreateUpdate($credential)->execute();
		return $this;
	}

	/**
	 * _authenticateSetup() - This method abstracts the steps involved with
	 * making sure that this adapter was indeed setup properly with all
	 * required pieces of information.
	 *
	 * @throws Zend_Auth_Adapter_Exception - in the event that setup was not done properly
	 * @return true
	 */
	protected function _authenticateSetup() {
		$exception = null;

		if ($this->_modelName == '') {
			$exception = 'A model must be supplied for the Void_Auth_Adapter_Doctrine authentication adapter.';
		} elseif ($this->_identityColumn == '') {
			$exception = 'An identity column must be supplied for the Void_Auth_Adapter_Doctrine authentication adapter.';
		} elseif ($this->_credentialColumn == '') {
			$exception = 'A credential column must be supplied for the Void_Auth_Adapter_Doctrine authentication adapter.';
		} elseif ($this->_identity == '') {
			$exception = 'A value for the identity was not provided prior to authentication or credential update with Void_Auth_Adapter_Doctrine.';
		} elseif ($this->_credential === null) {
			$exception = 'A credential value was not provided prior to authentication or credential update with Void_Auth_Adapter_Doctrine.';
		}

		if (null !== $exception) {
			/**
			 * @see Zend_Auth_Adapter_Exception
			 */
			require_once 'Zend/Auth/Adapter/Exception.php';
			throw new Zend_Auth_Adapter_Exception($exception);
		}

		$this->_authenticateResultInfo = array(
            'code'     => Zend_Auth_Result::FAILURE,
            'identity' => $this->_identity,
            'messages' => array()
		);

		return true;
	}

	/**
	 * _authenticateCreateSelect() - This method creates a Doctrine_Query object that
	 * is completely configured to be queried against the database.
	 *
	 * @return Doctrine_Query
	 */
	protected function _authenticateCreateSelect() {
		if ($this->_query instanceof Doctrine_Query) {
			$query = $this->_query;
		} else {
			$query = Doctrine_Query::create();
		}

		$from = $query->getSqlQueryPart('from');
		if (empty($from)) {
			$query->from($this->_modelName.' u');
		}

		$select = $query->getSqlQueryPart('select');
		if (empty($select)) {
			$query->select('u.*');
		}

		$alias = $query->getRootAlias();

		$query->addSelect(
			'(CASE WHEN ' . $alias. '.' . $this->_credentialColumn. ' = :credential '
			. ' THEN 1 ELSE 0 END) AS ' . $this->_zendAuthCredentialMatchColumn
		);

		if ($this->_strict) {
			$query->addWhere($alias.'.'.$this->_identityColumn.' = :identity', array('identity' => $this->_identity));
		} else {
			$query->addWhere('TRIM(LOWER('.$alias.'.'.$this->_identityColumn.')) = ?', array('identity' => trim(strtolower($this->_identity))));
		}

		return $query;
	}

	/**
	 * _updateCredentialCreateUpdate() - This method creates a Doctrine_Query object that
	 * is completely configured to be queried against the database.
	 *
	 * @return Doctrine_Query
	 */
	protected function _updateCredentialCreateUpdate($credential) {
		$query = ($this->_query instanceof Doctrine_Query ? $this->_query : Doctrine_Query::create());

		$update = $query->getSqlQueryPart('forUpdate');
		if (empty($update)) {
			$query->update($this->_modelName.' u');
		}

		$alias = $query->getRootAlias();

		$set = $query->getSqlQueryPart('set');
		if (empty($set)) {
			$query->set($alias. '.' . $this->_credentialColumn, '?', $credential);
		}

		if ($this->_strict) {
			$query->addWhere($alias.'.'.$this->_identityColumn.' = ?', $this->_identity);
		} else {
			$query->addWhere('TRIM(LOWER('.$alias.'.'.$this->_identityColumn.')) = ?', trim(strtolower($this->_identity)));
		}

		return $query;
	}

	/**
	 * _authenticateQuerySelect() - This method accepts a Zend_Db_Select object and
	 * performs a query against the database with that object.
	 *
	 * @param Doctrine_Query $query
	 * @throws Zend_Auth_Adapter_Exception - when an invalid select
	 *                                       object is encountered
	 * @return array
	 */
	protected function _authenticateQuerySelect(Doctrine_Query $query) {
		// Run optional treatment before passing in credential to the query
		$credential = $this->getTreatedCredential();
		try {
			$resultIdentities = $query->fetchArray(array(':credential' => $credential));
		} catch (Exception $e) {
			/**
			 * @see Zend_Auth_Adapter_Exception
			 */
			require_once 'Zend/Auth/Adapter/Exception.php';
			throw new Zend_Auth_Adapter_Exception('The supplied parameters to Void_Auth_Adapter_Doctrine failed to '
			. 'produce a valid SQL statement, please check table and column names '
			. 'for validity.', 0, $e);
		}

		return $resultIdentities;
	}

	/**
	 * Get credential processed (hashed, etc.) by a treatment method.
	 * @return string
	 */
	public function getTreatedCredential() {
		if ($this->_credentialTreatment instanceof Void_Auth_Credential_Treatment_Interface) {
			return $this->_credentialTreatment->treatment($this->_credential);
		}
		return $this->_credential;
	}


	/**
	 * _authenticateValidateResultSet() - This method attempts to make
	 * certain that only one record was returned in the resultset
	 *
	 * @param array $resultIdentities
	 * @return true|Zend_Auth_Result
	 */
	protected function _authenticateValidateResultSet(array $resultIdentities) {
		if (count($resultIdentities) < 1) {
			$this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
			$this->_authenticateResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
			return $this->_authenticateCreateAuthResult();
		} elseif (count($resultIdentities) > 1 && false === $this->getAmbiguityIdentity()) {
			$this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
			$this->_authenticateResultInfo['messages'][] = 'More than one record matches the supplied identity.';
			return $this->_authenticateCreateAuthResult();
		}

		return true;
	}

	/**
	 * _authenticateValidateResult() - This method attempts to validate that
	 * the record in the resultset is indeed a record that matched the
	 * identity provided to this adapter.
	 *
	 * @param array $resultIdentity
	 * @return Zend_Auth_Result
	 */
	protected function _authenticateValidateResult($resultIdentity) {
		if ($resultIdentity[$this->_zendAuthCredentialMatchColumn] != '1') {
			$this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
			$this->_authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
			return $this->_authenticateCreateAuthResult();
		}

		unset($resultIdentity[$this->_zendAuthCredentialMatchColumn]);
		$this->_resultRecord = $resultIdentity;

		$this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
		$this->_authenticateResultInfo['messages'][] = 'Authentication successful.';
		return $this->_authenticateCreateAuthResult();
	}

	/**
	 * _authenticateCreateAuthResult() - Creates a Zend_Auth_Result object from
	 * the information that has been collected during the authenticate() attempt.
	 *
	 * @return Zend_Auth_Result
	 */
	protected function _authenticateCreateAuthResult() {
		return new Zend_Auth_Result(
			$this->_authenticateResultInfo['code'],
			$this->_authenticateResultInfo['identity'],
			$this->_authenticateResultInfo['messages']
		);
	}

	/**
	 * Get list of required resources (array of strings)
	 * @return array
	 */
	public static function getRequiredResources() {
		return self::$_requiredResources;
	}

}
