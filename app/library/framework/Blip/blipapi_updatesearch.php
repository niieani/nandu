<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_update.php 195 2010-04-20 18:42:02Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_update.php 195 2010-04-20 18:42:02Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_UpdateSearch')) {
    class BlipApi_UpdateSearch extends BlipApi_Abstract implements IBlipApi_Command {
        /**
         * Search query
         *
         * @access protected
         * @var string
         */
        protected $_query;

        /**
         * Type of searched messages
         *
         * @access protected
         * @var string
         */
        protected $_type;

        /**
         *
         *
         * @access protected
         * @var string
         */
        protected $_recipient;

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
         * Setter for field: query
         *
         * @param string $value
         * @access protected
         */
        protected function __set_query ($value) {
            $this->_query = $value;
        }

        /**
         * Setter for field: type
         *
         * @param string $value
         * @access protected
         */
        protected function __set_type ($value) {
            if (!in_array ($value, array (''))) {
                throw new InvalidArgumentException ('Unknown message type');
            }

            $this->_type = $value;
        }

        /**
         * Setter for field: recipient
         *
         * @param string $value
         * @access protected
         */
        protected function __set_recipient ($value) {
            $this->_recipient = $value;
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
         * @param array $value
         * @access protected
         */
        protected function __set_user ($value) {
            $this->_user = $value;
        }


        /**
         * Reading update
         *
         * It's hard to explain what are doing specified parameters. Please consult with offcial API
         * documentation: {@link http://www.blip.pl/api-0.02.html}.
         *
         * Differences with official API: if you want messages from all users, specify $user == __ALL__.
         *
         * @static
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function read () {
            $url = '/updates/search';

            if (!$this->_query) {
                throw new InvalidArgumentException ('Query body missing', -1);
            }

            $params             = array ();
            $params['query']    = $this->_query;

            if ($this->_type) {
                $params['type'] = $this->_type;
            }
            if ($this->_user) {
                $params['user'] = implode (',', $this->_user);
            }
            if ($this->_recipient) {
                $params['recipient'] = implode (',', $this->_recipient);
            }
            if ($this->_since_id) {
                $params['since_id'] = $this->_since_id;
            }

            return array ($url, 'get', $params);
        }
    }
}

