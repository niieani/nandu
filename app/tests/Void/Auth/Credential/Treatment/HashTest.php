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
 * Authentication treatment using hash test case
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Auth_Credential_Treatment_HashTest extends PHPUnit_Framework_TestCase {
	const CREDENTIAL = 'Pójdź, kińże tę chmurność w głąb flaszy!';

	/**
	 * Selecting non-existing algorithm should result in an exception
	 * @expectedException Void_Auth_Credential_Treatment_Exception
	 */
	public function testUnknownAlgorithmException() {
		$treatment = new Void_Auth_Credential_Treatment_Hash('unknown_algorithm');
	}

	/**
	 * Test hashes
	 */
	public function testAlgorithms() {
		$hashes = $this->_getHashes();
		foreach ($hashes as $algorithm => $hash) {
			$treatment = new Void_Auth_Credential_Treatment_Hash($algorithm);
			$value = $treatment->treatment(self::CREDENTIAL);
			$this->assertSame($hash, $value);
		}
	}

	/**
	 * Get hmac_hash() values array for various algorithms.
	 * @return array
	 */
	private function _getHashes() {
		$hashes = array (
			'md2' => '0111da751b2db5f6dea5eef87e7b2289',
			'md4' => '032af64f78262248dab56dda910fd04d',
			'md5' => 'f94baf7551aae9a9405cd47792b3d825',
			'sha1' => '981a8554e5584a4d8550d0dd201ceaebac6687ba',
			'sha224' => 'ec27f0efea47c63bd10e65282c3b0a2b9b7f5b230854651b30903971',
			'sha256' => '768face4184f9cc965c959e1f21387fb7ee6ecdf96c1322cc2fe55c1c1e12785',
			'sha384' => '8f455117a957a7604b9b233784587d1ac66e39b7d301e61cbb7e74fc2e912543a328df5b0aec315db64f1c1bd76dc23a',
			'sha512' => '091ba6c07006ea30c49b070551f263420f8daa3c3c5711fc5fd856c103c665c9465163dce47257c9e15ff26696d716eb6e0eb47152481e7e03dfe52d4cd7d9ef',
			'ripemd128' => '209d31c2a8df3a1fbf22c36c633b42b6',
			'ripemd160' => 'fbfee8f34a86b2c280eb28ae5cc707338c4b9a8f',
			'ripemd256' => 'a1c9971af60b891437d99736507b6f1737eded71a3c1abe376e254d478c0b3a7',
			'ripemd320' => '7f29684cf4e4af4062c73563abe25e62ff933ddc8cf2311a8f31a314a226a224c6cb0fabd4b1f46f',
			'whirlpool' => 'f785118d21c59cfc2bb2d428ba9968b03f73aa9cf8e36a23d483e486e5bb0595bf9aea510148d7bee20d0a1a1849de9e8f0574e591362d3f0603aa0196ae96f5',
			'tiger128,3' => '918dfa049c30faaf721bfb5117d7e790',
			'tiger160,3' => '918dfa049c30faaf721bfb5117d7e79025a7b853',
			'tiger192,3' => '918dfa049c30faaf721bfb5117d7e79025a7b8536ff123a9',
			'tiger128,4' => 'f5c87f09d1d97d8003979e7ac1ff2b59',
			'tiger160,4' => 'f5c87f09d1d97d8003979e7ac1ff2b59676a864d',
			'tiger192,4' => 'f5c87f09d1d97d8003979e7ac1ff2b59676a864d17550f2a',
			'snefru' => '91cdc935f7b91e78310d184cc817227dc77bdf762b8215624e2a1cdea45e5fc7',
			'snefru256' => '91cdc935f7b91e78310d184cc817227dc77bdf762b8215624e2a1cdea45e5fc7',
			'gost' => '77af25767c7a684780b1293f656209370972e5ad9ec14c6060ae414f68adbd53',
			'adler32' => '58581705',
			'crc32' => '45890f91',
			'crc32b' => '2e316618',
			'salsa10' => 'e1406280d9373363b3856657a3c79e37325967801866f55d7dedb3aaeaeadaaed05971f5d171d85d8c74e55ac12b3c8b2422b0db721bd2d2a1939b384b94851d',
			'salsa20' => '87fefa05eb228b7bae5a61255ee18d0015368c7d3a54913b64197c0a3c4dd3c0d10bb6125810166def084e22f3a5403db10367657e0fd4683944aedb1f089f00',
			'haval128,3' => '0fcc917c01d061d9d6ce98d53ff6651d',
			'haval160,3' => '65efa4200f71c0798491022d7973c93a769e79bb',
			'haval192,3' => '5ea6408ad35cf16c857b87b59ff837bbb2113cf80c679bec',
			'haval224,3' => '131ea16210f01677ed333ea05151d0c08dbfa41d4cbb552fc04f2615',
			'haval256,3' => 'c7bee2a979d6ee1877e08405dce99cb8b6618a972558424bc979778f524feccf',
			'haval128,4' => '2c65e658dbcb3c30e46558719d83a2ec',
			'haval160,4' => 'd34e8bfec3588760e78b56862603cea1508c0cc5',
			'haval192,4' => '53d7eeb56a6a5e6c9eef80e402c2012e4e3f4b7a2264622a',
			'haval224,4' => 'bb3da507725095b720c5ad3580e6f63782f28ff56770ff2bdd8f8657',
			'haval256,4' => 'a0473e026671a61d0c1bd114da372b2c3259d32b8ca7eaa762422d67848547de',
			'haval128,5' => '271e448080d18564e52cfbfa289e9536',
			'haval160,5' => '5c4a2b881e9a55c843daff818666f6a0c01a626f',
			'haval192,5' => 'f0684a82e5cb9b4de0103834dedb05c6c3e2787311aae655',
			'haval224,5' => '2efa31a4b25dcf79f24012c39e717ec8c48a75ab28937692c8c71f24',
			'haval256,5' => '6d8a5995b8a5e074b7a8d447c4c416cf3d1a59ec6c6d3f8343c17aca73c33c7a',
		);

		return $hashes;
	}

}
