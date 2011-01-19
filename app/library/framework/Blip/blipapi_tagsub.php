<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_tagsub.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_tagsub.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_Tagsub')) {

    class BlipApi_Tagsub extends BlipApi_Abstract implements IBlipApi_Command {
        protected $_pl_chars = "\xc4\x84\xc4\x85\xc4\x86\xc4\x87\xc4\x98\xc4\x99\xc5\x81\xc5\x82\xc5\x83\xc5\x84\xc3\x93\xc3\xb3\xc5\x9a\xc5\x9b\xc5\xbb\xc5\xbc\xc5\xb9\xc5\xba";

        /**
         * Include some additional data in respond to read method.
         * More info: http://www.blip.pl/api-0.02.html#parametry
         *
         * @access protected
         * @var string|array
         */
        protected $_include;

        /**
         * Name of tag
         *
         * @access protected
         * @var string
         */
        protected $_name;

        /**
         * Type of tag subscription: all, ignored, subscribe
         *
         * @access protected
         * @var string
         */
        protected $_type = 'subscribe';

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
         * Setter for field: name
         *
         * @param string $value
         * @access protected
         */
        protected function __set_name ($value) {
            if (!$value || strlen ($value) == 0 || !preg_match ('/^[-a-zA-Z0-9_'.$this->_pl_chars.']+$/', $value)) {
                throw new InvalidArgumentException ('Incorrect value of tag name.');
            }
            $this->_name = $value;
        }

        /**
         * Setter for field: type
         *
         * @param string $value - one of: all (not for create!), ignore, subscribe (default)
         * @access protected
         */
        protected function __set_type ($value) {
            if (!in_array ($value, array ('all', 'ignore', 'subscribe'))) {
                throw new InvalidArgumentException ('Incorrect value of type.');
            }
            $this->_type = $value;
        }

        /**
         * Create relation between user and tag
         *
         * Throws InvalidArgumentException when tag name is missing, or type property is 'all'
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function create () {
            if (!$this->_name) {
                throw new InvalidArgumentException ('Tag name is missing.', -1);
            }
            if ($this->_type == 'all') {
                throw new InvalidArgumentException ('For creating, "all" type is incorrect. Should be one of: "ignore", "subscribe".', -1);
            }

            return array ("/tag_subscriptions/". $this->_type ."/$this->_name", 'put');
        }

        /**
         * Read user to tags relationships
         *
         * Meaning of params: {@link http://www.blip.pl/api-0.02.html}
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function read () {
            $url = '/tag_subscriptions';
            if ($this->_type != 'all') {
                $url .= '/'. $this->_type .'d';
            }

            $params = array ();
            if ($this->_include) {
                $params['include'] = implode (',', $this->_include);
            }

            return array ($url, 'get', $params);
        }

        /**
         * Delete relationship between user and tag
         *
         * Throws InvalidArgumentException when tag name is missing
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function delete () {
            if (!$this->_name) {
                throw new InvalidArgumentException ('Tag name is missing.', -1);
            }
            return array ("/tag_subscriptions/tracked/$this->_name", 'put');
        }
    }
}

