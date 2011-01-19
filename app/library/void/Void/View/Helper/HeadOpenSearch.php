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
 * Open Search view helper
 * @see http://www.opensearch.org/
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_View_Helper_HeadOpenSearch extends Zend_View_Helper_Abstract {

	public function headOpenSearch($title, $href = '') {
		if ($href === '') {
			$href = $this->view->baseUrl('opensearch.xml');
		}
		return $this->view->headLink($this->getOpenSearchLink($title, $href));
	}

	/**
	 * Returns array used by headLink() to add OpenSearch link.
	 *
	 * @see http://www.opensearch.org/
	 * @param string $title Label of search engine
	 * @param string $href URL of search engine
	 */
	private function getOpenSearchLink($title, $href) {
		return array('rel' => 'search', 'title' => $title, 'type' => 'application/opensearchdescription+xml', 'href' => $href);
	}

}
