<?php

return [
	'languages' => ['ru', 'en'],
	'robot_email' => 'robot@itwip.net',
	'allowNewAccounts' => 'yes', //открыта ли регистрация или нет default: yes
	'twitter' => [
        'accountsInApp' => 1000, //Аккаунтов в одном приложение
		'methods' => array(
			'fast' => array(
				'prices' => array(
					array(
						12 => 10,
						9 => 20,
						6 => 25,
						3 => 35,
						1 => 55
					)
				),
			)
		),
		'tweets' => array(
			'hashCount' => 3
		),
		'secret_key' => 'zla2x45v.,y312A6{]1A/!x3s6q2z32v0q6.z;q25z1',
		'salt' => 'Ka1->2{x2,3.Kqmmxz26q9z3Af."Q1q',
		'update_interval' => array(
			'yandex_rank' => '220', //в минутах
			'in_yandex' => '1440', //в минутах
			'google_pr' => '240', //в минутах
			'in_google' => '1440', //в минутах
			'all' => '1440', //в минутах
		),
		'posting_timeout' => '6', //в минутах
		'posting_timeout_max' => '10080', //в минутах
		'filters' => array(
			1 => 'personal', //мои фильтры
			2 => 'policy' //мои фильтры
		),
		'_finance_procent_extract' => 15
	],
	'extract_precent_system' => 'on', //включить изъятия системного процента
	'system_precent_extract' => 15, //процент системы, при пополнение и выводе
	'maxAutoEjectAmount' => 150, //максимальная сумма для автовывода
	'minAutoEjectAmount' => 10, //минимальная сумма для автовывода
	'autoEjectSumm' => 150,
	'ips' => [],
];