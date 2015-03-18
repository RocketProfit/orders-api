<?php
include 'RocketProfitApi.php';
$api = new RocketProfitApi();
$result = $api->createOrder($_REQUEST);

if ($result->code == RocketProfitApi::HTTP_REQUEST_STATUS_SUCCESS) {
	echo $result->message;
} else {
	echo 'Произошла ошибка: ' . $result->message . "<br/>Отладочная информация: <br/>";
	echo '<pre>';
	echo $api->debug_info;
}