<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_status.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_status.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_Status')) {
    class BlipApi_Status extends BlipApi_Abstract implements IBlipApi_Command {
        /**
         * Body of message
         *
         * @access protected
         * @var string
         */
        protected $_body;

        /**
         * ID of item to read
         *
         * @access protected
         * @var int
         */
        protected $_id;

        /**
         * Include some additional data in respond to read method.
         * More info: http://www.blip.pl/api-0.02.html#parametry
         *
         * @access protected
         * @var string|array
         */
        protected $_include;

        /**
         * Limit read results to $_limit items
         *
         * @access protected
         * @var int
         */
        protected $_limit   = 10;

        /**
         * Offset for read result set
         *
         * @access protected
         * @var int
         */
        protected $_offset  = 0;

        /**
         * Path to image
         *
         * @access protected
         * @var string
         */
        protected $_image;

        /**
         * ID of item where data is being set.
         *
         * @access protected
         * @var int
         */
        protected $_since_id;

        /**
        * User name
        *
        * @access protected
        * @var string
        */
        protected $_user;

        /**
         * Setter for field: body
         *
         * @param string $value
         * @access protected
         */
        protected function __set_body ($value) {
            $this->_body = $value;
        }

        /**
         * Setter for field: id
         *
         * @param string $value
         * @access protected
         */
        protected function __set_id ($value) {
            $this->_id = $this->__validate_int ($value, 'ID');
        }

        /**
         * Setter for field: include
         *
         * @param string $value
         * @access protected
         */
        protected function __set_include ($value) {
            $this->_include = $this->__validate_include ($value);
        }

        /**
         * Setter for field: limit
         *
         * @param string $value
         * @access protected
         */
        protected function __set_limit ($value) {
            $this->_limit = $this->__validate_limit ($value);
        }

        /**
         * Setter for field: offset
         *
         * @param string $value
         * @access protected
         */
        protected function __set_offset ($value) {
            $this->_offset = $this->__validate_int ($value, 'offset');
        }

        /**
         * Setter for field: image
         *
         * @param string $value
         * @access protected
         */
        protected function __set_image ($value) {
            $this->_image = $this->__validate_file ($value);
        }

        /**
         * Setter for field: since_id
         *
         * @param string $value
         * @access protected
         */
        protected function __set_since_id ($value) {
            $this->_since_id = $this->__validate_int ($value, 'since ID');
        }

        /**
         * Setter for field: user
         *
         * @param string $value
         * @access protected
         */
        protected function __set_user ($value) {
            $this->_user = $value;
        }

        /**
         * Create status
         *
         * Throws InvalidArgumentException when status body is missing
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function create () {
            if (!$this->_body) {
                throw new InvalidArgumentException ('Status body is missing.', -1);
            }
            $opts = array();
            $data = array('status[body]' => $this->_body);
            if ($this->_image) {
                $data['status[picture]'] = '@'.$this->_image;
                $opts['multipart'] = true;
            }
            return array ('/statuses', 'post', $data, $opts);
        }

        /**
         * Read status
         *
         * Meaning of params: {@link http://www.blip.pl/api-0.02.html}
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function read () {
            if ($this->_user) {
                if ($this->_user == '__ALL__') {
                    if ($this->_since_id) {
                        $url = "/statuses/$this->_since_id/all_since";
                    }
                    else {
                        $url = "/statuses/all";
                    }
                }
                else {
                    if ($this->_since_id) {
                        $url = "/users/$this->_user/statuses/$this->_since_id/since";
                    }
                    else {
                        $url = "/users/$this->_user/statuses";
                    }
                }
            }
            else if ($this->_id) {
                $url = "/statuses/$this->_id";
            }

            else {
                $url = '/statuses';
                if ($this->_since_id) {
                    $url .= "/$this->_since_id/since";
                }
            }

            $params = array ();
            if ($this->_limit) {
                $params['limit'] = $this->_limit;
            }
            if ($this->_offset) {
                $params['offset'] = $this->_offset;
            }
            if ($this->_include) {
                $params['include'] = implode (',', $this->_include);
            }

            return array ($url, 'get', $params);
        }

        /**
         * Delete status
         *
         * Throws InvalidArgumentException when status ID is missing
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function delete () {
            if (!$this->_id) {
                throw new InvalidArgumentException ('Status ID is missing.', -1);
            }
            return array ("/statuses/$this->_id", 'delete');
        }
    }
}

