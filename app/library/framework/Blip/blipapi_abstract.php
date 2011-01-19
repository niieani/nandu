<?php

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_abstract.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

/**
 * Blip! (http://blip.pl) communication library.
 *
 * @author Marcin Sztolcman <marcin /at/ urzenia /dot/ net>
 * @version 0.02.32
 * @version $Id: blipapi_abstract.php 240 2010-12-26 15:50:43Z urzenia $
 * @copyright Copyright (c) 2007, Marcin Sztolcman
 * @license http://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0 (LGPLv3)
 * @package blipapi
 */

if (!class_exists ('BlipApi_Abstract')) {

    abstract class BlipApi_Abstract {

        /**
         * Automatic constructor - if first argument is an array, assign all keys from it as object properties.
         *
         * @param array $args
         * @access public
         */
        public function __construct ($args=null) {
            if (!$args || !is_array ($args)) {
                return;
            }

            foreach ($args as $k=>$v) {
                $this->$k = $v;
            }
        }

        /**
         * Setter for some options
         *
         * For specified keys, call proper __set_* method. Throws InvalidArgumentException exception when incorrect key was
         * specified.
         *
         * @param string $key name of property to set
         * @param mixed $value value of property
         * @access public
         */
        public function __set ($key, $value) {
            if (!method_exists ($this, '__set_'.$key)) {
                throw new InvalidArgumentException (sprintf ('Unknown param: "%s".', $key), -1);
            }

            return call_user_func (array ($this, '__set_'.$key), $value);
        }

        /**
         * Getter for some options
         *
         * For specified keys, return them. Throws InvalidArgumentException exception when incorrect key was specified.
         *
         * @param string $key name of property to return
         * @return mixed
         * @access public
         */
        public function __get ($key) {
            if (method_exists ($this, '__get_'.$key)) {
                return call_user_func (array ($this, '__get_'.$key));
            }

            else if (!method_exists ($this, '__set_'.$key)) {
                throw new InvalidArgumentException ("Unknown param: \"$key\".", -1);
            }

            $key = '_'.$key;
            return $this->$key;
        }

        /**
         * Validator for field of type: file
         *
         * @param string $path
         * @param bool $allow_empty
         * @access protected
         */
        protected function __validate_file ($path=null, $allow_empty=false) {
            if (!$path) {
                if ($allow_empty) {
                    throw new InvalidArgumentException ('File path is missing.');
                }

                return '';
            }

            if ($path[0] == '@') {
                $path = substr ($path, 1);
            }

            if (!is_file ($path)) {
                throw new InvalidArgumentException ("File $path not found.");
            }

            return $path;
        }

        /**
         * Validator for field of type: limit
         *
         * @param int $limit
         * @access protected
         */
        protected function __validate_limit ($limit) {
            if (!is_int ($limit) || $limit < 0) {
                throw new InvalidArgumentException ("Incorrect value of limit.");
            }
            else if ($limit == 0) {
                return 50;
            }
            else {
                return $limit;
            }
        }

        /**
         * Validator for field of type: int (offset, id, etc)
         *
         * @param int $int
         * @param string $field name
         * @access protected
         */
        protected function __validate_int ($int, $field) {
            if (!is_int ($int) || $int < 0) {
                throw new InvalidArgumentException ("Incorrect value of $field.");
            }
            else {
                return $int;
            }
        }

        /**
         * Validator for field of type: include
         *
         * @param misc $include if array, return the same, if any other, return as array with this item as one value
         * @access protected
         */
        protected function __validate_include ($include) {
            if (!$include) {
                return;
            }
            else if (gettype ($include) != 'array') {
                return array ($include);
            }
            else {
                return $include;
            }
        }

    }
}

