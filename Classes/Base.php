<?php
    class Base {
        public static function gen_key($length = 12): string {
            Debug::Access("[Base::gen_key] Generating key of length {$length}");
            $key = bin2hex(openssl_random_pseudo_bytes($length));
            
            Debug::Access("[Base::gen_key] Returning key");
            return $key;
        }

        public static function sanitize_string(string $var): string {
            Debug::Access("[Base::sanitize_string] Stripping slashes");
            $var = stripslashes($var);
            Debug::Access("[Base::sanitize_string] Stripping tags");
            $var = strip_tags($var);
            Debug::Access("[Base::sanitize_string] Converting into HTML encodable entities (1/2)");
            $var = htmlentities($var);
            Debug::Access("[Base::sanitize_string] Converting into HTML encodable entities (2/2)");
            $var = htmlspecialchars($var);

            Debug::Access("[Base::sanitize_string] Returning sanitized string");
            return $var;
        }

        public static function get_slug(): array {
            Debug::Access("[Base::get_slug] Parsing URL");
            $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            Debug::Access("[Base::get_slug] Trimming URL path");
            $slugs = trim(parse_url($site_url, PHP_URL_PATH), '/');
            Debug::Access("[Base::get_slug] Converting trimmed URL to array");
            $slugs = explode('/', $slugs);

            Debug::Access("[Base::get_slug] Returning array");
            return $slugs;
        }
    }