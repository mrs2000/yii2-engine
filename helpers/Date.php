<?php

namespace mrssoft\engine\helpers;

use yii;

/**
 * Хелпер для работы с датами
 */
class Date
{
    public static function date($value): string
    {
        if (empty($value)) {
            return '';
        }
        $value = new \DateTime($value);
        $formater = new \IntlDateFormatter(Yii::$app->language, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, Yii::$app->timeZone);
        return $formater->format($value);
    }

    public static function datetime($value): string
    {
        if (empty($value)) {
            return '';
        }
        $value = new \DateTime($value);
        return self::getDate($value) . ' ' . $value->format('H:i');
    }

    private static function getDate($value): string
    {
        $formater = new \IntlDateFormatter(Yii::$app->language, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, Yii::$app->timeZone);

        return $formater->format($value);
    }
}