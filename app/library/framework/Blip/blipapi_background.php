<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_background.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_background.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_Background')) {
    class BlipApi_Background extends BlipApi_Abstract implements IBlipApi_Command {
        /**
         * Path to image
         *
         * @access protected
         * @var string
         */
        protected $_image   = '';

        /**
         * User name
         *
         * @access protected
         * @var string
         */
        protected $_user    = '';

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
         * Setter for field: user
         *
         * @param string $value
         * @access protected
         */
        protected function __set_user ($value) {
            $this->_user = $value;
        }

        /**
         * Get info about users background
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function read () {
            if (!$this->_user) {
                return array ("/background", 'get');
            }
            return array ("/users/$this->_user/background", 'get');
        }

        /**
         * Upload new background
         *
         * Throws InvalidArgumentException if background path is missing, or file not found
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function update () {
            if (!$this->_image) {
                throw new InvalidArgumentException ('Background path is missing or file not found.', -1);
            }
            return array ('/background', 'post', array ('background[file]' => '@'.$this->_image), array ('multipart' => 1));
        }

        /**
         * Delete background
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function delete () {
            return array ('/background', 'delete');
        }
    }
}

