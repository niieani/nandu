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
 * @package    Void_Application_Doctrine
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

require 'Console/Table.php';

/**
 * Doctrine queries logging tool class.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Application_Doctrine_Log {
	const QUERY_LOG_FILENAME = 'doctrine.log';

	/**
	 * @var string
	 */
	protected $_queryLogFilePath = '';

	/**
	 * @var array
	 */
	protected $_filteredEventTypes = array();

	/**
	 * @var bool
	 */
	protected $_logArguments = false;

	/**
	 * @var array
	 */
	protected $_profilers = array();

	/**
	 * @var Console_Table
	 */
	protected $_consoleTable;

	/**
	 * The constructor
	 *
	 * @param array $profilers An array of Doctrine_Connection_Profilers
	 * @param Console_Table $consoleTable Custom Console_Table object (may be null)
	 * @param string $queryLogFilePath Custom .log file path
	 */
	public function __construct(array $profilers, Console_Table $consoleTable = null, $queryLogFilePath = '') {
		$this->_queryLogFilePath = ($queryLogFilePath != '' ?  $queryLogFilePath : APPLICATION_PATH . DS . 'logs' . DS . self::QUERY_LOG_FILENAME);
		$this->_profilers = $profilers;
		$this->_consoleTable = ($consoleTable instanceof Console_Table ? $consoleTable : new Console_Table());
	}

	/**
	 * Select which types of events should be saved in a log
	 * @param array $filteredEventTypes
	 */
	public function setFilteredEventTypes(array $filteredEventTypes = array()) {
		$this->_filteredEventTypes = $filteredEventTypes;
	}

	/**
	 * Set whether to log queries arguments.
	 * @param bool $logArguments
	 */
	public function setLogArguments($logArguments) {
		$this->_logArguments = (bool) $logArguments;
	}

	/**
	 * Get Doctrine queries log for a given profiler.
	 *
	 * @param Doctrine_Connection_Profiler $profiler
	 * @param array $filteredEventTypes Log only these event types (if none, log all event types)
	 */
	public function getQueriesLog(Doctrine_Connection_Profiler $profiler, $filteredEventTypes = array()) {
		$time = 0;
		$count = 0;
		$executeCount = 0;

		$filterEvents = (count($filteredEventTypes) > 0 ? true : false);

		$sql = array();
		$sql[] = array('Action', 'SQL Query', 'Time', 'Arguments');
		foreach ($profiler as $event) {
			$count++;
			if ($event->getName() === 'execute') {
				$executeCount++;
			}
			$time += $event->getElapsedSecs();
			$query = $event->getQuery();
			$params = $event->getParams();
			if (count($params) > 0) {
				foreach ($params as $k => $param) {
					switch (gettype($param)) {
						case 'string': $params[$k] = "'" . $param . "'"; break;
						case 'NULL': $params[$k] = "NULL"; break;
					}
				}
				$query = str_replace('= ?', "= %s", $query);
				$query = str_replace('?,', "%s,", $query);
				$query = str_replace('?)', "%s)", $query);
				$query = vsprintf($query, $params);
			}
			if ($filterEvents === true && !in_array($event->getName(), $filteredEventTypes)) {
				continue;
			}
			$elapsedTime = sprintf("%f", $event->getElapsedSecs());
			$sql[] = array($event->getName(), $query, $elapsedTime, $event->getParams());
		}

		$result = array(
			'timeTotal' => $time,
      		'count' => $count,
      		'executeCount' => $executeCount,
      		'sql' => $sql
		);

		return $result;
	}

	/**
	 * Return an array of queries logs from provided Doctrine profilers
	 *
	 * @return array
	 */
	protected function getQueriesTable() {
		$table = $this->_consoleTable;
		$profilers = $this->_profilers;
		$filteredEventTypes = $this->_filteredEventTypes;
		$logArguments = $this->_logArguments;

		$result = array();
		foreach ($profilers as $key => $profiler) {
			$info = $this->getQueriesLog($profiler, $filteredEventTypes);
			$message = "Total SQL queries time (%s): %.2f, number of instructions/queries: %d/%d\n";
			$message = sprintf($message, $key, $info['timeTotal'], $info['count'], $info['executeCount']);
			$headers = array("Action", "SQL Query", "Time");
			if ($logArguments === true) $headers[] = "Arguments";
			$table->setHeaders($headers);
			array_shift($info['sql']);
			foreach ($info['sql'] as $row) {
				if ($logArguments === true) {
					$row[3] = implode(',', $row[3]);
				} else {
					unset($row[3]);
				}
				$table->addRow($row);
			}
			$result[] = $table->getTable();
		}

		return $result;
	}

	/**
	 * Save queries log to a file
	 */
	public function saveToFile() {
		$logs = $this->getQueriesTable();
		$path = $this->_queryLogFilePath;
		if (is_file($path)) unlink($path);
		foreach ($logs as $log) {
			$log = str_replace("\r\n", PHP_EOL, $log);
			file_put_contents($path, $log . PHP_EOL, FILE_APPEND);
		}
	}

}