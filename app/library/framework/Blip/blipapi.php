<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi')) {

    interface IBlipApi_Command { }

    /**
     * Function registered for SPL autoloading - load required class
     *
     * @param array $class_name
     */
    function BlipApi__autoload ($class_name) {
        if (substr ($class_name, 0, 8) == 'BlipApi_') {
            include strtolower ($class_name).'.php';
        }
    }
    spl_autoload_register ('BlipApi__autoload');

    class BlipApi extends BlipApi_Abstract {
        const VERSION = '0.02.32';

        /**
         * CURL handler
         *
         * @access protected
         * @var resource
         */
        protected $_ch;

        /**
         * OAuth token
         *
         * @access protected
         * @var OAuthToken
         */
        protected $_oauth_token;

        /**
         * OAuth consumer
         *
         * @access protected
         * @var OAuthConsumer
         */
        protected $_oauth_consumer;

        /**
         * OAuth signing method
         *
         * @access protected
         * @var OAuthSignatureMethod_HMAC_SHA1
         */
        protected $_oauth_method;

        /**
         * Useragent
         *
         * @access protected
         * @var string
         */
        protected $_uagent          = 'BlipApi.php/0.02.32 (http://blipapi.googlecode.com)';

        /**
         *
         *
         * @access protected
         * @var string
         */
        protected $_referer         = '';

        /**
         * URI to API host
         *
         * @access protected
         * @var string
         */
        protected $_root            = 'http://api.blip.pl';

        /**
         * Mime type for "Accept" header in request
         *
         * @access protected
         * @var string
         */
        protected $_format          = 'application/json';

        /**
         * Timeout for connecting
         *
         * @access protected
         * @var string
         */
        protected $_connect_timeout;

        /**
         * Timeout for command execution
         *
         * @access protected
         * @var string
         */
        protected $_timeout;

        /**
         * Debug mode flag
         *
         * @access protected
         * @var bool
         */
        protected $_debug;

        /**
         * Debug message type
         *
         * @access protected
         * @var bool
         */
        protected $_debug_tpl       = array ('', '');

        /**
         * Headers to be sent
         *
         * @access protected
         * @var array
         */
        protected $_headers         = array ();

        /**
         * Parser for JSON format
         *
         * This needs to contain name of the function for parsing JSON.
         * Alternatively it may be an array with object and its method name:
         * array ($json, 'decode')
         *
         * @access protected
         * @var array
         */
        protected $_parser          = 'json_decode';

        /**
         * BlipApi constructor
         *
         * Initialize CURL handler ({@link $_ch}). Throws RuntimeException exception if no CURL extension found.
         *
         * @param string $oauth_consumer
         * @param string $oauth_token
         */
        public function __construct ($oauth_consumer=null, $oauth_token=null, $dont_connect=false) {
            if (!function_exists ('curl_init')) {
                throw new RuntimeException ('CURL missing!', -1);
            }

            $this->_oauth_consumer  = $oauth_consumer;
            $this->_oauth_token     = $oauth_token;
            if ($oauth_token && $oauth_consumer) {
                $this->_oauth_method    = new OAuthSignatureMethod_HMAC_SHA1 ();
                require_once 'OAuth.php';
            }

            # inicjalizujemy handler curla
            $this->_ch              = curl_init ($this->_root);
            if (!$this->_ch) {
                throw new RuntimeException ('CURL initialize error: '. curl_error ($this->_ch), curl_errno ($this->_ch));
            }

            # ustawiamy domyślne nagłówki
            $this->_headers         = array (
                'Accept'        => $this->format,
                'X-Blip-API'    => '0.02',
            );

            # inicjalizujemy szablon dla debugow
            $this->debug_html       = false;
            # ustawiamy odpowiednie timeouty
            $this->connect_timeout  = 5;
            $this->timeout          = 10;

            if (!$dont_connect) {
                $this->connect ();
            }
        }

        /**
         * BlipApi destructor
         *
         * Close CURL handler, if active
         */
        public function __destruct () {
            if (is_resource ($this->_ch)) {
                curl_close ($this->_ch);
            }
        }

        /**
         * Magic method to execute commands as their names - it makes all dirty job...
         *
         * @param string $fn name of command
         * @param array $args arguments
         * @access public
         * @return return of {@link execute}
         */
        public function __call ($method_name, $args) {
            if (count ($args) < 1) {
                throw new InvalidArgumentException ("Missing method object.");
            }
            else if (!($args[0] instanceof IBlipApi_Command)) {
                throw new InvalidArgumentException ("Unknown command: ".(is_object ($args[0]) ? get_class ($args[0]) : gettype ($args[0])));
            }
            else if (
                !in_array ($method_name, array ('create', 'read', 'update', 'delete')) ||
                !method_exists ($args[0], $method_name)
            ) {
                throw new BadMethodCallException ("Unknown method \"$method_name\".");
            }

            $this->_debug ('CMD: '. get_class ($args[0]).'::'.$method_name);

            # wywołujemy znalezioną metodę aby pobrac dane dla requestu
            $method_data    = call_user_func (array ($args[0], $method_name));
            $url            = $method_data[0];
            $http_method    = $method_data[1];
            $http_data      = array ();
            $opts           = array ();
            if (count ($method_data) > 2) {
                $http_data  = $method_data[2];
            }
            if (count ($method_data) > 3) {
                $opts       = $method_data[3];
                $this->_debug ('method opts', $opts);
            }

            if (isset ($opts['just_return'])) {
                return $opts['just_return'];
            }

            # ustawiamy opcje dla konkretnego typu requestu
            $http_method = strtolower ($http_method);
            switch ($http_method) {
                case 'post':
                    if (!isset ($opts['multipart']) || !$opts['multipart']) {
                        $post_http_data = http_build_query ($http_data);
                    }
                    else {
                        $post_http_data = $http_data;
                    }
                    $this->_debug ('post http data', $post_http_data);

                    $curlopts = array (
                        CURLOPT_POST        => true,
                        CURLOPT_POSTFIELDS  => $post_http_data,
                    );
                break;

                case 'get':
                    $curlopts = array ( CURLOPT_HTTPGET => true );
                    if (count ($http_data)) {
                        $url .= '?' . http_build_query ($http_data);
                    }
                break;

                case 'put':
                    $curlopts = array ( CURLOPT_PUT => true,);
                    if (!count ($http_data)) {
                        $curlopts[CURLOPT_HTTPHEADER] = array ('Content-Length' => 0);
                    }
                    else {
                        $url .= '?' . http_build_query ($http_data);
                    }
                break;

                case 'delete':
                    $curlopts = array ( CURLOPT_CUSTOMREQUEST => 'DELETE' );
                break;

                default:
                    throw new InvalidArgumentException ('Unknown HTTP method.', -1);
            }
            $this->_debug ('METHOD: '. strtoupper ($http_method));

            # ustawiamy url
            $curlopts[CURLOPT_URL]  = $this->_root . $url;
            $this->_debug ('Connecting to: '. $curlopts[CURLOPT_URL]);

            $headers_names          = null;
            # jesli trzeba to dodajemy jednorazowe nagłówki które mamy wysłać
            if (isset ($curlopts[CURLOPT_HTTPHEADER])) {
                $this->headers_set ($curlopts[CURLOPT_HTTPHEADER]);
                $headers_names = array_keys ($curlopts[CURLOPT_HTTPHEADER]);
            }

            $headers = array ();

            # nagłówki do wysłania
            if ($this->_headers) {
                foreach ($this->_headers as $k=>$v) {
                    $headers[] = sprintf ('%s: %s', $k, $v);
                }
            }

            ## jesli mamy oauth, to podpisujemy request - tworzymy dodatkowy OAUth-owy naglowek
            if ($this->_oauth_consumer && $this->_oauth_token) {
                $oauth_request = OAuthRequest::from_consumer_and_token (
                    $this->_oauth_consumer,
                    $this->_oauth_token,
                    strtoupper ($http_method),
                    $curlopts[CURLOPT_URL],
                    (isset ($opts['multipart']) ? array () : $http_data)
                );
                $oauth_request->sign_request ($this->_oauth_method, $this->_oauth_consumer, $this->_oauth_token);

                $headers[] = $oauth_request->to_header ();
            }

            if (count ($headers)) {
                $curlopts[CURLOPT_HTTPHEADER] = $headers;
            }

            $this->_debug ('post2', print_r ($this->_headers, 1), $headers, print_r ($headers_names, 1));
            $this->_debug ('DATA: '. print_r ($http_data, 1), 'CURLOPTS: '.print_r ($this->_debug_curlopts ($curlopts), 1));

            if (!curl_setopt_array ($this->_ch, $curlopts)) {
                throw new RuntimeException (curl_error ($this->_ch), curl_errno ($this->_ch));
            }

            # wykonujemy zapytanie
            $reply = curl_exec ($this->_ch);

            # usuwamy z zestawu naglowkow do wyslania te ktore mialy byc jednorazowe
            if (isset ($headers_names)) {
                $this->headers_remove ($headers_names);
            }
            $this->_debug ('post3', print_r ($this->_headers, 1));

            if (!$reply) {
                throw new RuntimeException ('CURL Error: '. curl_error ($this->_ch), curl_errno ($this->_ch));
            }

            $reply = $this->__parse_reply ($reply);

            if ($reply['status_code'] >= 400) {
                throw new RuntimeException ($reply['status_body'], $reply['status_code']);
            }
            ## hack na 302 i przekierowanie na strone gg
            else if (
                $reply['status_code'] == 302 &&
                isset ($reply['headers']['location']) &&
                stripos ($reply['headers']['location'], 'http://help.gadu-gadu.pl/errors') === 0
            ) {
                throw new RuntimeException ('Service Unavailable', 503);
            }

            return $reply;
        }

        /**
         * Setter for {@link $_debug} property
         *
         * @param bool $enable
         * @access protected
         */
        protected function __set_debug ($enable = null) {
            $this->_debug = $enable ? true : false;

            curl_setopt($this->_ch, CURLOPT_VERBOSE, $this->_debug);
        }

        /**
         * Setter for {@link $_debug_html} property
         *
         * @param bool $enable
         * @access protected
         */
        protected function __set_debug_html ($enable = null) {
            if ($enable) {
                $this->_debug_tpl = array (
                    "<pre style='border: 1px solid black; padding: 4px;'><b>DEBUG MSG:</b>\n",
                    "</pre>\n",
                );
            }
            else {
                $this->_debug_tpl = array (
                    "DEBUG MSG:\n",
                    "\n",
                );
            }
        }

        /**
         * Setter for {@link $_format} property
         *
         * Format have to be string in mime type format. In other case, there will be prepended 'application/' prefix.
         *
         * @param string $format
         * @access protected
         */
        protected function __set_format ($format) {
            # jeśli nie jest to pełen typ mime, to doklejamy na początek 'application/'
            if ($format && strpos ($format, '/') === false) {
                $format = 'application/'. $format;
            }
            $this->_format = $format;
        }

        /**
         * Setter for {@link $_uagent} property
         *
         * @param string $uagent
         * @access protected
         */
        protected function __set_uagent ($uagent) {
            $this->_uagent = (string) $uagent;
            curl_setopt ($this->_ch, CURLOPT_USERAGENT, $this->_uagent);
        }

        /**
         * Setter for {@link $_referer} property
         *
         * @param string $referer
         * @access protected
         */
        protected function __set_referer ($referer) {
            $this->_referer = (string) $referer;
            curl_setopt ($this->_ch, CURLOPT_REFERER, $referer);
        }

        /**
         * Setter for {@link $_parser} property
         *
         * @param mixed $parser string|array - arguments for call_user_func
         * @access protected
         */
        protected function __set_parser ($data) {
            if (
                (is_string ($parser) && function_exists ($parser))
                ||
                (
                    is_array ($parser) && count ($parser) == 2 &&
                        (
                            (is_object ($parser[0]) && method_exists ($parser[0], $parser[1]))
                            ||
                            (is_string ($parser[0]) && class_exists ($parser[0]) && method_exists ($parser[0], $parser[1]))
                        )
                )
            ) {
                $this->_parser = $parser;
            }

            else {
                if (is_array ($parser)) {
                    $parser = (is_string ($parser[0]) ? $parser[0] : get_class ($parser[0])) . '::' . $parser[1];
                }
                throw new BadFunctionCallException ('Specified parser not found: '. $parser .'.');
            }
        }

        /**
         * Setter for {@link $_connect_timeout} property
         *
         * @param string $timeout
         * @access protected
         */
        protected function __set_connect_timeout ($timeout) {
            $this->_connect_timeout = (int) $timeout;
            curl_setopt ($this->_ch, CURLOPT_CONNECTTIMEOUT, $this->_connect_timeout);
        }

        /**
         * Setter for {@link $_timeout} property
         *
         * @param string $timeout
         * @access protected
         */
        protected function __set_timeout ($timeout) {
            $this->_timeout = (int) $timeout;
            curl_setopt ($this->_ch, CURLOPT_TIMEOUT, $this->_timeout);
        }

        /**
         * Setter for {@link $_headers} property
         *
         * @param array|string $headers headers in format specified at {@link _parse_headers}
         * @access protected
         */
        protected function __set_headers ($headers) {
            $this->_headers = $this->_parse_headers ($headers);
        }

        /**
         * Parsing headers parameter to correct format
         *
         * Param $headers have to be an array, where key is header name, and value - header value, or string in
         * 'Header-Name: Value'.
         * Throws InvalidArgumentException of incorect type of $headers is given
         *
         * @param array|string $headers
         * @access protected
         */
        protected function _parse_headers ($headers) {
            if (!$headers) {
                $headers = array ();
            }
            else if (is_string ($headers) && preg_match ('/^(\w+):\s(.*)/', $headers, $match)) {
                $headers = array ( $match[1] => $match[2] );
            }
            else if (!is_array ($headers)) {
                throw new InvalidArgumentException (sprintf ('%s::$headers have to be an array or string, but %s given.',
                    __CLASS__,
                    gettype ($headers)), -1
                );
            }

            return $headers;
        }

        /**
         * Add or replace headers to be sent to remote server
         *
         * @param array|string $headers headers in format specified at {@link _parse_headers}
         * @access public
         * @return bool false if empty array specified
         */
        public function headers_set ($headers) {
            $headers = $this->_parse_headers ($headers);
            if (!$headers) {
                return false;
            }

            foreach ($headers as $k=>$v) {
                $this->_headers[$k] = $v;
            }
            return true;
        }

        /**
         * Remove specified header
         *
         * @param array|string $headers headers in format specified at {@link _parse_headers}
         * @access public
         * @return bool false if empty array specified
         */
        public function headers_remove ($headers) {
            $headers = $this->_parse_headers ($headers);
            if (!$headers) {
                return false;
            }

            foreach ($headers as $k=>$v) {
                if (isset ($this->_headers[$k])) {
                    unset ($this->_headers[$k]);
                }
            }
            return true;
        }

        /**
         * Get headers set to sent
         *
         * $headers have to be:
         *  * array - with names of headers values to return
         *  * string - with single header name
         *  * null - if all headers have to be returned
         *
         * @param mixed $headers
         * @access public
         * @return array
         */
        public function headers_get ($headers=null) {
            if (is_null ($headers)) {
                return $this->_headers;
            }
            else if (is_string ($headers)) {
                $headers = array ($headers);
            }
            else if (!is_array ($headers)) {
                throw new InvalidArgumentException ('Incorrect value specified.', -1);
            }

            $ret = array ();
            foreach ($headers as $header) {
                $ret[$header] = (isset ($this->_headers[$header])) ? $this->_headers[$header] : null;
            }
            return $ret;
        }

        /**
         * Create connection with CURL, setts some CURL options etc
         *
         * Throws RuntimeException exception when CURL initialization has failed
         *
         * @access public
         * @return bool always true
         */
        public function connect () {
            # standardowe opcje curla
            $curlopts = array (
                CURLOPT_USERAGENT       => $this->uagent,
                CURLOPT_RETURNTRANSFER  => 1,
                CURLOPT_HEADER          => true,
                CURLOPT_HTTP200ALIASES  => array (201, 204),
                CURLOPT_CONNECTTIMEOUT  => 10,
            );

            # ustawiamy opcje
            curl_setopt_array ($this->_ch, $curlopts);

            return true;
        }

        /**
         * Execute command and parse reply
         *
         * Throws InvalidArgumentException exception when specified command does not exists, or RuntimeException
         * when exists some CURL error or returned status code is greater or equal 400.
         *
         * Internally using magic method BlipApi::__call.
         *
         * @param string $command command to execute
         * @param mixed $options,... options passed to proper command method (prefixed with _cmd__)
         * @access public
         * @return array like {@link __call}
         */
        public function execute () {
            if (!func_num_args ()) {
                throw new InvalidArgumentException ('Command missing.', -1);
            }
            $args   = func_get_args ();
            $fn     = array_shift ($args);
            return call_user_func_array (array ($this, $fn), $args);
        }

        /**
         * Print debug mesage if debug mode is enabled
         *
         * @param string $msg,... messages to print to stdout
         * @access protected
         * @return bool
         */
        protected function _debug () {
            if (!$this->_debug) {
                return;
            }

            $args = func_get_args ();

            echo $this->_debug_tpl[0];
            foreach ($args as $i=>$arg) {
                printf ("%d. %s\n", $i, print_r ($arg, 1));
            }
            echo $this->_debug_tpl[1];

            return 1;
        }

        /**
         * Return array with CURLOPT_* constants values replaced by these names. For debugging purposes only.
         *
         * @param array $opts array with CURLOPTS_* as keys
         * @return array the same as $opts, but keys are replaced by names of constants
         * @access protected
         */
        protected function _debug_curlopts ($opts) {
            $copts = array ();
            foreach (get_defined_constants () as $k => $v) {
                if (strlen ($k) > 8 && substr ($k, 0, 8) == 'CURLOPT_') {
                    $copts[$v] = $k;
                }
            }

            $ret = array ();
            foreach ($opts as $k => $v) {
                if (isset ($copts, $k)) {
                    $ret[$copts[$k]] = $v;
                }
                else {
                    $ret[$k] = $v;
                }
            }

            return $ret;
        }

        /**
         * Parse reply
         *
         * Throws BadFunctionCallException exception when specified parser was not found.
         * Return array with keys
         *  * headers - (array) array of headers (keys are lowercased)
         *  * body - (mixed) body of response. If reply's mime type is found in {@link $_parser}, then contains reply of specified parser, in other case contains raw string reply.
         *  * body_parse - (bool) if true, content was successfully parsed by specified parser
         *  * status_code - (int) status code from server
         *  * status_body - (string) content of status
         *
         * @param string $reply
         * @return array
         * @access protected
         */
        protected function __parse_reply ($reply) {
            ## rozdzielamy nagłówki od treści
            $reply          = preg_split ("/\r?\n\r?\n/mu", $reply, 2);
            ## HTTP1.1 pozwala na wyslanie kilku czesci naglowkow, oddzielonych znakiem nowej linii.
            while (strtolower (substr ($reply[1], 0, 5)) == 'http/') {
                $reply = preg_split ("/\r?\n\r?\n/mu", $reply[1], 2);
            }
            $body           = isset ($reply[1]) ? $reply[1] : '';
            $headers        = $reply[0];

            # parsujemy nagłówki
            $headers        = preg_split ("!\r?\n!mu", $headers);

            # usuwamy typ protokołu
            $header_http    = array_shift ($headers);
            $headers_parsed = array ();
            $header_name    = '';
            foreach ($headers as $header) {
                if ($header[0] == ' ' || $header[0] == "\t") {
                    $headers_parsed[$header_name] .= trim ($header);
                }
                else {
                    $header                         = preg_split ('/\s*:\s*/', trim ($header), 2);
                    $header_name                    = strtolower ($header[0]);
                    $headers_parsed[$header_name]   = $header[1];
                }
            }
            $headers = &$headers_parsed;

            # określamy kod statusu
            if (
                (isset ($headers['status']) && preg_match ('/(\d+)\s+(.*)/u', $headers['status'], $match))
                ||
                (preg_match ('!HTTP/(1\.[01])\s+(\d+)\s+([\w ]+)!', $header_http, $match))
            ) {
                $status = array ( $match[1], $match[2], $match[3] );
            }
            else {
                $status = array (1.0, 0, '');
            }

            # parsujemy treść odpowiedzi, jeśli mamy odpowiedni parser
            $body_parsed    = false;
            $body_tmp       = call_user_func ($this->_parser, $body);
            if ($body_tmp) {
                $body_parsed    = true;
                $body           = $body_tmp;
            }

            return array (
                'headers'       => $headers,
                'body'          => $body,
                'body_parsed'   => $body_parsed,
                'http_version'  => $status[0],
                'status_code'   => $status[1],
                'status_body'   => $status[2],
            );
        }
    }
}

// vim: fdm=manual
