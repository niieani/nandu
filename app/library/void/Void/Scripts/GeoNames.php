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
 * @package    Void_Scripts
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

require_once 'Console/ProgressBar.php';

/**
 * GeoNames database import tool script class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Scripts_GeoNames extends Void_Scripts {
	const VERSION = '0.2';
	const DESCRIPTION = 'Import GeoNames CSV file into the database';

	private $_columns = array();

	/**
	 * City object
	 * @var Doctrine_Record
	 */
	private $_city;

	/**
	 * City object class name
	 * @var Doctrine_Record
	 */
	private $_cityComponentName;

	/**
	 * A path where importer should look for GeoNames files,
	 * when no path is specified
	 * @var string
	 */
	private $_defaultGeoNamesPath;


	public function __construct(Doctrine_Record $city, $defaultGeoNamesPath = '') {
		parent::__construct();
		$this->_defaultGeoNamesPath = $defaultGeoNamesPath;
		$this->_city = $city;
		$this->_cityComponentName = $city->getTable()->getComponentName();
		$this->_columns = array('city_id', 'name', 'asciiname', 'alternatenames', 'lat', 'lng', 'feature_class', 'feature_code', 'country_code', 'cc2', 'admin1_code', 'admin2_code', 'admin3_code', 'admin4_code', 'population', 'elevation', 'gtpo30', 'timezone', 'modified_at');
	}

	public function run() {
		parent::run();

		$this->import($this->getCsvFile());
	}

	public function getCsvFile() {
		// No path provided, let's assume a fixed directory
		if ($this->cli->options['path'] === null) {
			$geonamesPath = $this->getDefaultGeonamesPath();
		} else {
			$geonamesPath = rtrim($this->cli->options['path'], DS) . DS;
		}

		// Combine a path with a filename
		$geonamesFileName = $geonamesPath . mb_strtoupper($this->cli->args['language']) . '.txt';

		// Create file object
		try {
			$file = new SplFileObject($geonamesFileName);
			$file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
			$file->setCsvControl("\t");
		} catch (RuntimeException $e) {
			printf("An error ocurred: %s\n" , $e->getMessage());
			exit($e->getCode());
		}

		return $file;
	}

	protected function getDefaultGeoNamesPath() {
		return $this->_defaultGeoNamesPath;
	}

	/**
	 * Get a number of lines in CSV file. Not very ellegant, but works.
	 *
	 * @param SplFileObject $file
	 * @return interger Number of lines
	 */
	private function getCsvFileLineCount(SplFileObject $file) {
		$file->seek(PHP_INT_MAX);
		$count = $file->key();
		$file->rewind();
		return $count;
	}

	/**
	 * Get list of columns from CSV file we should skip when inserting
	 * record into database table.
	 *
	 * @param Doctrine_Record $city
	 * @return array Ignored columns
	 */
	private function getIgnoredColumns(Doctrine_Record $city) {
		$ignoredColumns = array_diff($this->_columns, $city->getTable()->getColumnNames());
		return $ignoredColumns;
	}

	public function import(SplFileObject $file) {
		// We don't need all columns from imported CSV file, let's ignore them
		$ignoredColumns = $this->getIgnoredColumns($this->_city);

		// Get numer of lines in file
		$lineCount = $this->getCsvFileLineCount($file);

		// Show a progress bar
		if ($this->cli->options['verbose']) {
			$progressBar = new Console_ProgressBar('Importing: [%bar%] %percent%', '=', '.', '60', $lineCount - 1);
			$line = 0;
		}

		$lineImported = 0;

		// Iterate file by line
		foreach ($file as $row) {
			// We need to increase progressbar here -- because we filter out some lines later
			if ($this->cli->options['verbose']) $progressBar->update($line++);

			// Prepare associative array in format acceptable by fromArray() / synchronizeFromArray()
			$record = array_combine($this->_columns, $row);

			// We are interested in cities, villages etc. only
			if ($record['feature_class'] !== 'P') continue;

			// We want to skip ambadoned places, sections etc.
			if (preg_match('/^PPL[SRQX]$/s', $record['feature_code'])) continue;

			// Remove unnecessary columns
			foreach ($ignoredColumns as $ignoredColumn) unset($record[$ignoredColumn]);

			// Transform empty strings onto NULL values
			$record = array_map(function($item) { return ($item === '' ? null : $item); }, $record);

			// Skip ill-formed records
			$skip = false;
			foreach ($record as $field => $value) {
				// Only admin2_code field can be NULL
				if ($value === null && $field !== 'admin2_code') $skip = true;
			}
			if ($skip === true) continue;

			// Save record to the database
			$city = Doctrine_Core::getTable($this->_cityComponentName)->find($record['city_id']);
			$city = ($city instanceof $this->_cityComponentName ? $city : clone $this->_city);
			$city->fromArray($record);
			$city->save();
			$city->free();

			// Increase number of imported records
			$lineImported++;
		}

		// Fix some incorrect data we know of
		$this->fixIncorrectRecords();

		printf("\nSuccessfully imported %d out of %d records.\n", $lineImported, $lineCount);
	}

	private function fixIncorrectRecords() {
		// Warsaw -> Warszawa
		$city = Doctrine_Core::getTable($this->_cityComponentName)->findOneByName('Warsaw');
		if ($city instanceof $this->_cityComponentName) {
			$city->name = 'Warszawa';
			$city->asciiname = 'Warszawa';
			$city->save();
			$city->free();
		}
	}

	protected function setUpParser() {
		// Add an option to specify the path where GeoNames files reside
		$this->parser->addOption('path', array(
			'short_name'  => '-p',
			'long_name'   => '--path',
			'action'      => 'StoreString',
			'description' => 'directory path to GeoNames files'
	    ));

	    // Add an option to choose language file
	    $this->parser->addArgument('language', array(
			'action'      => 'StoreString',
			'description' => "language code of imported file ('pl', 'en', etc.)"
	    ));
	}

}
