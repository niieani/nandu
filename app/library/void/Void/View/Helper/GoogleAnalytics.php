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
 * Google Analytics async helper, inspired by Harold Thétiot work.
 * @see http://www.zfsnippets.com/snippets/view/id/30/google-analytics-view-helper
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_View_Helper_GoogleAnalytics extends Zend_View_Helper_Abstract {
	/**
	 * Tracker ID string
	 * @var string
	 */
	private $_trackerId = null;

	/**
	 * Default tracker ID string
	 * @var string
	 */
	private static $_defaultTrackerId = null;

	/**
	 * Should helper render its contents?
	 * @var bool
	 */
	private static $_enabled = true;

	/**
	 * Helper code
	 *
	 * @param string $trackerId The Google Analytics tracker ID
	 * @param array $options
	 *
	 * @return $this for fluent interface
	 */
	public function googleAnalytics($trackerId = null, array $options = array()) {
		$this->setTrackerId($trackerId);
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
	 * Render Google Analytics Tracker script
	 */
	public function toString() {
		if (self::$_enabled === false) {
			return '';
		}
		$scriptOpening = '<script type="text/javascript">';
		$useCdata = false;
		if ($this->view instanceof Zend_View_Abstract) {
			$scriptOpening = ($this->view->doctype()->isHtml5() ? '<script>' : $scriptOpening);
			$useCdata = $this->view->doctype()->isXhtml() ? true : false;
		}
		$escapeStart = ($useCdata) ? '//<![CDATA[' : '';
		$escapeEnd   = ($useCdata) ? '//]]>'       : '';
		$html = array();
		$html[] = $scriptOpening;
		if ($escapeStart != '') $html[] = $escapeStart;
		$html[] = sprintf("var _gaq = [['_setAccount', '%s'], ['_trackPageview']];", $this->getTrackerId());
		$html[] = "(function(d, t) {";
		$html[] = "	var g = d.createElement(t), s = d.getElementsByTagName(t)[0];";
		$html[] = "	g.async = true;";
		$html[] = "	g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';";
		$html[] = "	s.parentNode.insertBefore(g, s);";
		$html[] = "})(document, 'script');";
		if ($escapeEnd != '') $html[] = $escapeEnd;
		$html[] = "</script>";

		return implode("\n", $html) . "\n";
	}

	/**
	 * Get tracker ID string for this helper instance. If not set,
	 * return default tracker ID shared between all instances of this helper.
	 * @return string
	 */
	protected function getTrackerId() {
		return ($this->_trackerId ? $this->_trackerId : self::$_defaultTrackerId);
	}

	/**
	 * Set tracker ID string
	 * @param string $trackerId
	 */
	protected function setTrackerId($trackerId) {
		$this->_trackerId = $trackerId;
	}

	/**
	 * Set default tracker ID (shared between all view helper instances) string
	 * @param string $trackerId
	 */
	public static function setDefaultTrackerId($trackerId) {
		self::$_defaultTrackerId = $trackerId;
	}

	/**
	 * Whether to render helper or not
	 * @param string $enabled
	 */
	public static function setEnabled($enabled) {
		self::$_enabled = (bool) $enabled;
	}

}
