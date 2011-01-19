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
 * @package    Void_Validate
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

require_once('Zend/Validate/LessThan.php');

/**
 * Validate if less than or equal.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Validate_LessThanOrEqual extends Zend_Validate_LessThan {
	const NOT_LESS = 'notLessThanOrEqual';

	/**
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::NOT_LESS => "'%value%' is not less than or equal '%max%'"
	);

	/**
	 * Sets the max option
	 *
	 * @param  mixed $max
	 * @return Zend_Validate_LessThan Provides a fluent interface
	 */
	public function setMax($max)
	{
		$this->_max = $max + 1;
		return $this;
	}

}