<?php

/**
 * Blipoteka.pl
 *
 * LICENSE
 *
 * This source file is subject to the Simplified BSD License
 * that is bundled with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://blipoteka.pl/license
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to blipoteka@gmail.com so we can send you a copy immediately.
 *
 * @category   Blipoteka
 * @package    Blipoteka
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://blipoteka.pl/license Simplified BSD License
 */

/**
 * Custom bootstrapper class
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
final class Blipoteka_Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	const HEAD_META_DESCRIPTION = 'Bli­po­teka to biblio­teka mię­dzy­mia­stowa użyt­kow­ni­ków ser­wisu Blip.pl.';
	const HEAD_TITLE = 'Blipoteka';
	const OPENSEARCH_TITLE = 'Blipoteka';

	/**
	 * Default locale
	 * @var Zend_Locale
	 */
	private $_locale;

	/**
	 * Get the default locale
	 * @return Zend_Locale
	 */
	private function getLocale() {
		if ($this->hasResource('Locale') === false) {
			$this->_locale = $this->bootstrap('Locale')->getResource('Locale');
		}
		return $this->_locale;
	}

	/**
	 * Initialize HTTP response headers
	 */
	protected function _initHttpResponse() {
		$response = new Zend_Controller_Response_Http();
		$response->setHeader('Content-Language', $this->getLocale()->getLanguage());
	}

	/**
	 * Initialize view, common stylesheets, scripts, etc.
	 */
	protected function _initViews() {
		$language = $this->getLocale()->getLanguage();
		$resource = $this->bootstrap('View');
		$view = $this->getResource('View');
		$view->lang = $language;
		$view->headTitle(self::HEAD_TITLE)->setSeparator(' » ');
		$view->headMeta()->setName("description", self::HEAD_META_DESCRIPTION);
		$view->headLink()->appendStylesheet($view->baseUrl('css/style.css'));
		$view->headLink()->appendStylesheet($view->baseUrl('css/960.css'));
		$view->headLink()->appendStylesheet($view->baseUrl('css/blipoteka.css'));
		$view->headScript()->appendFile($view->baseUrl('js/libs/jquery-1.4.4.min.js'));
		$view->headOpenSearch(self::OPENSEARCH_TITLE, '');
	}

}
