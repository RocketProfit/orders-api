<?php
if(empty($_REQUEST['login'])){
	echo "Не заполнен логин!";
	return;
}

if(empty($_REQUEST['api_key'])){
	echo "Не заполнен ключ АПИ!";
	return;
}

if(empty($_REQUEST['from']) || empty($_REQUEST['to'])){
	echo "Не передан период выборки!";
	return;
}

include 'RocketProfitApi.php';
$api = new RocketProfitApi($_REQUEST['login'], $_REQUEST['api_key']);
$result = $api->getOrdersInfo($_REQUEST['from'], $_REQUEST['to']);

if ($result->status == RocketProfitApi::HTTP_REQUEST_STATUS_SUCCESS) {
	foreach ($result->orders as $order_id => $status) {
		echo "Заказ #$order_id имеет статус '" . $api->getOrderStatusName($status) . "'<br/>";
	}
} else {
	echo 'Произошла ошибка: ' . $result->message . "<br/>Отладочная информация: <br/>";
	echo '<pre>';
	echo $api->debug_info;
}