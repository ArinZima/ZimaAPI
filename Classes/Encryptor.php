<?php
    namespace Zima\Classes;

    /**
     * @author Arin Zima <arin@arinzima.com>
     */
    class Encryptor {
        /**
         * Validate a hash to BCrypt
         * 
         * @param string            $hash
         * @return bool
         */
        public function validate(string $hash)
        {
            $info = password_get_info($hash);

            if($info['algoName'] !== 'bcrypt') {
                return false;
            } else {
                return true;
            }
        }

        /**
         * Encrypt a given string
         * 
         * @param string            $password
         * @return string
         */
        public function hash(string $password)
        {
            return password_hash($password, PASSWORD_BCRYPT, [ 'cost' => PASSWORD_BCRYPT_DEFAULT_COST ]);
        }

        /**
         * Verify a hash with a password
         * 
         * @param string            $password
         * @param string            $hash
         * @return bool
         */
        public function verify(string $password, string $hash) {
            return password_verify($password, $hash);
        }
    }