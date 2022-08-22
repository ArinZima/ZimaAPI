<?php
    require '../../loader.php';

    $controls = new KeyController();

    $controls::authorize(function() {
        $test = new TestController();

        $test::do_test();
    });