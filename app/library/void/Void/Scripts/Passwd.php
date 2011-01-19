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

/**
 * Migration tool script class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Scripts_Passwd extends Void_Scripts {
	const VERSION = '0.1';
	const DESCRIPTION = 'Set password to a given account in application';

	/**
	 * Auth adapter object
	 * @var Void_Auth_Adapter_Doctrine
	 */
	private $adapter;

	/**
	 * The constructor
	 * @param Void_Application_Doctrine $doctrine
	 */
	public function __construct(Void_Auth_Adapter_Doctrine $adapter) {
		parent::__construct();

		$this->adapter = $adapter;
	}

	/**
	 * Run an action basing on a command issued
	 * @see Void_Scripts::run()
	 */
	public function run() {
		// Parse command line
		parent::run();
		// Run an action
		$this->changePassword();
	}

	protected function changePassword() {
		$identity = $this->cli->args['identity'];
		$credential = $this->cli->args['credential'];
		$this->adapter->setIdentity($identity)->setCredential($credential);
		$this->adapter->updateCredential();
		$result = $this->adapter->authenticate();
		// Authentication after credential update failed; print the reasons why
		if ($result->isValid()) {
			printf("Successfully updated credential for %s identity.\n", $identity);
			if ($this->cli->options['verbose']) {
				printf("The new credential: %s\n", $this->adapter->getTreatedCredential());
			}
			return true;
		} else {
			$messages = implode(', ', $result->getMessages());
			printf("Failed to update credential for %s identity, reason(s): %s\n", $identity, $messages);
            return false;
		}
	}

	/**
	 * Set up additional command line options, arguments, commands etc.
	 */
	protected function setUpParser() {
		// Add an option to prevent performing of actual actions (those, which can modify a database)
		$this->parser->addOption('dryrun', array(
		    'short_name'  => '-d',
			'long_name'   => '--dry-run',
		    'action'      => 'StoreTrue',
		    'description' => "don't perform actual changes"
		));

		$this->parser->addArgument('identity', array('description' => 'An identity (usually e-mail, login, etc.)', 'action' => 'StoreString'));
		$this->parser->addArgument('credential', array('description' => 'A password as plain text', 'action' => 'StoreString'));
	}

}

