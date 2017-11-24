<?php

namespace common\helpers;


class DateHelper
{
    public static function plural($number, $form1, $form2, $form3) {
        if (in_array($number % 10, [2, 3, 4]) && !in_array($number % 100, [11, 12, 13, 14])) {
            return $form2;
        } else {
            return $number % 10 == 1 ? $form1 : $form3;
        }
    }
}