<?php
    class KeyController {
        public static function getAuthHeader() {
            Debug::Access("[KeyController::getAuthHeader] Requesting HTTP headers");
            $headers = apache_request_headers();
            $has_auth = isset($headers['Authorization']);

            if(!$has_auth) {
                Debug::Access("[KeyController::getAuthHeader] Authorization header not set");
                return new Status(401);
            } else {
                Debug::Access("[KeyController::getAuthHeader] Authorization header set");
                $auth = $headers['Authorization'];

                Debug::Access("[KeyController::getAuthHeader] Splitting Authorization header");
                $parts = explode(':', $auth);
                $user = explode(' ', $parts[0]);
                $user = $user[1];
                $key = $parts[1];

                Debug::Access("[KeyController::getAuthHeader] Returning array");
                return array(
                    "parts" => $parts,
                    "user" => $user,
                    "key" => $key
                );
            }
        }

        public static function authorize($callback) {
            Debug::Access("[KeyController::authorize] Executing MySQL::connect");
            $db = MySQL::connect();
            
            Debug::Access("[KeyController::authorize] Executing KeyController::getAuthHeader");
            $auth = self::getAuthHeader();
            $key = $auth['key'];

            Debug::Access("[KeyController::authorize] Preparing SQL statement (SELECT)");
            $query = $db->prepare('SELECT * FROM `ACCESS` WHERE `User` = :user');
            Debug::Access("[KeyController::authorize] Binding parameters to SQL statement (1/1)");
            $query->bindParam(':user', $auth['user']);
            Debug::Access("[KeyController::authorize] Executing SQL statement");
            $query->execute();
            Debug::Access("[KeyController::authorize] Getting SQL result row count");
            $num = $query->rowCount();

            if($num <= 0) {
                Debug::Access("[KeyController::authorize] SQL did not return a result");
                return new Status(401);
            } else {
                $res = $query->fetch(PDO::FETCH_ASSOC);

                $hash = $res['Key'];

                Debug::Access("[KeyController::authorize] Getting encryption info");
                $is_valid_encryption = Encryptor::info($hash);

                if($is_valid_encryption['algoName'] !== 'unknown') {
                    Debug::Access("[KeyController::authorize] Verifying encrypted key");
                    $is_valid = Encryptor::verify($key, $hash);

                    if(!$is_valid) {
                        Debug::Access("[KeyController::authorize] Key is not valid");
                        return new Status(401);
                    } else {
                        Debug::Access("[KeyController::authorize] Key is valid, proceeding...");
                        call_user_func($callback);
                    }
                } else {
                    Debug::Access("[KeyController::authorize] Encryption is invalid");
                    return new Status(401);
                }
            }
        }

        public static function key_create($username) {
            Debug::Access("[KeyController::key_create] Executing MySQL::connect");
            $db = MySQL::connect();

            Debug::Access("[KeyController::key_create] Executing Base::gen_key");
            $key = Base::gen_key();
            Debug::Access("[KeyController::key_create] Executing Encryptor::hashB");
            $hash = Encryptor::hashB($key);

            Debug::Access("[KeyController::key_create] Preparing SQL statement (SELECT)");
            $user = $db->prepare('SELECT `User` FROM `ACCESS` WHERE `User` = :user');
            Debug::Access("[KeyController::key_create] Binding parameters to SQL statement (1/1)");
            $user->bindParam(':user', $username);
            Debug::Access("[KeyController::key_create] Executing SQL statement");
            $user->execute();
            Debug::Access("[KeyController::key_create] Getting SQL result row count");
            $num = $user->rowCount();
            
            if($num <= 0) {
                Debug::Access("[KeyController::key_create] No results found");
                Debug::Access("[KeyController::key_create] Preparing SQL statement (INSERT) and executing it");
                $insert = $db->prepare('INSERT INTO `ACCESS` (`User`, `Key`) VALUES (?, ?)')->execute([
                    $username,
                    $hash
                ]);
                
                if($insert) {
                    Debug::Access("[KeyController::key_create] SQL statement processed successfully");
                    http_response_code(200);
                    echo json_encode([
                        "type"=>"success",
                        "code"=>"200 OK",
                        "message"=>"A new key was created.",
                        "key"=>"{$key}"
                    ]);
                } else {
                    Debug::Access("[KeyController::key_create] SQL statement could not be processed");
                    return new Status(500);
                }
            } else{
                return new Status(403);
            }
        }

        public static function key_reset($username) {
            Debug::Access("[KeyController::key_reset] Executing MySQL::connect");
            $db = MySQL::connect();
            Debug::Access("[KeyController::key_reset] Executing KeyController::getAuthHeader");
            $auth = self::getAuthHeader();

            if($auth['user'] != $username) {
                Debug::Access("[KeyController::key_reset] User not authorized to reset key");
                return new Status(403);
            } else {
                Debug::Access("[KeyController::key_reset] Executing Base::gen_key");
                $key = Base::gen_key();
                Debug::Access("[KeyController::key_reset] Executing Encryptor::hashB");
                $hash = Encryptor::hashB($key);
    
                Debug::Access("[KeyController::key_reset] Preparing SQL statement (SELECT)");
                $user = $db->prepare('SELECT `User` FROM `ACCESS` WHERE `User` = :user');
                Debug::Access("[KeyController::key_reset] Binding parameters to SQL statement (1/1)");
                $user->bindParam(':user', $username);
                Debug::Access("[KeyController::key_reset] Executing SQL statement");
                $user->execute();
                Debug::Access("[KeyController::key_reset] Getting SQL result row count");
                $num = $user->rowCount();
                
                if($num <= 0) {
                    Debug::Access("[KeyController::key_reset] No result found");
                    return new Status(404);
                } else {
                    Debug::Access("[KeyController::key_reset] Preparing SQL statement (UPDATE) and executing it");
                    $insert = $db->prepare('UPDATE `ACCESS` SET `Key` = ? WHERE `User` = ?')->execute([
                        $hash,
                        $username
                    ]);
                    
                    if($insert) {
                        Debug::Access("[KeyController::key_reset] SQL statement successful");
                        http_response_code(200);
                        echo json_encode([
                            "type"=>"success",
                            "code"=>"200 OK",
                            "message"=>"A key was updated.",
                            "key"=>"{$key}"
                        ]);
                    } else {
                        Debug::Access("[KeyController::key_reset] SQL statement unsuccessful");
                        return new Status(500);
                    }
                }
            }
        }

        public static function key_delete($username) {
            Debug::Access("[KeyController::key_delete] Executing MySQL::connect");
            $db = MySQL::connect();
            Debug::Access("[KeyController::key_delete] Executing KeyController::getAuthHeader");
            $auth = self::getAuthHeader();

            if($auth['user'] != $username) {
                Debug::Access("[KeyController::key_delete] User not authorized");
                return new Status(401);
            } else {
                Debug::Access("[KeyController::key_delete] Preparing MySQL statement (DELETE) and executing it");
                $query = $db->prepare('DELETE FROM `ACCESS` WHERE `User` = ?')->execute([
                    $username
                ]);
                
                if($query) {
                    Debug::Access("[KeyController::key_delete] SQL statement successful");
                    return new Status(200);
                } else {
                    Debug::Access("[KeyController::key_delete] SQL statement unsuccessful");
                    return new Status(500);
                }
            }
        }
    }