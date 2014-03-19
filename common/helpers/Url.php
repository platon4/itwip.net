<?php

namespace common\helpers;

use Yii;

class Url
{
    private static $_hostInfo;

    public static function getDomen($url)
    {
        if($url == '')
            return;

        $url = str_replace("http://", "", strtolower($url));
        $url = str_replace("https://", "", $url);
        if(substr($url, 0, 4) == 'www.')
            $url = substr($url, 4);

        $url = explode('/', $url);
        $url = reset($url);
        $url = explode(':', $url);
        $url = reset($url);

        return $url;
    }

    public static function getHostUrl()
    {
        if(self::$_hostInfo === null) {
            $secure = Yii::$app->request->getIsSecureConnection();
            $http = $secure ? 'https' : 'http';

            if(isset($_SERVER['HTTP_APP_IP'])) {
                self::$_hostInfo = $http . '://' . $_SERVER['HTTP_APP_IP'];
            }
        }

        return self::$_hostInfo;
    }

    public static function homeUrl()
    {
        return rtrim(Yii::$app->homeUrl, '/');
    }

    public static function get($url, $params = [], $method = 'GET', $cookie = false, $headers = [], $ip = false)
    {
        $curl = curl_init();

        switch($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            default:
                if(count($params)) {
                    $p = [];

                    foreach($params as $k => $v)
                        $p[] = $k . '=' . $v;

                    $qs = implode('&', $p);
                    $url = strlen($qs) > 0 ? $url . '?' . $qs : $url;
                    $params = [];
                }
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);

        if($cookie) {
            if(!is_dir(Yii::$app->getRuntimePath() . '/cookie')) {
                @mkdir(Yii::$app->getRuntimePath() . '/cookie');
                @chmod(Yii::$app->getRuntimePath() . '/cookie', 0777);
            }

            curl_setopt($curl, CURLOPT_COOKIEJAR, Yii::$app->getRuntimePath() . '/cookie/' . md5(self::getDomen($url) . Yii::$app->request->getUserIP()) . '.txt'); //сохранить куки в файл
            curl_setopt($curl, CURLOPT_COOKIEFILE, Yii::$app->getRuntimePath() . '/cookie/' . md5(self::getDomen($url) . Yii::$app->request->getUserIP()) . '.txt'); //считать куки из файла
        }

        //устанавливаем наш вариат клиента (браузера) и вид ОС
        if(!empty($_SERVER['HTTP_USER_AGENT']))
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        else
            curl_setopt($curl, CURLOPT_USERAGENT, "Opera/10.00 (Windows NT 5.1; U; ru) Presto/2.2.0");

        $icount = isset(Yii::$app->params['ips']) && is_array(Yii::$app->params['ips']) ? count(Yii::$app->params['ips']) : 0;

        if($icount || $ip !== false) {
            curl_setopt($curl, CURLOPT_INTERFACE, $ip === false ? Yii::$app->params['ips'][rand(0, $icount - 1)] : $ip);
        }

        //Установите эту опцию в ненулевое значение, если вы хотите, чтобы PHP завершал работу скрыто, если возвращаемый HTTP-код имеет значение выше 300. По умолчанию страница возвращается нормально с игнорированием кода.
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);

        //Максимальное время в секундах, которое вы отводите для работы CURL-функций.
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);

        //Установите эту опцию в ненулевое значение, если вы хотите, чтобы шапка/header ответа включалась в вывод.
        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // разрешаем редиректы
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if(count($headers)) {

            $header = array();

            foreach($headers as $k => $v) {
                $header[] = trim($k . ': ' . $v);
            }

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($curl); // выполняем запрос и записываем в переменную

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($curl);
        $error = curl_error($curl);
        $error_code = curl_errno($curl);

        curl_close($curl); // заканчиваем работу curl

        return ['response' => $response, 'code' => $code, 'info' => $info, 'error' => $error, 'error_code' => $error_code];
    }
} 