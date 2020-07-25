<?php

namespace lib;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\Type\DateTime;

class Validator {


    const RULE_DATETIME = 'checkDateTime';
    const RULE_NOT_EMPTY = 'checkNotEmpty';
    const RULE_DB_EXISTS = 'checkExistInDb';

    /**
     * Проверка поля на пустое значение
     * @param $fieldValue значение поля
     * @return array
     */
    public static function checkNotEmpty($fieldValue)
    {
        return !empty($fieldValue);
    }

    /**
     * Проверка значения даты и времени
     * @param $fieldValue
     */
    public static function checkDateTime($fieldValue)
    {
        if (!\DateTime::createFromFormat('d.m.Y H:i:s',$fieldValue)) {
            return false;
        }

        return true;
    }

    /**
     * Валидация массива полей
     * @param $fields
     * @param $rules
     * @return array
     */
    public static function validate($fields, $rules)
    {
        if (empty($fields)) {
            return [];
        }

        $errors = [];
        foreach ($fields as $fieldName => $fieldValue) {
            foreach ($rules as $rule) {
                if ($fieldName == $rule['field']) {
                    $result = call_user_func(get_class().'::'.$rule['ruleName'], $fieldValue);
                    if (!$result)
                        $errors[$fieldName][$rule['ruleName']] = self::getErrorMessage($rule['ruleName']);
                    }
                }
            }

        return $errors;
    }

    public static function getErrorMessage($ruleName)
    {
        $messages = [
            'checkDateTime' => 'Некорректное значение даты и времени',
            'checkNotEmpty' => 'Требуется ввести значение'
        ];

        return $messages[$ruleName];
    }
}