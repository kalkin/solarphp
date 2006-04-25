<?php
require_once 'Solar.php';
$config = dirname(__FILE__) . '/config.inc.php';
Solar::start($config);

// configure and run the suite
$config = array(
    'dir' => dirname(__FILE__) . '/Test',
    'sub' => isset($argv[1]) ? trim($argv[1]) : '',
);
$suite = Solar::factory('Solar_Test_Suite', $config);
$info = $suite->run();

// done
Solar::stop();
return $info;
?>