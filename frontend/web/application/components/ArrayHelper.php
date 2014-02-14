<?php

class ArrayHelper
{
	public static function orderBy(&$arr, $col, $dir = SORT_ASC)
	{
		$sort_col = array();
		foreach($arr as $key => $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
	}

	public static function merge($a, $b)
	{
		$args = func_get_args();
		$res = array_shift($args);
		while (!empty($args)) {
			$next = array_shift($args);
			foreach ($next as $k => $v) {
				if (is_integer($k)) {
					isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
				} elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
					$res[$k] = self::merge($res[$k], $v);
				} else {
					$res[$k] = $v;
				}
			}
		}
		return $res;
	}
}
