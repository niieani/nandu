<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_bliposphere.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_bliposphere.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_Bliposphere')) {
    class BlipApi_Bliposphere extends BlipApi_Abstract implements IBlipApi_Command {
        /**
         * Limit read results to $_limit items
         *
         * @access protected
         * @var int
         */
        protected $_limit   = 10;

        /**
         * Include some additional data in respond to read method.
         * More info: http://www.blip.pl/api-0.02.html#parametry
         *
         * @access protected
         * @var string|array
         */
        protected $_include = null;

        /**
         * ID of item where data is being set.
         *
         * @access protected
         * @var int
         */
        protected $_since_id;

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
         * Setter for field: include
         *
         * @param string $value
         * @access protected
         */
        protected function __set_include ($value) {
            $this->_include = $this->__validate_include ($value);
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
         * Return current bliposhpere
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function read () {
            $url = '/bliposphere';
            if ($this->_since_id) {
                $url .= "/since/$this->_since_id";
            }

            $params = array ();
            if ($this->_limit) {
                $params['limit'] = $this->_limit;
            }
            if ($this->_include) {
                $params['include'] = implode (',', $this->_include);
            }

            return array ($url, 'get', $params);
        }
    }
}

