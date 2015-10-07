<?php

    namespace AloFramework\Common;

    /**
     * Static common component container
     * @author Art <a.molcanovas@gmail.com>
     * @since  1.1 ifundefined() added
     */
    abstract class Alo {

        /**
         * Includes a file if it exists
         * @author Art <a.molcanovas@gmail.com>
         *
         * @param string $path Path to the file
         *
         * @return bool True if the file is found, false if not
         */
        static function includeIfExists($path) {
            if (self::isIncludable($path)) {
                include $path;

                return true;
            }

            return false;
        }

        /**
         * Checks if the path is includable
         * @author Art <a.molcanovas@gmail.com>
         *
         * @param string $path The path
         *
         * @return bool
         */
        private static function isIncludable($path) {
            return file_exists($path) && is_file($path);
        }

        /**
         * include_once() a file if it exists
         * @author Art <a.molcanovas@gmail.com>
         *
         * @param string $path Path to the file
         *
         * @return bool True if the file is found, false if not
         */
        static function includeOnceIfExists($path) {
            if (self::isIncludable($path)) {
                include_once $path;

                return true;
            }

            return false;
        }

        /**
         * Checks if we're dealing with a CLI request
         * @author Art <a.molcanovas@gmail.com>
         * @return bool
         */
        static function isCliRequest() {
            return PHP_SAPI == 'cli' || defined('STDIN');
        }

        /**
         * Returns $var if it's set, null if it's not
         *
         * @param mixed $var Reference to the variable
         *
         * @return mixed|null $var if it's set, null if it's not
         */
        static function get(&$var) {
            return isset($var) ? $var : null;
        }

        /**
         * Returns $var if it's set and evaluates as true (a non-empty string, non-0 int etc), null otherwise
         * @author Art <a.molcanovas@gmail.com>
         *
         * @param mixed $var Reference to the variable
         *
         * @return mixed|null The var or null
         */
        static function nullget(&$var) {
            return self::get($var) ? $var : null;
        }

        /**
         * Returns $var if it's set $planB if it's not
         * @author Art <a.molcanovas@gmail.com>
         *
         * @param mixed $var        Reference to the main variable
         * @param mixed $planB      What to return if $var isn't available
         * @param bool  $useNullget If set to true, will use self::nullget(), otherwise will use self::get() to
         *                          determinewhether $var is set
         *
         * @return mixed $var if available, $planB if not
         */
        static function ifnull(&$var, $planB, $useNullget = false) {
            $v = $useNullget ? self::nullget($var) : self::get($var);

            return $v !== null ? $v : $planB;
        }

        /**
         * Returns the value of the constant with the name of $const if it's defined, $planB if it's not
         * @author Art <a.molcanovas@gmail.com>
         *
         * @param string $const Constant name
         * @param mixed  $planB What to return if $const isn't defined
         *
         * @return mixed
         * @since  1.1
         */
        static function ifundefined($const, $planB) {
            return defined($const) ? constant($const) : $planB;
        }

        /**
         * Checks if the request was made via AJAX
         * @author Art <a.molcanovas@gmail.com>
         * @return bool
         */
        static function isAjaxRequest() {
            return self::get($_SERVER['HTTP_X_REQUESTED_WITH']) == 'XMLHttpRequest';
        }
    }
