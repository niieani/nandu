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
 * Authentication treatment using HMAC hash test case
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Auth_Credential_Treatment_HmacTest extends PHPUnit_Framework_TestCase {
	const SECRET = 'secret';
	const CREDENTIAL = 'Pójdź, kińże tę chmurność w głąb flaszy!';

	/**
	 * Selecting non-existing algorithm should result in an exception
	 * @expectedException Void_Auth_Credential_Treatment_Exception
	 */
	public function testUnknownAlgorithmException() {
		$treatment = new Void_Auth_Credential_Treatment_Hmac(self::SECRET, 'unknown_algorithm');
	}

	/**
	 * Test hashes
	 */
	public function testAlgorithms() {
		$hashes = $this->_getHashes();
		foreach ($hashes as $algorithm => $hash) {
			$treatment = new Void_Auth_Credential_Treatment_Hmac(self::SECRET, $algorithm);
			$value = $treatment->treatment(self::CREDENTIAL);
			$this->assertSame($hash, $value);
		}
	}

	/**
	 * Get hmac_hash() values array for various algorithms.
	 * @return array
	 */
	private function _getHashes() {
		$hashes = array(
			'md2' => '1ab500d1edf6ea7837f444ccb81e3ca6',
			'md4' => 'f1fa1c3831c727872f5f26f7b12ae6e5',
			'md5' => '1e34c2932193da096368af1fd7633e23',
			'sha1' => '7aa626372076eed653b2adfd4fb8f13fc53d4a14',
			'sha224' => 'af39edca3674e9b82f73ce6a69c40474aea727d51e78937f720d5ea0',
			'sha256' => '279fdb822172d8b55fff138a26bb6bdc1a41c74c9e5926021cb2941b3f18aea0',
			'sha384' => '756fd0894755968ae44c0de74daffaccfbb70ef8fbbfdfcaf9a9d584af3394dcfcda7aa9c1885ab0170c9bef8ca8638f',
			'sha512' => '94a9da78cf8083c1e948fc7d57e1f7929faa85d2848339e1a861e11ee07af0e903791252dd0d8f8815bc37743a1d095c8bf6d2990c014b66f166c02ad5768011',
			'ripemd128' => 'ab5ae7a5a2e89ede63d79b3654a1b068',
			'ripemd160' => '61f091d3ba39f86c14ead0feffcbb1da5f402a30',
			'ripemd256' => 'f6c26019b92b5d96bc6c60057a777c4c81c010e6c448fff2633299f2ebb27bf6',
			'ripemd320' => '703684a96ff2ddc58041c37abfab44ff1c4d0319f3ffcd6bd884bccc49b6a6177d7f4d0ca24873de',
			'whirlpool' => '1080892e382155a5fd18bc6eb0241f86b1c9a40812619c454dc69089a55939fc2d50293c0f19c3ed73d6602b2c28a376e7e77a8f078adf42ddb87d57bde67993',
			'tiger128,3' => '675948c053094d015fda6e80f3794ba5',
			'tiger160,3' => '65d66926efa782145b1b21de7a2b04281ef82a47',
			'tiger192,3' => 'f15a3cd9b2ed8b5ccfb29df446357a7ce6cfce12cf9c520f',
			'tiger128,4' => '97de2bc06ed2d7519d7094b3f7c6b4e8',
			'tiger160,4' => '4e16f447b0a034ae3722bf736b2e5ecb278414ee',
			'tiger192,4' => '35d8810da1d6ee3d58fdf6c49c7af362e96dda45ff768ecf',
			'snefru' => '17028cc5f4cb46f6e9bfb90fdd8c62058bb4f3d6f5f4821ab6b3e32488be7b05',
			'snefru256' => '17028cc5f4cb46f6e9bfb90fdd8c62058bb4f3d6f5f4821ab6b3e32488be7b05',
			'gost' => '01a0ef307ba27ee45d59280dea87b49a417513c2a3d85e663bee9a55a2faf2a8',
			'adler32' => '10670310',
			'crc32' => 'efd39440',
			'crc32b' => '1d87983f',
			'salsa10' => 'f8b3a756a0d077ca8fe3c5a20a467f890576765c9bbd2e22914c4b531fb9e8acbce6ea303572a03c2beb943df1012e1d0de773f39fedbc5f1a58a77316c9e478',
			'salsa20' => '27d24a344fb0e0b71d48de76dbbe5c15dbded4c8e59de330773b1d6b5c4d865021f6942708dbd595b1bab0f382c46516f0f47b01248a1f760ad15104f014216e',
			'haval128,3' => '587441de33dbff29c123e36b9ea924fc',
			'haval160,3' => 'a961e61bfa14123941d148deb73e8f041535f4f3',
			'haval192,3' => '2d1efc4db276228d5898dfc10810b43261338765d349fb75',
			'haval224,3' => 'ab6435f31599e96838672b3854a4bca1972ce451e028861fea105603',
			'haval256,3' => 'c4a89660b135957580c6ff0c7f4256e33ce7d6848e3d45eb8681edc9d46cd306',
			'haval128,4' => '65408df57ce054d3d215af23ba336c72',
			'haval160,4' => 'b7f7767d4fc68a62c186a04eb03f10007362fdca',
			'haval192,4' => 'c9cb1e0521ebbcec028aa191c11b658c0a7122db8e9f08a5',
			'haval224,4' => '676afdc670a3301041c9c40002ae5ebdf63434d6bfb02ba821aa3bae',
			'haval256,4' => '0667a5311eec703ff83e7e4546bf1da06df89331d1d104d626098e0dc2d8a365',
			'haval128,5' => '7ab10564fe29717a8ce9f4038a049565',
			'haval160,5' => '1581c7843e0138da9fc8c82ff6ca82d0b8a81acf',
			'haval192,5' => 'e627eaba2955a38384738dc64ecd8b11fb356b4f68583a36',
			'haval224,5' => 'cfd5d645a3ffb43fdadc8b7dad38b3f9d7cde8a0343bb9a9a6dded17',
			'haval256,5' => '8f77e5bcb51999b138e3ec62bfc2bbea88b397170c42e6b7f7ca7615531ec95c',
		);

		return $hashes;
	}

}
