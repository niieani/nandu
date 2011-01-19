<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_movie.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_movie.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_Movie')) {
    class BlipApi_Movie extends BlipApi_Abstract implements IBlipApi_Command {
        /**
         * ID of item to read
         *
         * @access protected
         * @var int
         */
        protected $_id;

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
         * Read movie attached to status/message/update
         *
         * Throws InvalidArgumentException when status ID is missing
         *
         * @access public
         * @return array parameters for BlipApi::__call
         */
        public function read () {
            if (!$this->_id) {
                throw new InvalidArgumentException ('Update ID is missing.', -1);
            }
            return array ("/updates/$this->_id/movie", 'get');
        }
    }
}

