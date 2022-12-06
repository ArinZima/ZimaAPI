<?php
    namespace Zima\Controllers;

    class TestController {
        /**
         * Provide a test to see if ZimaAPI was successfully installed. If no PHP errors found, ZimaAPI was successfully added.
         * 
         * @return string
         */
        public function do_test() {
            http_response_code(200);
            echo json_encode([
                "type" => "success",
                "code" => "200 OK",
                "message" => "If you're getting this response, congratulations! You've successfully set up @ZimaWork/api and can begin building your API!"
            ]);
        }
    }