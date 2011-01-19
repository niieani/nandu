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
 * @package    Void_Util_Text
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Polish language specific functions. 
 * 
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Util_Text_Polish {

	/**
	 * Zwraca odmienioną formę liczebnika w zależności od wartości podanej liczby całkowitej.
	 *
	 * @param integer $numeral Liczba całkowita (liczebnik)
	 * @param string $base Forma podstawowa ('produkt')
	 * @param string $several Forma odmieniona w bierniku ('produkty')
	 * @param string $multi Forma odmieniona w dopełniaczu ('produktów')
	 */
	static public function numeral($numeral, $base, $several, $multi) {
		$r = $numeral;
		if ($r == 1) { return $base; }
		$r = $numeral % 100;
		if (in_array($r % 10, array(2, 3, 4)) && !in_array($r, array(12, 13, 14))) {
			return $several;
		}
		return $multi;
	}

}
