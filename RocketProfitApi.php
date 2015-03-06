<?php
/**
 * Class RocketProfitApi - пример реализации подключения к АПИ RocketProfit.ru
 *
 * @author Roman Agilov <agilovr@gmail.com>
 * @date 03.02.2014
 * @copyright Copyright &copy; RocketProfit.ru 2014-
 */
class RocketProfitApi
{

	/**
	 * @var integer Статус заказа "Не найден"
	 */
	const ORDER_STATUS_NOT_FOUND = 0;

	/**
	 * @const integer Публичный статус заказа "Ручной дозвон"
	 */
	const ORDER_STATUS_MANUAL_DIALING = 1;

	/**
	 * @const integer Публичный статус заказа "В работе"
	 */
	const ORDER_STATUS_IN_WORK = 2;

	/**
	 * @const integer Публичный статус заказа "Недозвон"
	 */
	const ORDER_STATUS_CANT_DIAL = 8;

	/**
	 * @const integer Публичный статус заказа "Доставляется"
	 */
	const ORDER_STATUS_SHIPPING = 3;

	/**
	 * @const integer Публичный статус заказа "Доставлен"
	 */
	const ORDER_STATUS_SHIPPED = 4;

	/**
	 * @const integer Публичный статус заказа "Доставлен (вознаграждение выплачено)"
	 */
	const ORDER_STATUS_PAID = 5;

	/**
	 * @const integer Публичный статус заказа "Отменен"
	 */
	const ORDER_STATUS_CANCELLED = 6;

	/**
	 * @var integer Статус ответа, если он успешный
	 */
	const HTTP_REQUEST_STATUS_SUCCESS = 200;

	/**
	 * @var string Логин пользователя
	 */
	private $_login;

	/**
	 * @var string Ключ апи
	 */
	private $_api_key;

	/**
	 * @var string URL для обращения к API
	 */
	public $api_url = 'http://www.rocketprofit.ru/crm/api/';

	/**
	 * @var array Поля для передачи
	 */
	public $data;

	/**
	 * @var integer Статус ответа
	 */
	public $status;

	/**
	 * @var integer Сообщение об ошибке или успешно завершенной операции
	 */
	public $message;

	/**
	 * @var string Режим дебага - включить если нужно
	 */
	public $debug_mode = true;

	/**
	 * @var string Информация для отладки
	 */
	public $debug_info;

	/**
	 * @var array Доступные на данный момент публичные статусы
	 */
	public static $statuses = [
		RocketProfitApi::ORDER_STATUS_NOT_FOUND => "Не найден",
		RocketProfitApi::ORDER_STATUS_MANUAL_DIALING => "Ручной дозвон",
		RocketProfitApi::ORDER_STATUS_CANT_DIAL => "Недозвон",
		RocketProfitApi::ORDER_STATUS_IN_WORK => "В работе",
		RocketProfitApi::ORDER_STATUS_SHIPPING => "Доставляется",
		RocketProfitApi::ORDER_STATUS_SHIPPED => "Доставлен",
		RocketProfitApi::ORDER_STATUS_PAID => "Доставлен (вознаграждение выплачено)",
		RocketProfitApi::ORDER_STATUS_CANCELLED => "Отменен",
	];

	/**
	 * Конструктор.
	 *
	 * Можно обойтись и без него, если прописать
	 * логин и пароль хардкодом прямо в классе
	 *
	 * @param string $login Логин пользователя в системе rocketprofit.ru
	 * @param string $api_key Ключ API пользователя в системе rocketprofit.ru
	 */
	public function __construct($login = null, $api_key = null)
	{
		$this->_login = $login;
		$this->_api_key = $api_key;
	}


	/**
	 * Проверка статусов заказов
	 *
	 * @param string|array $ids Ид заказов для проверки (массив или строка через запятую)
	 * @return array Ответ сервера
	 */
	public function getStatuses($ids)
	{
		if (is_array($ids)) {
			$ids = implode(',', $ids);
		}
		$this->data['login'] = $this->_login;
		$this->data['key'] = md5($ids . $this->_api_key);
		$this->data['orders'] = $ids;

		return $this->request('getOrdersStatuses');
	}

	/**
	 * Создание заказа
	 *
	 * @param array $data Данные для создания заказа (читайте документацию апи)
	 * @return array Ответ сервера
	 */
	public function createOrder($data)
	{
		$data['jsonp'] = 0;
		$this->data = $data;

		return $this->request('createOrder');
	}


	/**
	 * Отправка запроса на сервер
	 *
	 * @param string $method Вызвываемый метод
	 * @return integer Статус ответа сервера
	 */
	private function request($method)
	{
		if (!$method) {
			return false;
		}

		$request = http_build_query($this->data);

		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $this->api_url . $method);
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl_handle, CURLOPT_POST, 1);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $request);
		$curl_answer = curl_exec($curl_handle);
		curl_close($curl_handle);

		if ($this->debug_mode) {
			ob_start();
			echo "-------START RocketProfitApi::Request(...);-------\r\n";
			echo "REQUEST URL: \r\n" . $this->api_url . $method . "\r\n\r\n";
			echo "POST DATA: \r\n";
			print_r($request);
			echo "\r\n\r\n";
			echo "CURL ANSWER: \r\n";
			print_r($curl_answer);
			echo "\r\n\r\n";
			echo "-------END RocketProfitApi::Request(...);-------\r\n";
			$this->debug_info .= ob_get_clean();
		}

		return json_decode($curl_answer);
	}

	/**
	 * Возвращает имя статуса по его идентификатору
	 *
	 * @param $status_id
	 * @return array
	 */
	public function getOrderStatusName($status_id)
	{
		return isset(self::$statuses[$status_id]) ? self::$statuses[$status_id] : $status_id;
	}
}