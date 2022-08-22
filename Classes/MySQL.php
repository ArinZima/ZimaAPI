<?php
    class MySQL extends Base {
        public static function connect() {
            $dbh = null;
            Debug::Access("[MySQL::connect] Attempting connection");
            try {
                $dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER,DB_PASS);
            } catch (PDOException $e) {
                $message = $e->getMessage();
                Debug::Access("[MySQL::connect] Connection failed to establish. Refer to error log for more info");
                
                Debug::Error($message);
                die($message);
            }
            return $dbh;
        }
    }