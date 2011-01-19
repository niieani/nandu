<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ZEND FRAMEWORK IS NOT REQUIRED TO RUN THIS CLASS
 *
 * Based on class by Paweł Korzeniewski <pkorzeni@gmail.com>
 *
 * Small changes by Jakub Argasiński <argasek@gmail.com>
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @link http://blog.jacekkobus.com/2010/10/25/fuzzy-dates-x-dni-godzin-minut-temu-helper-zenda/
 * @license http://www.gnu.org/licenses/gpl-3.0.html and/or New BSD
 * @uses Zend_Translate, Zend_Locale, Zend_Registry
 */

/**
 * Fuzzy dates view helper
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_View_Helper_FuzzyDates {
	/**
	 * Array of inflections per language (if no Zend_Translate detected)
	 * @todo add Zend_Translate
	 * @var array
	 */
	protected $_inflections = array(
		'en' => array(
			'second' 	=> array('second', 'seconds'),
			'minute' 	=> array('minute', 'minutes'),
			'hour' 		=> array('hour', 'hours'),
			'day' 		=> array('day', 'days'),
			'week' 		=> array('week', 'weeks'),
			'month' 	=> array('month', 'months'),
			'year' 		=> array('year', 'years'),
	),
		'pl' => array(
			'second' 	=> array('sekundę', 'sekundy', 'sekund'),
			'minute' 	=> array('minutę', 	'minuty', 'minut'),
			'hour' 		=> array('godzinę', 'godziny', 'godzin'),
			'day' 		=> array('dzień', 'dni', 'dni'),
			'week' 		=> array('tydzień', 'tygodnie', 'tygodni'),
			'month' 	=> array('miesiąc', 'miesiące', 'miesięcy'),
			'year' 		=> array('rok', 'lata', 'lat'),
	),
	);

	/**
	 * @var string
	 */
	protected $_language = 'en';

	/**
	 * @var Zend_Locale
	 */
	protected $_zfLocale = null;

	public function __construct() {
		if (class_exists('Zend_Registry', false)) {
			if (($zflocale = Zend_Registry::get('Zend_Locale')) instanceof Zend_Locale) {
				/* @var $zflocale Zend_Locale */
				$this->_language = $zflocale->getLanguage();
				$this->_zfLocale = $zflocale;
			}
		}
	}

	/**
	 * Render fuzzy date
	 * @param int|string|Zend_Date $timestamp Date in the past (or future) that we are relating to
	 * @param int|string|Zend_Date $timestamp2 Current timestamp by default
	 * @param bool $singleValue Result is rounded to a single value (2 years)
	 * @return string
	 */
	public function fuzzyDates($timestamp = null, $timestamp2 = null, $singleValue = true) {
		return $this->render($timestamp, $timestamp2, $singleValue);
	}

	/**
	 * Render fuzzy date
	 * @param int|string|Zend_Date $timestamp Date in the past (or future) that we are relating to
	 * @param int|string|Zend_Date $timestamp2 Current timestamp by default
	 * @param bool $singleValue Result is rounded to a single value (2 years)
	 * @return string
	 */
	public function render($timestamp = null, $timestamp2 = null, $singleValue = false) {
		if ($timestamp instanceof Zend_Date) {
			$timestamp = $timestamp->toString(Zend_Date::TIMESTAMP);
		}elseif (is_string($timestamp)) {
			$timestamp = strtotime($timestamp);
		}

		if (!$timestamp2)
		$timestamp2 = time();

		$diff = $timestamp2 - $timestamp;  // difference of seconds between both dates

		if ($diff == 0)
		$diff = 1;

		if ($diff < 0)
		$diff = $diff*(-1);

		$seconds = array(
			'year' 		=> (60 * 60 * 24 * 365),
			'month' 	=> (60 * 60 * 24 * 30),
			'week' 		=> (60 * 60 * 24 * 7),
			'day' 		=> (60 * 60 * 24),
			'hour' 		=> (60 * 60),
			'minute' 	=> (60),
			'second' 	=> 1,
		);

		$results = array();

		foreach ($seconds as $part => $value) {
			$tmp = floor($diff/$value);
			if ($tmp >= 1) {
				$results[$part] = $tmp;
				$diff = $diff - $value*$tmp;
			}else{
				$results[$part] = 0;
			}
		}

		$string = array();

		foreach ($results as $part => $int) {
			if ($int != 0) {
				$inflections = $this->_inflections[$this->_language][$part];
				if (!isset($inflections[2])) $inflections[2] = $inflections[1];
				$string[$part] = $int . ' ' . Void_Util_Text_Polish::numeral($int, $inflections[0], $inflections[1], $inflections[2]);
				if ($singleValue)
				break;
			}
		}
		return implode(' ', $string);
	}

}
