<?php
    namespace Zima\Classes;

    /**
     * @author Arin Zima <arin@arinzima.com>
     */
    class Status
    {
        public function __construct($code)
        {
            switch ($code) {
                case 200:
                    return $this->two_hundred();
                case 401:
                    return $this->four_o_one();
                case 403:
                    return $this->four_o_three();
                case 404:
                    return $this->four_o_four();
                case 405:
                    return $this->four_o_five();
                case 410:
                    return $this->four_ten();
                case 423:
                    return $this->four_twenty_three();
                case 500:
                    return $this->five_hundred();
                default:
                    return $this->NoStatusError();
            }
        }

        private function two_hundred()
        {
            http_response_code(200);
            echo json_encode([
                "type" => "success",
                "code" => "200 OK",
                "message" => "All changes were carefully recorded."
            ]);
        }

        private function four_o_one()
        {
            http_response_code(401);
            echo json_encode([
                "type" => "danger",
                "code" => "401 Unauthorized",
                "message" => "Please authenticate to use this resource."
            ]);
        }

        private function four_o_three()
        {
            http_response_code(403);
            echo json_encode([
                "type" => "danger",
                "code" => "403 Forbidden",
                "message" => "Forbidden action."
            ]);
        }

        private function four_o_four()
        {
            http_response_code(404);
            echo json_encode([
                "type" => "warning",
                "code" => "404 Not Found",
                "message" => "No data could be found."
            ]);
        }

        private function four_o_five()
        {
            http_response_code(405);
            echo json_encode([
                "type" => "danger",
                "code" => "405 Method Not Allowed",
                "message" => "This request method is not allowed on this resource."
            ]);
        }

        private function four_ten()
        {
            http_response_code(410);
            echo json_encode([
                "type" => "danger",
                "code" => "410 Gone",
                "message" => "This resource is no longer available."
            ]);
        }

        private function four_twenty_three()
        {
            http_response_code(423);
            echo json_encode([
                "type" => "danger",
                "code" => "423 Locked",
                "message" => "The requested resource is locked."
            ]);
        }

        private function five_hundred()
        {
            http_response_code(500);
            echo json_encode([
                "type" => "danger",
                "code" => "500 Internal Server Error",
                "message" => "Something went wrong with our servers whilst processing your request."
            ]);
        }

        private function NoStatusError()
        {
            http_response_code(501);
            echo json_encode([
                "type" => "danger",
                "code" => "501 Not Implemented",
                "message" => "The developer did not provide a status to be returned."
            ]);
        }
    }