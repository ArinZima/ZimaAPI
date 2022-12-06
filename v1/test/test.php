<?php
    require '../../loader.php';
    require '../../Controllers/TestController.php';

    use Zima\Classes\Key;
    use Zima\Controllers\TestController;

    $key = new Key();
    $auth = $key->auth();

    if($auth) {
        $test = new TestController();
        $test->do_test();
    }