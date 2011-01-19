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
 * @package    Void_Tests
 * @copyright  Copyright (c) 2010-2011 Jakub Argasiński (argasek@gmail.com)
 * @license    http://tekla.art.pl/license/void-simplified-bsd-license.txt Simplified BSD License
 */

/**
 * Authentication treatment using HMAC hash encoded as BASE64 test case
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Auth_Credential_Treatment_Hmac_Base64Test extends PHPUnit_Framework_TestCase {
	const SECRET = 'secret';
	const CREDENTIAL = 'The quick brown fox jumps over the lazy dog';

	/**
	 * Test hashes
	 */
	public function testAlgorithms() {
		$hashes = $this->_getHashes();
		foreach ($hashes as $algorithm => $hash) {
			$treatment = new Void_Auth_Credential_Treatment_Hmac_Base64(self::SECRET, $algorithm, true);
			$value = $treatment->treatment(self::CREDENTIAL);
			$this->assertSame($hash, $value);
		}
	}

	/**
	 * Get hmac_hash() binary, base64 encoded values array for various algorithms.
	 * @return array
	 */
	private function _getHashes() {
		$hashes = array(
			'md2' => 'Xv9dGux+G7cXSV3LuWqKsw==',
			'md4' => 'BhaSsqWJkB9jO28gE7unAw==',
			'md5' => 'Lj83QsIb6I5k3rISf+eS0g==',
			'sha1' => 'GY6h6gTENcEka1hqBtXPEcP/zaY=',
			'sha224' => 'QNccWYRx9WUspDyy3iycydl9JDgI9/ygnu5RUg==',
			'sha256' => 'VM1bgnwOyTj6ByopsXdGnIQzF7CVWR3IRnZ6ozi6xgA=',
			'sha384' => 'v4oi071c+I4PQfqQ7rAOuQj8zZJdVacwXyPiBjWLtIj77wEDkwjkNMJV5Z+OO63D',
			'sha512' => 'dq81iGIO9uLCRNWjYOCAwNZJtt1rgszRFe7v7o/0A7zumusIYY25oqlKnoDHmWuyywwA9uad447YrydY7znfCg==',
			'ripemd128' => 'wOdWRC57lYyLhqy0suNA/w==',
			'ripemd160' => 'sTssm3FlU3uuoXhOZL347p/6MKg=',
			'ripemd256' => 'BQHib9ZlQRShJghrS4zeHkL88J1fgJPHt4Ay8YhhnBU=',
			'ripemd320' => 'EtKBc0e+6jb0Xqf7F+2Bgp2wbLq/kT3COfSoLBP/kL8IijsrQ3N3KA==',
			'whirlpool' => 'GfIUb0U1DWMVVxdMNcb/l1B/ilm6wpWCWBTme32ypwoVAKqxi+MqQQgCuyGpv60R2xsvm4cCp6C5GvW7f62tBA==',
			'tiger128,3' => '14PgYf9MjKveGPWehatKtg==',
			'tiger160,3' => 'Rk2UlpFeUgCl2QspCv3Fy7LKdkY=',
			'tiger192,3' => 'w/Wr/utFTo0Gsl67epfIcTR5mwRzsirG',
			'tiger128,4' => '2hMSmPkpuDPaDpPR59mkXw==',
			'tiger160,4' => '6B4mhCEv/BebhkOkCaEkQ4gRMpY=',
			'tiger192,4' => 'f0Tv6HCuYwZWYYPH5ZUlav9PDsfZM5eK',
			'snefru' => 'si/qK6qUMGH5xua51Q7chi1fvcW1B9ApAlHROUM/3EU=',
			'snefru256' => 'si/qK6qUMGH5xua51Q7chi1fvcW1B9ApAlHROUM/3EU=',
			'gost' => '5p/5yzUPJUXieRrgwF91jLjso31v4744T0QO8v24yf8=',
			'adler32' => 'EeAEHA==',
			'crc32' => 'uzxaMA==',
			'crc32b' => 'Zu0nSQ==',
			'salsa10' => '/FhZDK2AJwHamL5P9PgxREtxKePlzzMljvpMWr+QRJkE1Oo10WpU1y46lNGPjbOk7Odz85/tvF8aWKdzFsnkeA==',
			'salsa20' => 'K3b76lxgj+5n/dcjxnAN0CHZiE8vr+gzdOkecvwj4j1p5JQspNOKMLQJsYchUOqdz/R7ASSKH3YK0VEE8BQhbg==',
			'haval128,3' => 'fCnz3ShG9SheXEqOmhCaOQ==',
			'haval160,3' => 'm+eQB3HE8HmRr3DSEQ3bk/QvSuA=',
			'haval192,3' => 'MLEp0udT8YrNyz7lyHYozHDAP9sl8xw0',
			'haval224,3' => 'rfvm+Ss/8KrgerKDy2U+O9lUnVDygqADlmWaIg==',
			'haval256,3' => 'Pl/APnRGx2pSADA1/aLEHdaY9nIiclWXVlHa369YxdI=',
			'haval128,4' => 'HHF5rJr3npYXKCCBfeWOaw==',
			'haval160,4' => 'tfiD3prRVMaNRtA4AAr4bZIRffw=',
			'haval192,4' => 'asu4XZhcmY67wvIPpz7Yj/ncKZ4CeQXu',
			'haval224,4' => 'PCzezW3dCxYHx8ShclQu+iR3DtW31Soeq/ewSA==',
			'haval256,4' => 'PXdQ1KJAKlSZ52UybUnjekqJxpXXqcOL5j9F3ToW7/E=',
			'haval128,5' => 'xXIfWTEbgFBihzMLYkIzag==',
			'haval160,5' => 'CmnYITZHS2sDkpLTQrf9S8/xpkw=',
			'haval192,5' => 'FUsIAllZYuIXleNRYXqXAUlGm5ud24BF',
			'haval224,5' => '2Iin9LqlJvR9lAlsTHTRGkYXA9JS2U5i21NynA==',
			'haval256,5' => '/AvMudaTxSc/upKapqCuaY2nWEv3KIyfv8G1elKxLDs=',
		);

		return $hashes;
	}

}
