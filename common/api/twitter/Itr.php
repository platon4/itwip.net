<?php

namespace common\api\twitter;

use Yii;

class Itr
{
    public static function _($statuses, $followers, $at_create, $listed_count, $ya, $gp, $md = 10)
    {
        $md = $md * 0.1;
        $fitr = self::calc($statuses, $followers, $at_create, $listed_count, $md);

        $pit = ($gp + sqrt($ya) / 254) * 2.2;
        $pit = ($pit > 0 AND $pit < 1) ? 1 : round($pit, 1);
        $fitr = ($fitr > 0 AND $fitr < 1) ? 1 : round($fitr, 1);

        $itr = ($fitr + $pit) * $md;

        return ($itr >= 1) ? $itr : 1;
    }

    protected static function calc($statuses, $followers, $at_create, $listed_count, $md)
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

    public static function cost($itr)
    {
        $_itr = 0.50;
        $cost = 0.10;

        $s = 1;

        for($i = 1 ; $i <= $itr ; $i = $i + 0.1) {
            $_itr += $cost;

            if($s < floor($i)) {
                $cost += 0.10;
                $s++;
            }
        }

        return $_itr < 1 ? 1 : $_itr;
    }
}