# API для работы с заказами
## ->createOrderWithCustomData
Создание заказа с добавлением пользовательских данных

#### Пример URL для обращения:
 Данне можно передавать методом GET или POST

 http://www.rocketprofit.com/crm/api/createOrderWithCustomData?flow_id=1&phone=82223334455&fio=Теркин%20Василий%20Василиевич&key=bc29b36f623ba82aaf6724fd3b16718&custom_data[my_data]=myrealdata&custom_data[my_custom_data]=custom_data_data

#### Возможные данные для передачи:
 (Обязательные элементы помечены звездочкой)

     {
        'phone' : '8913154884', // * число или строка, содержащая только цифры от 10 до 13 знаков
        'fio' : 'Петров Василий Юрьевич', // * любая сторка
        'flow_id' : '123', // * число или строка, содержащая только цифры  - ID потока в системе rocketprofit.com
        'key' : '1bc29b36f623ba82aaf6724fd3b16718', // * строка, которая формируется по алгоритму md5($api_key.$flow_id.$phone); Где точка - конкатенация строк;
        'ip' : '195.123.234.23', // строка, IP адрес покупателя
        'date' : '2015-05-06 22:18:12', // Дата создания заказа в формате TIMESTAMP
        'custom_data' : {'myData' : 123}, // произвольный массив данных, который сохранятся в нашей системе и жет быть запрошен позже
        'subids' : {'adv' : 'adv123', 'src' : 'vk'}, // массив данных, который сохранятся в нашей системе и будет интерпретирован как SubID
        'webmaster_id' : 'webmaster', //Идентификатор вебмастера в партнерской системе. Обязателен для партнерских сетей, для прочих вебмастеров игнорируется
        'country_code' : 'RU', //Alpha-2 ISO 3166-1 код страны в которую доставляется заказ. Список кодов стран: https://ru.wikipedia.org/wiki/ISO_3166-1 Если не передан - по умолчанию принимается RU (Россия). Должен быть в верхнем регистре
        'traffic_type' : '1' // Тип трафика (1 - веб, 2  - мобильный, 3 - неопределенно)
    }

#### JSON ответ:
    {
        'code': $code, // код ответа (200 - если успех и любой другой, если ошибка)
        'message': $message, // сообщение об успешно выполненной операции, либо об ошибке (зависит от кода)
        'order_id': $order_id // номер заказа (возвращается только в случае успешно завершенной операции)
    }

 Варианты:
 * 200 - Заказ успешно создан! Номер заказа: $order_id
 * 481 - Заполните телефон!
 * 482 - Заполните ФИО!
 * 483 - Невозможно определить поток! ( не передан flow_id)
 * 484 - Поток № $flow_id не зарегистрирован в системе!
 * 485 - Неверный токен!
 * 500 - Ошибка валидации поля (Если какое-либо переданное поле не прошло валидацию)
 







### ->GetOrdersInfo
 Запрос данных по заказам за определенный период
 
#### Пример URL для обращения
 Данные можно передавать методом GET или POST
 
 http://www.rocketprofit.com/crm/api/getOrdersInfo?login=BestPartner&key=0a153b13d8a443915f832424b8f7949a&from=2015-02-08+00:00:00&to=2015-02-09+23:59:59
 или
 http://www.rocketprofit.com/crm/api/getOrdersInfo?login=BestPartner&key=0a153b13d8a443915f832424b8f7949a&orders=14,28,31,42,123

#### Данные для передачи
  * login
  * key
  * from
  * to
  * orders
 
 #### Формат данных
  * login - логин сервера в системе rocketprofit.com
  * from - начало выборки по периоду вида '2015-05-06 22:18:12'
  * to - конец выборки по периоду вида '2015-05-07 22:18:12'
  * orders - строка с идентификаторами заказов в вашей системе через запятую либо массив идентификаторов
  * key - ключ авторизации в любом регистре, который формируется по следующему алгоритму:
       * Для строки с идентификаторами: md5($orders.$api_key.$from.$to);
       * Для массива с идентификаторами: md5(implode(",", $orders).$api_key.$from.$to)
  *
  * Где:
  * $orders - идентификаторы заказов (строка либо массив)
  * $api_key - это ключ API вашего пользователя в системе rocketprofit.com,
  * . - это конкатенация строк
  Обязательные поля: login, key. Также должны быть или идентификаторы заказов или даты from-to 
         
#### Формат ответа

     {
        "status": $status,
        "orders" : {
        {
            "id" : 999, // Ид в нашей системе
            "status_id" : 22, // Ид статуса в нашей системе
            "date" : "2015-02-02 12:12:21", // Дата создания заказа
            "ip" : "115.201.2.34", // IP адрес заказчика
            "geo" : "RU", // Код страны заказчика
            "money" : 550, // Вознаграждение за заказ в рублях
            "money_paid" : 550, // Начисленное вознаграждение за заказ в рублях
            "custom_data" : {"mySysId" : 7809}, // Ваши данные по заказы переданные ранее при создании заказа
            "subids" : {'adv' : 'adv123', 'src' : 'vk'}, // SubID по этому заказу
            "comment" : "ляляля", // Комментарий к заказу
            "trouble" : null // Номер статуса проблемы. Присутсвует, если заказ в статусе "Отменен" или "Невалид"
        },
        {...}
        }
     }

     // или
     {
          "status" : $status, // статус ответа (200 - если успех, любой другой - ошибка)
          "message" : $message // сообщение об ошибке
     }

#### Варианты ответов
 * 492 - $param is required (Если вы забыли передать один из обязательных параметров)
 * 493 - Wrong login (Если вы передали незарегистрированный в нашей системе логин или вы заблокированы)
 * 494 - Wrong key: $key (Если вы передали неверный ключ $key - ключ, который вы передали)
 * 200 - Массив с данными заказов

#### Перечень возможных статусов для заказов по доставке:
 * 1 - Новый
 * 8 - Недозвон
 * 2 - В работе
 * 3 - Доставляется
 * 4 - Доставлен
 * 5 - Доставлен (вознаграждение начислено)
 * 6 - Отменен

 #### Перечень возможных статусов для заказов по подтвержденке:
 * 0 - Не найден (если заказ с переданным вами идентификатором не был найден в нашей системе)
 * 1 - В работе
 * 2 - Подтверждено
 * 3 - Отмена
 * 4 - Невалид
 
#### Перечень статусов проблем:
 * 16 - Дубль
 * 18 - Невалидный номер
 * 19 - Спам
 * 201 - Не заказывал
 * 202 - Передумал
 * 203 - Дорогая доставка
 * 204 - Дорогой товар
 * 205 - Долгая доставка
 * 206 - Отсутствие курьерской доставки
 * 207 - Нужен оригинал
 * 208 - Сомнения в товаре
 * 209 - Автоматическая отмена
 * 210 - Фрод
 * 211 - Другое
 
### ->GetOrdersStatuses
 Запрос данных по статусам заказов
 
#### Пример URL для обращения
 Данные можно передавать методом GET или POST
 
http://www.rocketprofit.com/crm/api/getOrdersStatuses?login=BestPartner&orders=14,28,31,42,123&key=0a153b13d8a443915f832424b8f7949a

#### Данные для передачи
 * login
 * orders
 * key

#### Формат данных
 * login - логин сервера в системе rocketprofit.com
 * orders - строка с идентификаторами заказов в вашей системе через запятую либо массив идентификаторов
 * key - ключ авторизации в любом регистре, который формируется по следующему алгоритму:
 * Для строки с идентификаторами: md5($orders.$api_key);
 * Для массива с идентификаторами: md5(implode(",", $orders).$api_key)
 *
 * Где:
 * $orders - идентификаторы заказов (строка либо массив)
 * $api_key - это ключ API вашего пользователя в системе rocketprofit.com,
 * . - это конкатенация строк
 
#### Формат ответа
 * json-строка: {"status": $status, "orders" : {
 * 123: 0,
 * 897: 2,
 * 564: 3,
 * ...
 * }}
 *
 *
 * если ошибка: {"status": $status, "message" : $message}
 *
 * Где:
 * $status (число) - статус ответа (200 - если успех, любой другой - ошибка)
 * $message (строка) - сообщение об ошибке
 *
 * Http status code ответа соответсвует $status,
 * пояснение к Http status code соответсвует $message.
 *
 * Наприметр вы получите: 492 "login" is required
 *
 * ====Возможны следующие варианты ответов:====
 * =====Ошибки:=====
 * 492 - $param is required (Если вы забыли передать один из обязательных параметров)
 * 493 - Wrong login (Если вы передали незарегистрированный в нашей системе логин или вы заблокированы)
 * 494 - Wrong key: $key (Если вы передали неверный ключ $key - ключ, который вы передали)
 *
 * =====Успех:=====
 * 200 - Массив со статусами заказов
 *
 *
 * #### Перечень возможных сатусов для заказов по доставке:
 * 1 - Новый
 * 8 - Недозвон
 * 2 - В работе
 * 3 - Доставляется
 * 4 - Доставлен
 * 5 - Доставлен (вознаграждение начислено)
 * 6 - Отменен
 *
 * #### Перечень возможных сатусов для заказов по подтвержденке:
 * 0 - Не найден (если заказ с переданным вами идентификатором не был найден в нашей системе)
 * 1 - В работе
 * 2 - Подтверждено
 * 3 - Отмена
 * 4 - Невалид
