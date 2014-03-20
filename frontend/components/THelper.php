<?php

/**
 * Приводим все значения формулы к одному диапазону размерности (напр. 0-30). И потом суммируем с коэффициентами значимости.
 * Приведение необходимо проводить по нелинейной функции, напр. квадратный корень. Точнее
 *
 *
 * Показатель FW. Его коєффициент пересчета к:
 * 30 – это параметры Билла или типа того. 30 соответствует 3 млн читателей.
 * Значит k=y/sqrt(x)
 * k=30/sqrt(3млн)
 *
 * Читаемых предлагаю не учитывать.
 *
 * Дней в твиттере DB.
 * Дата первого твита в твиттере - 21 марта 2006
 * https://twitter.com/jack/status/29
 * Today() ¬ (21/03/2006) - это 30
 * k=30/sqrt(Today() ¬ (21/03/2006))
 *
 * Списки. SP Для точки отсчета надо иметь максимальное к-во списков… у кого?
 * Коєф пересчета аналогичен.
 *
 * Формула
 * В1, В2, В3 – наши вручную устанавливаемые коэффициенты важности параметра.
 * k1, k2, k3 – коэффициенты «приведения» к одному диапазону.
 * ИТВИПдикий= В1*k1*sqrt(FW) + B2*k2*sqrt(DB) + B3*k3*SP
 * ИТВИПБилла= В1*k1*sqrt(FW) + B2*k2*sqrt(DB) + B3*k3*SP
 * ИТВИП=ИТВИПдикий*30/ИТВИПБилла
 *
 * Любой следующий параметр можно добавлять аналогично. Высчитывая коэф пересчета (и имея макс значение).
 */
class THelper
{

	public static function itr($statuses, $followers, $at_create, $listed_count, $ya, $gp, $md = 10)
	{
		$md   = $md * 0.1;
		$fitr = self::pCalc($statuses, $followers, $at_create, $listed_count, $md);

		$pit  = ($gp + sqrt($ya) / 254) * 2.2;
		$pit  = ($pit > 0 AND $pit < 1) ? 1 : round($pit, 1);
		$fitr = ($fitr > 0 AND $fitr < 1) ? 1 : round($fitr, 1);

		$itr = ($fitr + $pit) * $md;

		return ($itr >= 1) ? $itr : 1;
	}

	public static function pCalc($statuses, $followers, $at_create, $listed_count, $md)
	{
		$itr_1 = ((sqrt(3000000 * 0.7)) +
				(ceil((time() - strtotime('2006-03-21')) / 86400) * 0.5) +
				(sqrt(1000000) * 0.3) +
				(sqrt(100000) * 1)) / 40;

		$itr = ((sqrt($followers * 0.7)) +
				(ceil((time() - strtotime($at_create)) / 86400) * 0.5) +
				(sqrt($statuses) * 0.3) +
				(sqrt($listed_count) * 1)) / $itr_1;

		return $itr;
	}

	public static function itrCost($itr)
	{
		$_itr = 0.50;
		$cost = 0.10;

		$s = 1;

		for($i = 1; $i <= $itr; $i = $i + 0.1) {
			$_itr += $cost;

			if($s < floor($i)) {
				$cost += 0.10;
				$s++;
			}
		}

		return $_itr < 1 ? 1 : $itr;
	}

	public static function setParams($list, $options)
	{
		$params = [];
		$days   = ['today' => 1, 'three_days' => 3, 'seven_days' => 7, 'month' => 31];

		foreach($list as $k => $v) {
			if(array_key_exists($k, $options)) {
				switch($options[$k]['t']) {
				case "enum":
					if($v != 'matter')
						$params[] = ['fields' => $options[$k]['c'] . $options[$k]['w'] . ':' . $k, 'values' => [$k, ($v == 'yes') ? 1 : 0]];
					break;
				case "value":
					if($v != 'matter')
						$params[] = ['fields' => $options[$k]['c'] . $options[$k]['w'] . ':' . $k, 'values' => [$k, $v]];
					break;
				case "days":
					if($v != 'all') {
						$time = NULL;

						if(array_key_exists($v, $days)) {
							$time = time() - ($days[$v] * 86400);
						}
						else if(intval($v)) {

							$time = time() - ($v * (31 * 86400));
						}

						if($time)
							$params[] = ['fields' => $options[$k]['c'] . $options[$k]['w'] . ':' . $k, 'values' => [$k, $time]];
					}
					break;
				case "in":
					if(is_array($v) AND count($v))
						$params[] = ['fields' => $options[$k]['c'] . ' IN(\'' . implode('\',\'', $v) . '\')'];
					break;
				case "sql":
					if($v == 1)
						$params[] = ['fields' => $options[$k]['c']];
					break;

				case "not":
					if($options[$k]['w'] != $v)
						$params[] = ['fields' => $options[$k]['c'] . '=:' . $k, 'values' => [$k, $v]];
					break;

				default:
					if(is_numeric($v) AND $v > 0)
						$params[] = ['fields' => $options[$k]['c'] . $options[$k]['w'] . ':' . $k, 'values' => [$k, $v]];
				}
			}
		}

		return $params;
	}
}
