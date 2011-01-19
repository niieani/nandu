<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_shortlink.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_shortlink.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_Shortlink')) {
    class BlipApi_Shortlink extends BlipApi_Abstract implements IBlipApi_Command {
        /**
         * Code from rdir.pl to decode
         *
         * @access protected
         * @var string
         */
        protected $_code;

        /**
         * Limit read results to $_limit items
         *
         * @access protected
         * @var int
         */
        protected $_limit       = 10;

        /**
         * URL to encode
         *
         * @access protected
         * @var string
         */
        protected $_link;

        /**
         * Offset for read result set
         *
         * @access protected
         * @var int
         */
        protected $_offset      = 0;

        /**
         * ID of item where data is being set.
         *
         * @access protected
         * @var int
         */
        protected $_since_id;

        /**
         * Setter for field: code
         *
         * @param string $value
         * @access protected
         */
        protected function __set_code ($value) {
            $this->_code = $value;
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
         * Setter for field: link
         *
         * @param string $value
         * @access protected
         */
        protected function __set_link ($value) {
            $this->_link = $value;
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
         * Setter for field: since_id
         *
         * @param string $value
         * @access protected
         */
        protected function __set_since_id ($value) {
            $this->_since_id = $this->__validate_int ($value, 'since ID');
        }

        /**
         * Create shortlink
         *
         * Throws InvalidArgumentException if url is missing.
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function create () {
            if (!$this->_link) {
                throw new InvalidArgumentException ("Url is missing.");
            }

            $url = '/shortlinks';
            $data = array ();
            $data['shortlink[original_link]'] = $this->_link;

            return array ($url, 'post', $data);
        }

        /**
         * Get shortlinks from Blip!'s rdir system
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function read () {
            if ($this->_code) {
                $url = "/shortlinks/$this->_code";
            }
            else if ($this->_since_id) {
                $url = "/shortlinks/$this->_since_id/all_since";
            }
            else {
                $url = '/shortlinks/all';
            }

            $params = array ();
            if ($this->_limit) {
                $params['limit'] = $this->_limit;
            }
            if ($this->_offset) {
                $params['offset'] = $this->_offset;
            }

            return array ($url, 'get', $params);
        }
    }
}

