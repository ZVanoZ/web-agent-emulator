<?php
$loader = require_once '../vendor/autoload.php';
//$loader->addPsr4('WebAgentServer\\', realpath('../src'));
$app = new \WebAgentServer\App();
$app->run();
