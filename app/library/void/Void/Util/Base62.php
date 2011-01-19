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
 * @package    Void_Util
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * The URL-safe BASE62 codec class.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Util_Base62 {
	const BASE = 62;
	const CHARS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * Encode 32-bit integer value (2^31 - 1) using URL-safe BASE62 encoding.
	 *
	 * @param integer $val A value to encode
	 * @param integer $base Base number, 62 by default
	 * @param string $chars Set of characters to use
	 * @return string Encoded value as string
	 */
	public static function encode($val, $base = self::BASE, $chars = self::CHARS) {
		// can't handle numbers larger than  = 2147483647
		$str = '';
		do {
			$i = $val % $base;
			$str = $chars[$i] . $str;
			$val = ($val - $i) / $base;
		} while ($val > 0);
		return $str;
	}

	/**
	 * Decode BASE62 encoded integer value.
	 *
	 * @param string $str A string to encode
	 * @param integer $base Base number, 62 by default
	 * @param string $chars Set of characters to use
	 * @return integer Original value
	 */
	public static function decode($str, $base = self::BASE, $chars = self::BASE) {
		$len = strlen($str);
		$val = 0;
		$arr = array_flip(str_split($chars));
		for ($i = 0; $i < $len; ++$i) {
			$val += $arr[$str[$i]] * pow($base, $len - $i - 1);
		}
		return $val;
	}

}
