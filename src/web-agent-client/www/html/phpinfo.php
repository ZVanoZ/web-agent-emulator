<h1><?=__FILE__?></h1>
<h2>Now: <?=(new DateTime())->format('Y-n-d h:i:s')?></h2>
<?php
phpinfo();
echo '<div style=" border: 1px dotted black;">';

echo '<br>$_REQUEST';
echo '<pre>';
var_dump($_REQUEST);
echo '</pre>';

echo '<br>$_SERVER';
echo '<pre>';
var_dump($_SERVER);
echo '</pre>';

echo '<br>ini_get_all()';
echo '<pre>';
var_dump(ini_get_all());
echo '</pre>';


$data = array();

$data['get max_execution_time-before'] = ini_get('max_execution_time');
$data['set max_execution_time'] = ini_set('max_execution_time', 3600);
$data['get max_execution_time-after'] = ini_get('max_execution_time');
$data['get default_socket_timeout'] = ini_get('default_socket_timeout');

echo '<br>$data';
echo '<pre>';
var_dump($data);
echo '</pre>';

echo '</div>';

