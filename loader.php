<?php
    /**
     * Headers
     */
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Content-Type: application/json');

    /**
     * Starting session
     */
    session_start();

    define("APP_ROOTPATH", __DIR__);

    /**
     * Config
     */
    require APP_ROOTPATH.'/config/config.php';

    /**
     * Classes
     */
    require APP_ROOTPATH.'/Classes/API.php';
    require APP_ROOTPATH.'/Classes/Encryptor.php';
    require APP_ROOTPATH.'/Classes/Key.php';
    require APP_ROOTPATH.'/Classes/MySQL.php';
    require APP_ROOTPATH.'/Classes/Status.php';