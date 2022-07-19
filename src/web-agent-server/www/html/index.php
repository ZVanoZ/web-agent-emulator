<?php
$loader = require_once '../vendor/autoload.php';
$loader->addPsr4('WebAgentServer\\', realpath('../src'));
(new \WebAgentServer\App)->run();
