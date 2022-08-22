<?php
    class Encryptor extends Base {
        public static function algos(): array {
            return password_algos();
        }

        public static function info($hash): array {
            return password_get_info($hash);
        }

        public static function hash($password): string {
            return password_hash($password, PASSWORD_DEFAULT, [ 'cost' => 11 ]);
        }

        public static function hashB($password): string {
            return password_hash($password, PASSWORD_BCRYPT);
        }

        public static function verify($password, $hash): bool {
            return password_verify($password, $hash);
        }
    }