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
 * Google Analytics resource class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Application_Resource_Analytics extends Zend_Application_Resource_ResourceAbstract {
	/**
	 * Google Analytics view helper instance
	 * @var Void_View_Helper_GoogleAnalytics
	 */
	private $_analytics;

	/**
	 * (non-PHPdoc)
	 * @see Zend_Application_Resource_Resource::init()
	 */
	public function init() {
		$trackerId = (isset($this->_options['ua']) ? $this->_options['ua'] : '');
		$enabled = (isset($this->_options['enabled']) && $this->_options['enabled'] == 1 ? $this->_options['enabled'] : '');
		Void_View_Helper_GoogleAnalytics::setDefaultTrackerId($trackerId);
		Void_View_Helper_GoogleAnalytics::setEnabled($enabled);
		return $this;
	}

}
