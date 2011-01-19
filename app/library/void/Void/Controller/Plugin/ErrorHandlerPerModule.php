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
 * @package    Void_Controller_Plugin
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Plugin allowing each application module to have it's own error handler.
 *
 * @todo Write, test, blah...
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Controller_Plugin_ErrorHandlerPerModule extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$module = $request->getModuleName();
		$errorHandler = Zend_Controller_Front::getInstance()->getPlugin('Zend_Controller_Plugin_ErrorHandler');
		$prefix = ($module === 'default' ? '' : ucfirst($module) . '_');
		if (class_exists($prefix . 'ErrorController', true)) {
			$errorHandler->setErrorHandlerModule($module);
		}
	}
}
