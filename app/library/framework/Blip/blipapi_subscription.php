<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_subscription.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_subscription.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_Subscription')) {
    class BlipApi_Subscription extends BlipApi_Abstract implements IBlipApi_Command {
        /**
         * Specify whis subscription is read: to user, from user or both.
         * Accepted values:
         *  * to
         *  * from
         *  * both (default)
         *
         * @access protected
         * @var string
         */
        protected $_direction   = 'both';

        /**
         * Affect Instant Messenger subscription
         *
         * @access protected
         * @var bool
         */
        protected $_im;

        /**
         * Include some additional data in respond to read method.
         * More info: http://www.blip.pl/api-0.02.html#parametry
         *
         * @access protected
         * @var string|array
         */
        protected $_include;

        /**
         * User name
         *
         * @access protected
         * @var string
         */
        protected $_user;

        /**
         * Affect WWW subscription
         *
         * @access protected
         * @var bool
         */
        protected $_www;

        /**
         * Setter for field: direction
         *
         * @param string $value
         * @access protected
         */
        protected function __set_direction ($value) {
            if (!in_array ($value, array ('from', 'to', 'both'))) {
                throw new InvalidArgumentException ("Incorrect direction.");
            }

            $this->_direction = $value;
        }

        /**
         * Setter for field: im
         *
         * @param string $value
         * @access protected
         */
        protected function __set_im ($value) {
            $this->_im = $value;
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
         * Setter for field: user
         *
         * @param string $value
         * @access protected
         */
        protected function __set_user ($value) {
            $this->_user = $value;
        }

        /**
         * Setter for field: www
         *
         * @param string $value
         * @access protected
         */
        protected function __set_www ($value) {
            $this->_www = $value;
        }

        /**
         * Return user current subscriptions
         *
         * Throws InvalidArgumentException when incorrect $direction is specified.
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function read () {
            if ($this->_direction == 'both') {
                $this->_direction = '';
            }

            $url = '/subscriptions/' . $this->_direction;
            if ($this->_user) {
                $url = "/users/$this->_user$url";
            }

            $params = array ();
            if ($this->_include) {
                $params['include'] = implode (',', $this->_include);
            }

            return array ($url, 'get', $params);
        }

        /**
         * Create or delete subscription of given user to current signed
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function update () {
            $url = '/subscriptions';
            if ($this->_user) {
                $url .= "/$this->_user";
            }

            $data = array (
                'subscription[www]' => $this->_www ? 1 : 0,
                'subscription[im]'  => $this->_im  ? 1 : 0,
            );
            return array ($url, 'put', $data);
        }

        /**
         * Delete subscription
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function delete () {
            if (!$this->_user) {
                throw new InvalidArgumentException ("Missing user");
            }

            return array ("/subscriptions/$this->_user", 'delete');
        }
    }
}

