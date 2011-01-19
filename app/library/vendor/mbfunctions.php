<?php

/*
 * mbfunctions
 * PHP Multibyte Functions for PHP < 6
 *
 * Author: Ignacio Lago (www.ignaciolago.es)
 * Version: 1.0
 * Timestamp: 20091222
 *
 * The mainly purpouse is to use UTF-8 in PHP 5 while PHP 6 is not out & ready. Or what PHP < 6 should do but it doesn't! damn!
 *
 * All these functions extend the PHP 5 core mb functions (multibyte). You should use them to use php in no-ascii (utf8) environments.
 *
 * The functions are defined with the same arguments and returns that their no-multibyte counterparts.
 *
 * Note: Some of them could have added arguments with default values defined.
 *
 * Modified by: Jakub Argasiński <argasek@gmail.com>
 * - filename: mbfunctions-1.0.20100223.inc.php => mbfunctions.php
 * - more accented characters
 * - not accented characters moved to a constant
 * - changed constant names to more sophisticated ones to avoid possible collisions
 * - default locale from es_ES to pl_PL
 * - added mb_ltrim() and mb_rtrim() from http://code.google.com/p/mbfunctions/issues/detail?id=1
 *
 * @todo:
 *   $a = array('Ĳ', 'ĳ',  'Œ', 'œ', 'Ǽ', 'ǽ', 'Æ', 'æ');
 *   $b = array('IJ', 'ij', 'OE', 'oe', 'AE', 'ae', 'AE', 'ae');
 *
 */


/*
 * The main secret, the core of the magic is...
 *    utf8_decode ($str);
 *    utf8_encode ($str);
 */

define('UTF8_ENCODED_CHARLIST',  'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåçèéêëìíîïñòóôõöøùúûüýÿĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĴĵĶķĹĺĻļĽľĿŀŁłŃńŅņŇňŉŌōŎŏŐőŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽžſƒƠơƯưǍǎǏǐǑǒǓǔǕǖǗǘǙǚǛǜǺǻǾǿ');
define('UTF8_NOACCENT_CHARLIST', 'AAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaceeeeiiiinoooooouuuuyyAaAaAaCcCcCcCcDdDdEeEeEeEeEeGgGgGgGgHhHhIiIiIiIiIiJjKkLlLlLlLlllNnNnNnnOoOoOoRrRrRrSsSsSsSsTtTtTtUuUuUuUuUuUuWwYyYZzZzZzsfOoUuAaIiOoUuUuUuUuUuAaOo');
define('UTF8_DECODED_CHARLIST',  utf8_decode(UTF8_ENCODED_CHARLIST));

if (! function_exists ('mb_init'))
{
   function mb_init($locale = 'pl_PL')
   {
      /*
       * Setting the Content-Type header with charset
       */
      setlocale(LC_CTYPE, $locale.'.UTF-8');
      iconv_set_encoding("output_encoding", "UTF-8");
      mb_internal_encoding('UTF-8');
      mb_regex_encoding('UTF-8');
   }
}

if (! function_exists ('mb_ucfirst'))
{
   function mb_ucfirst ($str)
   {
      return utf8_encode (ucfirst (utf8_decode($str)));
   }
}

if (! function_exists ('mb_lcfirst'))
{
   function mb_lcfirst ($str)
   {
      return utf8_encode (lcfirst (utf8_decode($str)));
   }
}

if (! function_exists ('mb_ucwords'))
{
   function mb_ucwords ($str)
   {
      return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
   }
}

if (! function_exists ('mb_strip_accents'))
{
   function mb_strip_accents ($string)
   {
      $keys = preg_split('//u', UTF8_ENCODED_CHARLIST, -1, PREG_SPLIT_NO_EMPTY);
      $values = preg_split('//u', UTF8_NOACCENT_CHARLIST, -1, PREG_SPLIT_NO_EMPTY);
      $replace_pairs = array_combine($keys, $values);
      return strtr ($string, $replace_pairs);
   }
}

if (! function_exists ('mb_strtr'))
{
   function mb_strtr ($str, $from, $to = null)
   {
      if(is_array($from))
      {
         foreach($from as $k => $v)
         {
            $utf8_from[utf8_decode($k)]=utf8_decode($v);
         }
         return utf8_encode (strtr (utf8_decode ($str), $utf8_from));
      }
      return utf8_encode (strtr (utf8_decode ($str), utf8_decode($from), utf8_decode ($to)));
   }
}

if (! function_exists('mb_preg_replace'))
{
   function mb_preg_replace($pattern, $replacement, $subject, $limit = -1, &$count = null)
   {
      if(is_array($pattern))
         foreach($pattern as $k => $v)
            $utf8_pattern[utf8_decode($k)]=utf8_decode($v);
      else
         $utf8_pattern=utf8_decode($pattern);

      if(is_array($replacement))
         foreach($replacement as $k => $v)
            $utf8_replacement[utf8_decode($k)]=utf8_decode($v);
      else
         $utf8_replacement=utf8_decode($replacement);

      if(is_array($subject))
         foreach($subject as $k => $v)
            $utf8_subject[utf8_decode($k)]=utf8_decode($v);
      else
         $utf8_subject=utf8_decode($subject);

      $r = preg_replace ($utf8_pattern,$utf8_replacement,$utf8_subject,$limit,$count);

      if(is_array($r))
         foreach($r as $k => $v)
            $return[utf8_encode($k)]=utf8_encode($v);
      else
         $return = utf8_encode($r);

      return $return;
   }
}

if (! function_exists ('mb_str_word_count'))
{
   function mb_str_word_count ($string, $format = 0, $charlist = UTF8_DECODED_CHARLIST)
   {
      /*
       * format
       * 0 - returns the number of words found
       * 1 - returns an array containing all the words found inside the string
       * 2 - returns an associative array, where the key is the numeric position of the word inside the string and the value is the actual word itself
       */
      $r = str_word_count(utf8_decode($string),$format,$charlist);
      if($format == 1 || $format == 2)
      {
         foreach($r as $k => $v)
         {
            $u[$k] = utf8_encode($v);
         }
         return $u;
      }
      return $r;
   }
}

if (! function_exists ('mb_html_entity_decode'))
{
   function mb_html_entity_decode ($string, $quote_style = ENT_COMPAT, $charset = 'UTF-8')
   {
      return html_entity_decode ($string, $quote_style, $charset);
   }
}

if (! function_exists ('mb_htmlentities'))
{
   function mb_htmlentities ($string, $quote_style = ENT_COMPAT, $charset = 'UTF-8', $double_encode = true)
   {
      return htmlentities ($string, $quote_style, $charset, $double_encode);
   }
}

if (! function_exists ('mb_trim'))
{
   function mb_trim ($string, $charlist = null)
   {
      if($charlist == null)
      {
         return utf8_encode(trim (utf8_decode($string)));
      }
      return utf8_encode(trim (utf8_decode($string), utf8_decode($string)));
   }
}

/************************ EXPERIMENTAL ZONE ************************/

if (! function_exists('mb_strip_tags_all'))
{
   function mb_strip_tags_all($document,$repl = ''){
      $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                     '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                     '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                     '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
      );
      $text = mb_preg_replace($search, $repl, $document);
      return $text;
   }
}

if (! function_exists('mb_strip_tags'))
{
   function mb_strip_tags($document,$repl = ''){
      $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                     '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                     '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                     '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
      );
      $text = mb_preg_replace($search, $repl, $document);
      return $text;
   }
}

if (! function_exists('mb_strip_urls'))
{
   function mb_strip_urls($txt, $repl = ' ')
   {
      $txt = mb_preg_replace('@http[s]?://[^\s<>"\']*@',$repl,$txt);
      return $txt;
   }
}

// parse strings as identifiers
if(!function_exists('mb_string_url'))
{
   function mb_string_url($string, $to_lower = true)
   {
      $string = mb_strtolower($string);
      $string = mb_strip_accents($string);
      $string = preg_replace('@[^a-z0-9]@',' ',$string);
      $string = preg_replace('@\s+@','-',$string);
      return $string;
   }
}

if (! function_exists ('mb_rtrim'))
{
   function mb_rtrim ($string, $charlist = null)
   {
      if($charlist == null)
      {
         return utf8_encode(rtrim (utf8_decode($string)));
      }
      return utf8_encode(rtrim (utf8_decode($string), utf8_decode($string)));
   }
}

if (! function_exists ('mb_ltrim'))
{
   function mb_ltrim ($string, $charlist = null)
   {
      if($charlist == null)
      {
         return utf8_encode(ltrim (utf8_decode($string)));
      }
      return utf8_encode(ltrim (utf8_decode($string), utf8_decode($string)));
   }
}
