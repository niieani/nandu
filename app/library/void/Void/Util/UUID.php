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
 * UUID generator class
 *
 * Generates valid RFC 4211 compliant Universally Unique IDentifiers (UUID) version 3, 4 and 5.
 * UUIDs generated validate using the OSSP UUID Tool, and the output for named-based UUIDs are
 * exactly the same. This is a pure PHP implementation.
 *
 * Usage:
 *
 *   Name-based UUID:
 *
 *     $v3uuid = Void_Util_UUID::v3('1546058f-5a25-4334-85ae-e68f2a44bbaf', 'SomeRandomString');
 *     $v5uuid = Void_Util_UUID::v5(UUID::NS_URL, 'http://www.google.com/');
 *
 *   Pseudo-random UUID:
 *
 *     $v4uuid = Void_Util_UUID::v4();
 *
 *
 * Originally found at: http://www.php.net/manual/en/function.uniqid.php#94959
 *
 * @author Andrew Moore
 *
 *
 * Modifications made by Henry Merriam <php@henrymerriam.com> on 2009-12-20:
 *
 *   + Added constants for predefined namespaces as defined in RFC 4211 Appendix C.
 *     + NS_DNS
 *     + NS_URL
 *     + NS_ISO_UID
 *     + NS_X500_DN
 *
 *   + Wrote this documentation comment.
 *
 * Modifications made by Jakub Argasiński <argasek@gmail.com> on 2010-03-08:
 *
 *   + Renamed class from 'UUID' to 'Void_Util_UUID'.
 *
 * @todo: make the class use php5-uuid or php5-ossp-uid extension if available.
 * @see http://codingforums.com/showthread.php?p=881913
 * @see https://answers.launchpad.net/ubuntu/+source/ossp-uuid/+question/87216
 * @see https://bugs.launchpad.net/ubuntu/+source/ossp-uuid/+bug/283398
 */
class Void_Util_UUID {

	const NS_DNS     = '6ba7b810-9dad-11d1-80b4-00c04fd430c8'; // FQDN
	const NS_URL     = '6ba7b811-9dad-11d1-80b4-00c04fd430c8'; // URL
	const NS_ISO_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8'; // ISO OID
	const NS_X500_DN = '6ba7b814-9dad-11d1-80b4-00c04fd430c8'; // X.500 DN (in DER or a text output format)

	/**
	 * Returns valid V3 UUID.
	 *
	 * @example Void_Util_UUID::v3('1546058f-5a25-4334-85ae-e68f2a44bbaf', 'SomeRandomString');
	 * @see http://en.wikipedia.org/wiki/UUID
	 * @param $namespace
	 * @param $name
	 */
	public static function v3($namespace, $name) {

		if(!self::isValid($namespace)) return false;

		// Get hexadecimal components of namespace
		$nhex = str_replace(array('-','{','}'), '', $namespace);

		// Binary Value
		$nstr = '';

		// Convert Namespace UUID to bits
		for($i = 0; $i < strlen($nhex); $i+=2) {
			$nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
		}

		// Calculate hash value
		$hash = md5($nstr . $name);

		// Format and return UUID
		return sprintf('%08s-%04s-%04x-%04x-%12s',

		// 32 bits for "time_low"
		substr($hash, 0, 8),

		// 16 bits for "time_mid"
		substr($hash, 8, 4),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 3
		(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

		// 48 bits for "node"
		substr($hash, 20, 12)
		);

	}

	/**
	 * Returns valid V4 UUID().
	 *
	 * @return string V4 UUID.
	 */
	public static function v4() {

		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),

		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,

		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);

	}

	public static function v5($namespace, $name) {

		if(!self::isValid($namespace)) return false;

		// Get hexadecimal components of namespace
		$nhex = str_replace(array('-','{','}'), '', $namespace);

		// Binary Value
		$nstr = '';

		// Convert Namespace UUID to bits
		for($i = 0; $i < strlen($nhex); $i+=2) {
			$nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
		}

		// Calculate hash value
		$hash = sha1($nstr . $name);

		// Format and return UUID
		return sprintf('%08s-%04s-%04x-%04x-%12s',

		// 32 bits for "time_low"
		substr($hash, 0, 8),

		// 16 bits for "time_mid"
		substr($hash, 8, 4),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 5
		(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

		// 48 bits for "node"
		substr($hash, 20, 12)
		);

	}

	/**
	 * Checks whether UUID is a valid one.
	 *
	 * @param string $uuid
	 */
	public static function isValid($uuid) {
		return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
            '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
	}

}
