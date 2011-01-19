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
 * @package    Void_View_Helper
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * HTML5 Boilerplate view helper
 * Generates <html> element
 *
 * @see http://html5boilerplate.com/
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_View_Helper_Html5BoilerplateHtml extends Zend_View_Helper_HtmlElement {
	/**
	 * Cache manifest default filename
	 * @see http://www.w3.org/TR/html5/offline.html
	 * @var string
	 */
	const CACHE_MANIFEST_FILENAME = "cache.manifest";

	/**
	 * Helper code. If true passed as $manifest value, attach manifest="..." attribute
	 * to a rendered element with a default location of cache.manifest file; if false,
	 * don't attach anything. If string passed as $manifest value, treat it is a relative
	 * URL to the cache manifest file.
	 *
	 * @example html5Boilerplate('pl', 'resources/settings/cache.manifest');
	 * @param string $lang Language code (en, pl, ...)
	 * @param bool|string $manifest Boolean or string
	 *
	 * @return $this for fluent interface
	 */
	public function html5BoilerplateHtml($lang, $manifest = false) {
		$this->setLang($lang);
		$this->setManifest($manifest);
		return $this;
	}

	/**
	 * Cast to string representation
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Render <html> element
	 *
	 * @return string
	 */
	public function toString() {
		if ($this->view->doctype()->isHtml5() === false) {
			throw new Zend_View_Exception('You cannot use HTML5 Boilerplate helpers for non-HTML5 documents');
		}

		$manifest = $this->getManifest();
		$lang = $this->getLang();

		$html = array();
		$html[] = "<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->";
		$html[] = "<!--[if lt IE 7 ]> <html lang=\"$lang\" class=\"no-js ie6\"> <![endif]-->";
		$html[] = "<!--[if IE 7 ]>    <html lang=\"$lang\" class=\"no-js ie7\"> <![endif]-->";
		$html[] = "<!--[if IE 8 ]>    <html lang=\"$lang\" class=\"no-js ie8\"> <![endif]-->";
		$html[] = "<!--[if IE 9 ]>    <html lang=\"$lang\" class=\"no-js ie9\"> <![endif]-->";
		$html[] = "<!--[if (gt IE 9)|!(IE)]><!--> <html lang=\"$lang\" class=\"no-js\"$manifest> <!--<![endif]-->";

		return implode(self::EOL, $html);
	}

	/**
	 * Get language code
	 *
	 * @return string
	 */
	protected function getLang() {
		return $this->_lang;
	}

	/**
	 * Get cache manifest URL
	 *
	 * @see http://www.w3.org/TR/html5/offline.html
	 * @return string
	 */
	protected function getManifest() {
		$manifest = '';
		if (is_bool($this->_manifest)) {
			$manifest = ($this->_manifest ? $this->getManifestAttribute(self::CACHE_MANIFEST_FILENAME) : '');
		} else {
			$manifest = $this->getManifestAttribute($this->_manifest);
		}
		return $manifest;
	}

	/**
	 * Return manifest HTML attribute
	 *
	 * @param string $value
	 * @return string
	 */
	private function getManifestAttribute($value) {
		return ' manifest="' . $this->view->baseUrl($value) . '"';
	}

	/**
	 * Set cache manifest URL
	 *
	 * @param unknown_type $manifest
	 * @throws Zend_View_Exception
	 */
	public function setManifest($manifest) {
		if (is_bool($manifest) || is_string($manifest)) {
			$this->_manifest = $manifest;
		} else {
			throw new Zend_View_Exception('Incorrect parameter passed as manifest (must be boolean or relative URL to manifest file)');
		}
	}

	/**
	 * Set HTML lang attribute
	 *
	 * @param string $lang
	 */
	public function setLang($lang) {
		$this->_lang = (string) $lang;
	}

}