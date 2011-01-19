<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_avatar.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_avatar.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_Avatar')) {
    class BlipApi_Avatar extends BlipApi_Abstract implements IBlipApi_Command {
        /**
         * User name
         *
         * @access protected
         * @var string
         */
        protected $_user    = '';

        /**
         * Path to image
         *
         * @access protected
         * @var string
         */
        protected $_image   = '';

        /**
         * If true, return only url to avatar of specified size.
         *
         * @accesc protected
         * @var bool
         */
        protected $_url_only    = false;

        /**
         * Size of avatar to return.
         * Available values:
         *  * femto - 15x15 px
         *  * nano - 30x30 px
         *  * pico - 50x50 px
         *  * standard - 90x90 px
         *  * large - 120x120 px
         *
         * Ignored when url_only is set to false.
         *
         * @access protected
         * @var string
         */
        protected $_size        = 'standard';

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
         * Setter for field: image
         *
         * @param string $value
         * @access protected
         */
        protected function __set_image ($value) {
            $this->_image = $this->__validate_file ($value);
        }

        /**
         * Setter for field: url_only
         *
         * @param bool $value
         * @access protected
         */
        protected function __set_url_only ($value) {
            $this->_url_only = $value ? true : false;
        }

        /**
         * Setter for field: size
         *
         * @param bool $value
         * @access protected
         */
        protected function __set_size ($value) {
            if (!in_array ($value, array ('femto', 'nano', 'pico', 'standard', 'large'))) {
                throw new InvalidArgumentException ('Unrecognized size of avatar.');
            }

            $this->_size = $value;
        }

        /**
         * Get info about users avatar
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function read () {
            if (!$this->_user) {
                return array ('/avatar', 'get');
            }
            else if ($this->_url_only) {
                return array (null, null, null, array ('just_return' => "http://blip.pl/users/$this->_user/avatar/$this->_size.jpg"));
            }

            return array ("/users/$this->_user/avatar", 'get');
        }

        /**
         * Upload new avatar
         *
         * Throws InvalidArgumentException if avatar path is missing or file not found
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function update () {
            if (!$this->_image) {
                throw new InvalidArgumentException ('Avatar path missing or file not found.', -1);
            }
            return array ('/avatar', 'post', array ( 'avatar[file]' => '@'.$this->_image ), array ('multipart' => 1));
        }

        /**
         * Delete avatar
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function delete () {
            return array ('/avatar', 'delete');
        }
    }
}

