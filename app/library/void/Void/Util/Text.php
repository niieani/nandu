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

require_once('mbfunctions.php');

/**
 * Various text utilities.
 *
 * @author Jakub Argasiński <argasek@gmail.com>
 *
 */
class Void_Util_Text {
	/**
	 * Default ellipsis string
	 *
	 * @var string
	 */
	const DEFAULT_TRUNCATE_ELLIPSIS = ' (…)';

	/**
	 * Matches Unicode characters that are word boundaries.
	 *
	 * @see http://unicode.org/glossary
	 *
	 * Characters with the following General_category (gc) property values are used
	 * as word boundaries. While this does not fully conform to the Word Boundaries
	 * algorithm described in http://unicode.org/reports/tr29, as PCRE does not
	 * contain the Word_Break property table, this simpler algorithm has to do.
	 * - Cc, Cf, Cn, Co, Cs: Other.
	 * - Pc, Pd, Pe, Pf, Pi, Po, Ps: Punctuation.
	 * - Sc, Sk, Sm, So: Symbols.
	 * - Zl, Zp, Zs: Separators.
	 *
	 * Non-boundary characters include the following General_category (gc) property
	 * values:
	 * - Ll, Lm, Lo, Lt, Lu: Letters.
	 * - Mc, Me, Mn: Combining Marks.
	 * - Nd, Nl, No: Numbers.
	 *
	 * Note that the PCRE property matcher is not used because we wanted to be
	 * compatible with Unicode 5.2.0 regardless of the PCRE version used (and any
	 * bugs in PCRE property tables).
	 *
	 * @see http://api.drupal.org/api/function/truncate_utf8/7
	 * @license GNU General Public License, version 2 and later
	 * @var string
	 */
	const PREG_UNICODE_WORD_BOUNDARY = '\x{0}-\x{2F}\x{3A}-\x{40}\x{5B}-\x{60}\x{7B}-\x{A9}\x{AB}-\x{B1}\x{B4}\x{B6}-\x{B8}\x{BB}\x{BF}\x{D7}\x{F7}\x{2C2}-\x{2C5}\x{2D2}-\x{2DF}\x{2E5}-\x{2EB}\x{2ED}\x{2EF}-\x{2FF}\x{375}\x{37E}-\x{385}\x{387}\x{3F6}\x{482}\x{55A}-\x{55F}\x{589}-\x{58A}\x{5BE}\x{5C0}\x{5C3}\x{5C6}\x{5F3}-\x{60F}\x{61B}-\x{61F}\x{66A}-\x{66D}\x{6D4}\x{6DD}\x{6E9}\x{6FD}-\x{6FE}\x{700}-\x{70F}\x{7F6}-\x{7F9}\x{830}-\x{83E}\x{964}-\x{965}\x{970}\x{9F2}-\x{9F3}\x{9FA}-\x{9FB}\x{AF1}\x{B70}\x{BF3}-\x{BFA}\x{C7F}\x{CF1}-\x{CF2}\x{D79}\x{DF4}\x{E3F}\x{E4F}\x{E5A}-\x{E5B}\x{F01}-\x{F17}\x{F1A}-\x{F1F}\x{F34}\x{F36}\x{F38}\x{F3A}-\x{F3D}\x{F85}\x{FBE}-\x{FC5}\x{FC7}-\x{FD8}\x{104A}-\x{104F}\x{109E}-\x{109F}\x{10FB}\x{1360}-\x{1368}\x{1390}-\x{1399}\x{1400}\x{166D}-\x{166E}\x{1680}\x{169B}-\x{169C}\x{16EB}-\x{16ED}\x{1735}-\x{1736}\x{17B4}-\x{17B5}\x{17D4}-\x{17D6}\x{17D8}-\x{17DB}\x{1800}-\x{180A}\x{180E}\x{1940}-\x{1945}\x{19DE}-\x{19FF}\x{1A1E}-\x{1A1F}\x{1AA0}-\x{1AA6}\x{1AA8}-\x{1AAD}\x{1B5A}-\x{1B6A}\x{1B74}-\x{1B7C}\x{1C3B}-\x{1C3F}\x{1C7E}-\x{1C7F}\x{1CD3}\x{1FBD}\x{1FBF}-\x{1FC1}\x{1FCD}-\x{1FCF}\x{1FDD}-\x{1FDF}\x{1FED}-\x{1FEF}\x{1FFD}-\x{206F}\x{207A}-\x{207E}\x{208A}-\x{208E}\x{20A0}-\x{20B8}\x{2100}-\x{2101}\x{2103}-\x{2106}\x{2108}-\x{2109}\x{2114}\x{2116}-\x{2118}\x{211E}-\x{2123}\x{2125}\x{2127}\x{2129}\x{212E}\x{213A}-\x{213B}\x{2140}-\x{2144}\x{214A}-\x{214D}\x{214F}\x{2190}-\x{244A}\x{249C}-\x{24E9}\x{2500}-\x{2775}\x{2794}-\x{2B59}\x{2CE5}-\x{2CEA}\x{2CF9}-\x{2CFC}\x{2CFE}-\x{2CFF}\x{2E00}-\x{2E2E}\x{2E30}-\x{3004}\x{3008}-\x{3020}\x{3030}\x{3036}-\x{3037}\x{303D}-\x{303F}\x{309B}-\x{309C}\x{30A0}\x{30FB}\x{3190}-\x{3191}\x{3196}-\x{319F}\x{31C0}-\x{31E3}\x{3200}-\x{321E}\x{322A}-\x{3250}\x{3260}-\x{327F}\x{328A}-\x{32B0}\x{32C0}-\x{33FF}\x{4DC0}-\x{4DFF}\x{A490}-\x{A4C6}\x{A4FE}-\x{A4FF}\x{A60D}-\x{A60F}\x{A673}\x{A67E}\x{A6F2}-\x{A716}\x{A720}-\x{A721}\x{A789}-\x{A78A}\x{A828}-\x{A82B}\x{A836}-\x{A839}\x{A874}-\x{A877}\x{A8CE}-\x{A8CF}\x{A8F8}-\x{A8FA}\x{A92E}-\x{A92F}\x{A95F}\x{A9C1}-\x{A9CD}\x{A9DE}-\x{A9DF}\x{AA5C}-\x{AA5F}\x{AA77}-\x{AA79}\x{AADE}-\x{AADF}\x{ABEB}\x{D800}-\x{F8FF}\x{FB29}\x{FD3E}-\x{FD3F}\x{FDFC}-\x{FDFD}\x{FE10}-\x{FE19}\x{FE30}-\x{FE6B}\x{FEFF}-\x{FF0F}\x{FF1A}-\x{FF20}\x{FF3B}-\x{FF40}\x{FF5B}-\x{FF65}\x{FFE0}-\x{FFFD}';

	/**
	 * Returns var_dump($variable) output as string.
	 *
	 * @author Jakub Argasiński <argasek@gmail.com>
	 * @param mixed $variable
	 * @param boolean $html If true, outputs text inside '<pre></pre>' tags. Default: false
	 * @return string
	 */
	public static function varDump($variable, $html = false) {
		ob_start();
		echo ($html ? '<pre>' : '');
		var_dump($variable);
		echo ($html ? '</pre>' : '');
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	/**
	 * Returns var_export($variable) output as string.
	 *
	 * @param mixed $variable
	 * @param boolean $html If true, outputs text inside '<pre></pre>' tags. Default: false
	 * @author Jakub Argasiński <argasek@gmail.com>
	 * @return string
	 */
	public static function varExport($variable, $html = false) {
		ob_start();
		echo ($html ? '<pre>' : '');
		var_export($variable);
		echo ($html ? '</pre>' : '');
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	/**
	 * Returns beautified JSON output.
	 *
	 * @author Umbrae <umbrae@gmail.com>
	 * @param string $json JSON-encoded structure
	 * @return string
	 */
	public static function jsonPrettyPrint($json) {
		$tab = "  ";
		$new_json = "";
		$indent_level = 0;
		$in_string = false;

		$json_obj = Zend_Json::decode($json);

		if ($json_obj === false) return false;

		$json = Zend_Json::encode($json_obj);
		$len = mb_strlen($json);

		for($c = 0; $c < $len; $c++) {
			$char = $json[$c];
			switch($char)			{
				case '{':
				case '[':
					if(!$in_string)					{
						$new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
						$indent_level++;
					}	else {
						$new_json .= $char;
					}
					break;
				case '}':
				case ']':
					if(!$in_string) {
						$indent_level--;
						$new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
					}	else {
						$new_json .= $char;
					}
					break;
				case ',':
					if(!$in_string)	{
						$new_json .= ",\n" . str_repeat($tab, $indent_level);
					} else {
						$new_json .= $char;
					}
					break;
				case ':':
					if(!$in_string)	{
						$new_json .= ": ";
					}	else {
						$new_json .= $char;
					}
					break;
				case '"':
					if($c > 0 && $json[$c-1] != '\\')	{
						$in_string = !$in_string;
					}
				default:
					$new_json .= $char;
					break;
			}
		}

		return $new_json;
	}


	/**
	 * Takes xml as a string and returns it nicely indented
	 *
	 * @author Will Bond
	 * @param string $xml The xml to beautify
	 * @param boolean $html_output If the xml should be formatted for display on an html page
	 * @return string The beautified xml
	 */
	public static function xmlPrettyPrint($xml, $html_output = false) {
		$xml_obj = new SimpleXMLElement($xml);
		$xml_lines = explode("\n", $xml_obj->asXML());
		$indent_level = 0;

		$new_xml_lines = array();
		foreach ($xml_lines as $xml_line) {
			if (preg_match('#^(<[a-z0-9_:-]+((\s+[a-z0-9_:-]+="[^"]+")*)?>.*<\s*/\s*[^>]+>)|(<[a-z0-9_:-]+((\s+[a-z0-9_:-]+="[^"]+")*)?\s*/\s*>)#i', ltrim($xml_line))) {
				$new_line = str_pad('', $indent_level*4) . ltrim($xml_line);
				$new_xml_lines[] = $new_line;
			} elseif (preg_match('#^<[a-z0-9_:-]+((\s+[a-z0-9_:-]+="[^"]+")*)?>#i', ltrim($xml_line))) {
				$new_line = str_pad('', $indent_level*4) . ltrim($xml_line);
				$indent_level++;
				$new_xml_lines[] = $new_line;
			} elseif (preg_match('#<\s*/\s*[^>/]+>#i', $xml_line)) {
				$indent_level--;
				if (trim($new_xml_lines[sizeof($new_xml_lines)-1]) == trim(str_replace("/", "", $xml_line))) {
					$new_xml_lines[sizeof($new_xml_lines)-1] .= $xml_line;
				} else {
					$new_line = str_pad('', $indent_level*4) . $xml_line;
					$new_xml_lines[] = $new_line;
				}
			} else {
				$new_line = str_pad('', $indent_level*4) . $xml_line;
				$new_xml_lines[] = $new_line;
			}
		}

		$xml = join("\n", $new_xml_lines);
		return ($html_output) ? '<pre>' . htmlentities($xml) . '</pre>' : $xml;
	}

	/**
	 * Truncate string to $n characters maximum.
	 *
	 * @see http://api.drupal.org/api/function/truncate_utf8/7
	 * @license GNU General Public License, version 2 and later
	 *
	 * @param string $string The string to truncate.
	 * @param integer $max_length An upper limit on the returned string length, including trailing ellipsis if $ellipsis is a string.
	 * @param bool $wordsafe If true, attempt to truncate on a word boundary. Word boundaries are spaces, punctuation, and Unicode characters used as word boundaries in non-Latin languages; see PREG_CLASS_UNICODE_WORD_BOUNDARY for more information. If a word boundary cannot be found that would make the length of the returned string fall within length guidelines (see parameters $max_return_length and $min_wordsafe_length), word boundaries are ignored.
	 * @param bool|string $ellipsis If given a string, add this string (if given string is empty, default to DEFAULT_TRUNCATE_ELLIPSIS) to the end of the truncated string (defaults to false, don't add anything). The string length will still fall within $max_return_length.
	 * @param integer $min_wordsafe_length If $wordsafe is true, the minimum acceptable length for truncation (before adding an ellipsis, if $add_ellipsis is true). Has no effect if $wordsafe is false. This can be used to prevent having a very short resulting string that will not be understandable. For instance, if you are truncating the string "See myverylongurlexample.com for more information" to a word-safe return length of 20, the only available word boundary within 20 characters is after the word "See", which wouldn't leave a very informative string. If you had set $min_wordsafe_length to 10, though, the function would realise that "See" alone is too short, and would then just truncate ignoring word boundaries, giving you "See myverylongurl..." (assuming you had set $ellipses to non-false).
	 *
	 * @return The truncated string
	 */
	public static function truncate($string, $max_length, $wordsafe = false, $ellipsis = false, $min_wordsafe_length = 1) {
		$max_length = max($max_length, 0);
		$min_wordsafe_length = max($min_wordsafe_length, 0);

		if (mb_strlen($string) <= $max_length) {
			// No truncation needed, so don't add ellipsis, just return.
			return $string;
		}

		if ($ellipsis !== false) {
			// Set default ellipsis if empty string provided
			$ellipsis = ($ellipsis === '' ? self::DEFAULT_TRUNCATE_ELLIPSIS : $ellipsis);
			// Truncate ellipsis in case $max_length is small.
			$ellipsis = mb_substr($ellipsis, 0, $max_length);
			$max_length -= mb_strlen($ellipsis);
			$max_length = max($max_length, 0);
		}

		if ($max_length <= $min_wordsafe_length) {
			// Do not attempt word-safe if lengths are bad.
			$wordsafe = false;
		}

		if ($wordsafe === true) {
			$matches = array();
			// Find the last word boundary, if there is one within $min_wordsafe_length
			// to $max_length characters. preg_match() is always greedy, so it will
			// find the longest string possible.
			$found = preg_match('/^(.{' . $min_wordsafe_length . ',' . $max_length . '})[' . self::PREG_UNICODE_WORD_BOUNDARY . ']/u', $string, $matches);
			$string = ($found ? $matches[1] : mb_substr($string, 0, $max_length));
		} else {
			$string = mb_substr($string, 0, $max_length);
		}

		$string .= ($ellipsis !== false ? $ellipsis : '');

		return $string;
	}

}
