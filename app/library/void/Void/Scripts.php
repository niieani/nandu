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

require_once 'Console/CommandLine.php';

/**
 * Generic script class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
abstract class Void_Scripts {
	/**
	 * A command line parser
	 * @var Console_CommandLine
	 */
	protected $parser;

	/**
	 * Result of command line parsing
	 * @var Console_CommandLine_Result
	 */
	protected $cli;

	/**
	 * Common constructor for all scripts
	 */
	public function __construct() {
		// Set up basic and class-provided parser options
		$this->setUpCommonParser();
		$this->setUpParser();
	}

	/**
	 * Set up parser and common parser options
	 */
	private function setUpCommonParser() {
		// Create the parser
		$this->parser = new Console_CommandLine(array(
		    'description' => static::DESCRIPTION,
		    'version'     => static::VERSION
		));

		// Add an option to make the program verbose
		$this->parser->addOption('verbose', array(
		    'short_name'  => '-v',
		    'long_name'   => '--verbose',
		    'action'      => 'StoreTrue',
		    'description' => 'turn on verbose output'
		));
	}

	/**
	 * Each script class should provide a concrete implementation
	 */
	abstract protected function setUpParser();

	/**
	 * Get a command line parser
	 * @return Console_CommandLine
	 */
	public function getParser() {
		return $this->parser;
	}

	/**
	 * Run tasks (basically, try to parse a command line)
	 */
	public function run() {
		// Parse the command line arguments
		try {
			$this->cli = $this->parser->parse();
		} catch (Exception $e) {
			$this->parser->displayError($e->getMessage());
			exit($e->getCode());
		}
	}
}
