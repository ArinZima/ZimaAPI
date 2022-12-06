<?php
    namespace Zima\Classes;

    /**
     * @author Arin Zima <arin@arinzima.com>
     */
    class API
    {
        /**
         * Generate an API key
         * 
         * @param int           $length
         * @return string
         * 
         * !! THIS FUNCTION USES OPENSSL. IF OPENSSL IS UNAVAILABLE, THIS FUNCTION WILL NOT WORK. 
         * 
         * TODO: Add handling for non-OpenSSL usage.
         */
        public function key(int $length = 16)
        {
            if(extension_loaded("openssl")) {
                return bin2hex(openssl_random_pseudo_bytes($length));
            }
        }

        /**
         * Get requesting IP
         * 
         * @return string
         */
        public function ip()
        {
            if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
                return $_SERVER['HTTP_CLIENT_IP'];
            } else if (!empty('HTTP_X_FORWARDED_FOR')) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                return $_SERVER['REMOTE_ADDR'];
            }
        }

        /**
         * Sanitize string for data storage
         * 
         * @param string        $var
         * @return string
         */
        public function sanitize(string $var)
        {
            $var = stripslashes($var);
            $var = strip_tags($var);
            $var = htmlentities($var);
            $var = htmlspecialchars($var);

            return $var;
        }

        /**
         * Grab a slug from the URL
         * 
         * @param int           $key
         * @return string
         */
        public function slug(int $key)
        {
            $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            $slugs = trim(parse_url($site_url, PHP_URL_PATH), '/');
            $slugs = explode('/', $slugs);

            $has_slug = isset($slugs[$key]);

            if(!$has_slug) {
                $slugs = null;
            } else {
                $slugs = $slugs[$key];
            }

            return $slugs;
        }
    }