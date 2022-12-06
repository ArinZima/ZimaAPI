<?php
    namespace Zima\Classes;

    use PDO;

    use Zima\Classes\API;
    use Zima\Classes\Encryptor;
    use Zima\Classes\MySQL;
    use Zima\Classes\Status;

    /**
     * TODO: Create key resetting & deleting
     * TODO: Protect key creation(?)
     * 
     * @author Arin Zima <arin@arinzima.com>
     */
    class Key
    {
        /**
         * Create the key access table
         * 
         * @return bool
         */
        public function prep()
        {
            $db = new MySQL();
            $db = $db->connect();

            $query = $db->prepare('CREATE TABLE IF NOT EXISTS `'.KEY_TABLE.'` (`id` BIGINT NOT NULL AUTO_INCREMENT, `USER` VARCHAR(255), `KEY` VARCHAR(255), PRIMARY KEY(id))');
            $query->execute();

            if($query) {
                http_response_code(200);
                echo json_encode([
                    "type" => "success",
                    "code" => "200 OK",
                    "message" => "The table was successfully created."
                ]);
            } else {
                return new Status(500);
            }
        }

        /**
         * Get the Authorization header, if any
         * 
         * @return array|object
         */
        public function header()
        {
            $headers = apache_request_headers();
            $has_auth = isset($headers['Authorization']);

            if(!$has_auth) {
                return new Status(401);
            } else {
                $auth = $headers['Authorization'];

                $parts = explode(':', $auth);
                $user = explode(' ', $parts[0]);
                $user = $user[1];
                $key = $parts[1];

                return array(
                    "user" => $user,
                    "key" => $key
                );
            }
        }

        /**
         * Validate the key
         * 
         * @return true|object
         */
        public function auth()
        {
            $db = new MySQL();
            $db = $db->connect();

            $auth = $this->header();
            $key = $auth['key'];

            $query = $db->prepare("SELECT * FROM `".KEY_TABLE."` WHERE `USER` = :user");
            $query->bindParam(':user', $auth['user']);
            $query->execute();

            if($query) {
                $num = $query->rowCount();

                if($num <= 0) {
                    return new Status(401);
                } else {
                    $res = $query->fetch(PDO::FETCH_ASSOC);
                    $hash = $res['KEY'];

                    $enc = new Encryptor();
                    $valid = $enc->validate($hash);

                    if($valid) {
                        $verified = $enc->verify($key, $hash);

                        if($verified) {
                            return true;
                        } else {
                            return new Status(401);
                        }
                    } else {
                        return new Status(401);
                    }
                }
            } else {
                return new Status(500);
            }
        }

        /**
         * Create a new key
         * 
         * @param string            $username
         * @return string|object
         */
        public function create(string $username)
        {
            $db = new MySQL();
            $db = $db->connect();

            $api = new API();
            $enc = new Encryptor();

            $key = $api->key();
            $hash = $enc->hash($key);

            $user = $db->prepare("SELECT `USER` FROM `".KEY_TABLE."` WHERE `USER` = :user");
            $user->bindParam(':user', $username);
            $user->execute();

            if($user) {
                $num = $user->rowCount();
    
                if($num <= 0) {
                    $insert = $db->prepare("INSERT INTO `".KEY_TABLE."` (`USER`, `KEY`) VALUES (?, ?)")->execute([
                        $username,
                        $hash
                    ]);

                    if($insert) {
                        http_response_code(200);
                        echo json_encode([
                            "type" => "success",
                            "code" => "200 OK",
                            "message" => "A new key was created.",
                            "key" => $key
                        ]);
                    } else {
                        return new Status(500);
                    }
                }
            } else {
                return new Status(500);
            }
        }
    }