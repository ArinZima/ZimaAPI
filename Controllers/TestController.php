<?php
    class TestController {
        public static function do_test() {
            http_response_code(200);
            echo json_encode([
                "type" => "success",
                "code" => "200 OK",
                "message" => "If you're getting this response, congratulations! You've successfully set up @ZimaWork/api and can begin building your API!"
            ]);
        }
    }