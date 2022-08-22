<?php
    class Debug {
        private static function LogToFile($type, $message = false) {
            if(APP_DEBUG === false || !$message) {
                return;
            } else {
                $date = date("Y-m-d");
                $log_path = "./logs/{$type}.log-{$date}.txt";

                $logline = "[" . date("H:i:s") . "] " . $message . "\n";
                file_put_contents($log_path, $logline, FILE_APPEND);
            }
        }

        public static function Error($message = false) {
            return self::LogToFile("error", $message);
        }

        public static function Access($message = false) {
            return self::LogToFile('access', $message);
        }
    }