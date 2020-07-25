<?php

namespace lib;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\ORM\Entity;

class StructHelper {

    /**
     * Получение значений пользовательского списка
     * @param $block_id
     * @return array
     */
    public static function getEnumItems($block_id) {

        $block_options = \CUserFieldEnum::GetList([],['BLOCK_ID'=> $block_id]);

        $result = [];
        foreach ($block_options->arResult as $item) {
            $result[] = [
                'ID' => $item['ID'],
                'VALUE' => $item['VALUE']
            ];
        }

        return $result;
    }

    /**
     * @param $hlblock_id
     * @param Entity $object
     * @return bool
     */
    public static function getBlockFieldsArray($hlblock_id, $values)
    {

        if (empty($hlblock_id) || empty($values)) {
            return false;
        }

        $fields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('HLBLOCK_'.$hlblock_id, 0, LANGUAGE_ID);
        if (!$fields) {
            return false;
        }

        foreach ($fields as $field) {

            if ($field['USER_TYPE_ID'] == 'enumeration') {
                $option = \CUserFieldEnum::GetList([], ['BLOCK_ID' => $hlblock_id, 'ID' => $values['UF_STATUS'][0]])->getNext();
                $values[$field['FIELD_NAME']] = ['ID' => $option['ID'], 'VALUE' => $option['VALUE']];
            }
        }
        return $values;
    }
}