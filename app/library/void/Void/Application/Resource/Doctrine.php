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
 * Doctrine resource class.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract {

	public function init() {
		$className = 'Doctrine_Core';
		if (!class_exists($className)) {
			throw new Zend_Application_Resource_Exception(sprintf("Class '%s' not found. Probably Doctrine is not installed or include_path settings are wrong", $className));
		}
		spl_autoload_register(array($className, 'autoload'));

		return $this->getDoctrine();
	}

	public function getDoctrine() {
		$manager = Doctrine_Manager::getInstance();
		// Set Doctrine configuration options (like model loading, etc.)
		if (isset($this->_options['attr'])) {
			foreach ($this->_options['attr'] as $key => $value) {
				$manager->setAttribute(constant('Doctrine_Core::' . strtoupper($key)), $value);
			}
		}
		/**
		 * The array of profilers for all databases
		 *
		 * @var array[string] Doctrine_Connection_Profiler
		 */
		$profilers = array();

		// Get databases array from configuration
		$databases = $this->getBootstrap()->getOption('db');

		if ($databases === null) {
			throw new Zend_Application_Resource_Exception(sprintf("Before using Doctrine, you must setup at least one database connection"));
		}

		// Iterate through all database setups
		foreach ($databases as $name => $attributes) {
			// We create the DSN string from configuration attributes
			$dsn = sprintf('%s://%s:%s@%s/%s',
			$attributes['adapter'],
			$attributes['username'],
			$attributes['password'],
			$attributes['host'],
			$attributes['dbname']
			);

			// Open a new connection
			$connection = $manager->openConnection($dsn, $name, ($name === 'default'));

			// Add a profiler listener if configuration said so
			if (isset($attributes['profiler']) && $attributes['profiler'] == 1) {
				$profiler = new Doctrine_Connection_Profiler();
				$profilers[$name] = $profiler;
				$connection->addListener($profiler, 'profiler');
			}
			// Set connection charset
			if (isset($attributes['charset'])) {
				// We need to set a connection charset, but we have to do it after
				// connection has been established. Thus, we attach an event listerner
				// implementing postConnect() method.
				$charset = new Void_Application_Doctrine_EventListener_Charset($attributes['charset']);
				$connection->addListener($charset, 'charset');
			}
		}

		// Register profilers and loggers
		$doctrineProfilers = array(
			'profilers' => $profilers,
			'loggers' => (isset($this->_options['dqlloggers']) ? $this->_options['dqlloggers'] : null)
		);

		$manager->setParam('profilers', $doctrineProfilers);

		$config = array();
		if (isset($this->_options['cli'])) {
			$config = array(
				'data_fixtures_path'  =>  $this->_options['cli']['fixturesPath'],
				'models_path'         =>  $this->_options['cli']['modelsPath'],
				'migrations_path'     =>  $this->_options['cli']['migrationsPath'],
				'sql_path'            =>  $this->_options['cli']['sqlPath'],
				'yaml_schema_path'    =>  $this->_options['cli']['yamlSchemaPath']
			);
		}

		$cli = new Doctrine_Cli($config);

		$doctrine = new Void_Application_Doctrine($manager, $cli);

		return $doctrine;
	}

}
