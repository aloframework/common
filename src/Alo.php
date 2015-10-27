<?php

    namespace AloFramework\Common;

    use Traversable;

    /**
     * Static common component container
     * @author Art <a.molcanovas@gmail.com>
     * @since  1.2 getFingerprint(), isTraversable(), unXss() added<br/>
     *         1.1 ifundefined() added
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

        /**
         * Returns a hashed browser fingerprint
         *
         * @author Art <a.molcanovas@gmail.com>
         *
         * @param string $hashAlgo Hash algorithm to use
         *
         * @return string
         * @since  1.2
         */
        static function getFingerprint($hashAlgo = 'sha256') {
            return hash($hashAlgo,
                        '#QramRAN7*s%6n%@x*53jVVPsnrz@5MY$49o^mhJ8HqY%3a09yJnSWg9lBl$O4CKUb&&S%EgYBjhUZEbhquw$keCjR6I%zMcA!Qr' .
                        self::get($_SERVER['HTTP_USER_AGENT']) .
                        'OE2%fWaZh4jfZPiNXKmHfUw6ok6Z0s#PInaFa8&o#xh#nVyaFaXHPUcv^2y579PnYr5AOs6Zqb!QTAZCgRR968*%QxKc^XNuYYM8' .
                        self::get($_SERVER['HTTP_DNT']) .
                        '%CwyJJ^GAooDl&o0mc%7zbWlD^6tWoNSN&m3cKxWLP8kiBqO!j2PM5wACzyOoa^t7AEJ#FlDT!BMtD$luy%2iZejMVzktaiftpg*' .
                        self::get($_SERVER['HTTP_ACCEPT_LANGUAGE']) .
                        'tep!uTwVXk1CedJq0osEI7p&XCxnC3ipGDWEpTXULEg8J!K1NJSxe4GPor$R3OOb**ZjzPN$$SOHe4ZDcQWQULdtT&XxP2!YYxZy');
        }

        /**
         * Checks if the variable is usable in a foreach loop
         * @author Art <a.molcanovas@gmail.com>
         *
         * @param mixed $input The variable
         *
         * @return bool
         * @since  1.2
         */
        static function isTraversable($input) {
            return is_array($input) || $input instanceof Traversable;
        }

        /**
         * Protects the input from cross-site scripting attacks
         * @author Art <a.molcanovas@gmail.com>
         *
         * @param string|array|Traversable $input The scalar input, or an array/Traversable
         *
         * @return string|array|Traversable The escaped string. If an array or traversable was passed on, the input
         * withall its applicable values escaped.
         * @since  1.2
         */
        static function unXss($input) {
            if (self::isTraversable($input)) {
                foreach ($input as &$i) {
                    $i = self::unXss($i);
                }
            } elseif (is_scalar($input)) {
                $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5);
            }

            return $input;
        }
    }
