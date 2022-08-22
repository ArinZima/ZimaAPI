<?php
/*
|--------------------------------------------------
| loader.php
|--------------------------------------------------
|
| This file is responsible for loading the framework
| on demand when classes aren't registered yet.
|
*/

// Headers to be sent
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: application/json');

// Start a session first
session_start();

// Define the root directory from where classes should be loaded.
// The path will be (as declared below)
// /absolute/path/here/from/where/this/file/is/config/database.php
define("APP_ROOTPATH", __DIR__);

// Require config.php
require APP_ROOTPATH.'/config.php';

// Start output buffering so we don't echo immediately
ob_start();

// Register all classes for the application
$classmap = [
    'Base' => 'Classes/Base.php',
    'Debug' => 'Classes/Debug.php',
    'MySQL' => 'Classes/MySQL.php',
    'Encryptor' => 'Classes/Encryptor.php',
    'Status' => 'Classes/Status.php',
    'KeyController' => 'Classes/KeyController.php',

    'TestController' => 'Controllers/TestController.php'
];

class ClassNotFoundException extends Exception {};

set_error_handler(function(int $errorNumber, string $error, string $errorFile, int $errorLine) {
    if(APP_DEBUG === true) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'type' => 'danger',
            'message' => 'The application could not process your request.',
            'extra' => [
                'data' => [
                    'errorNumber' => $errorNumber,
                    'errorMessage' => $error,
                    'errorFile' => $errorFile,
                    'errorLine' => $errorLine
                ]
            ]
        ]);
        exit();
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'type' => 'danger',
            'message' => 'The application could not process your request.'
        ]);
        exit();
    }
});

set_exception_handler(function($e) {
    if(APP_DEBUG === true && is_object($e) && get_class($e) === ClassNotFoundException::class) {
        $data = ob_get_clean();

        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'type' => 'danger',
            'message' => 'The application could not process your request.',
            'extra' => [
                'data' => $data
            ]
        ]);
        exit();
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'type' => 'danger',
            'message' => 'The application could not process your request.'
        ]);
        exit();
    }
});

register_shutdown_function(function() {
    // Nothing here...
});

/**
 * @param string $class The class PHP tries to load
 */
spl_autoload_register(function($class) use($classmap) {
    if(array_key_exists($class, $classmap)) {
        $classfile = sprintf('%s/%s', APP_ROOTPATH, $classmap[$class]);

        if(is_readable($classfile)) {
            require_once $classfile;
            return;
        }
    }

    throw new ClassNotFoundException(sprintf('The class %s in question could not be found.', $class));
});