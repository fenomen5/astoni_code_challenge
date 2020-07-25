<?

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Error;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
Loader::includeModule("highloadblock");

class CTaskList extends CBitrixComponent implements Controllerable
{
    const WORK_IN_PROGRESS = 1;
    const WORK_IS_DONE = 2;

    const BLOCK_ID = 1;
    public function configureActions()
    {
        return [
            'updateStatus' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                ],
                'postfilters' => []
            ]
        ];
    }

    public function executeComponent()
    {
        if($this->startResultCache(3600, false, false))
        {
            $this->arResult = $this->getTasksList();
            $this->endResultCache();
        }
        $this->includeComponentTemplate();
        return $this->arResult["Y"];
    }

    /**
     * Получение списка задач
     * @return int
     * @throws \Bitrix\Main\LoaderException
     */
    protected function getTasksList()
    {
        $hlblock_id = $this->arParams['BLOCK_ID'];
        if (empty($hlblock_id)) {
            ShowError(GetMessage('HLBLOCK_LIST_NO_ID'));
            return 0;
        }

        $hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
        if (empty($hlblock)) {
            ShowError(GetMessage('HLBLOCK_LIST_404'));
            return 0;
        }
        $this->checkRights($hlblock_id);

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $fields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('HLBLOCK_'.$hlblock['ID'], 0, LANGUAGE_ID);

        $sortId = 'ID';
        if (isset($this->arParams['SORT_FIELD']) && isset($fields[$arParams['SORT_FIELD']])) {
            $sortId = $this->arParams['SORT_FIELD'];
        }
        
        $mainQuery = new Entity\Query($entity);
        $mainQuery->setSelect(['*']);
        $mainQuery->setOrder([$sortId => 'DESC']);
        
        $result = $mainQuery->exec();
        $result = new CDBResult($result);

        $rows = [];
        $tableColumns = [];
        while ($row = $result->fetch())
        {
            foreach ($row as $k => $v)
            {
                $arUserField = $fields[$k];

                if ($k == 'ID') {
                    $tableColumns['ID'] = true;
                    continue;
                }
                if ($arUserField['SHOW_IN_LIST'] != 'Y') {
                    continue;
                }


                $html = call_user_func_array(
                    [$arUserField['USER_TYPE']['CLASS_NAME'], 'getadminlistviewhtml'],
                    [
                        $arUserField,
                        [
                            'NAME' => 'FIELDS['.$row['ID'].']['.$arUserField['FIELD_NAME'].']',
                            'VALUE' => htmlspecialcharsbx(is_array($v) ? implode(', ', $v) : $v)
                        ]
                    ]
                );

                if ($arUserField['USER_TYPE_ID'] =='enumeration') {
                    $html = [
                        'value' => $html,
                        'id' => $v
                    ];
                }

                $tableColumns[$k] = true;
                $row[$k] = $html;
            }

            $rows[] = $row;
        }

        $arResult['rows'] = $rows;
        $arResult['fields'] = $fields;
        $arResult['tableColumns'] = $tableColumns;
        $arResult['sort_id'] = $sortId;

        return $arResult;
    }

    /**
     * Проверка прав доступа к инфоблоку
     * @param $hlblock_id
     * @return int
     */
    protected function checkRights($hlblock_id)
    {

        if (isset($this->arParams['CHECK_PERMISSIONS']) && $this->arParams['CHECK_PERMISSIONS'] == 'Y' && !$USER->isAdmin()) {
            $operations = HL\HighloadBlockRightsTable::getOperationsName($hlblock_id);
            if (empty($operations)) {
                ShowError(GetMessage('HLBLOCK_LIST_404'));
                return 0;
            }
        }
    }
    /**
     * Обновление статуса задачи
     */
    public function updateStatusAction($task_id, $status)
    {
        if (empty($task_id)) {
            return AjaxJson::createError(new ErrorCollection([new Error(GetMessage('HLBLOCK_TASK_NO_ID'))]));
        }

        $hlblock_id = $this->arParams['BLOCK_ID'];
        if (empty($hlblock_id)) {
            return AjaxJson::createError(new ErrorCollection([new Error(GetMessage('HLBLOCK_LIST_NO_ID'))]));
        }

        $hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
        if (empty($hlblock)) {
            return AjaxJson::createError(new ErrorCollection([new Error(GetMessage('HLBLOCK_LIST_404'))]));
        }

        $this->checkRights($hlblock_id);

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);

        $fields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('HLBLOCK_'.$hlblock['ID'], 0, LANGUAGE_ID);
        $available_statuses = array_column(\lib\StructHelper::getEnumItems($fields['UF_STATUS']['ID']),'ID');

        if (!in_array($status , $available_statuses)) {
            return AjaxJson::createError(new ErrorCollection([new Error(GetMessage('INVALID_STATUS_ID'))]));
        }

        $entity_data_class = $entity->getDataClass();

        $task = $entity_data_class::getById($task_id)->fetchRaw();

        if (empty($task)) {
            return AjaxJson::createError(new ErrorCollection([new Error(GetMessage('INVALID_TASK_ID'))]));
        }

        /** @var \Bitrix\Main\ORM\Data\UpdateResult $result */
        $result = $entity_data_class::update($task_id, ['UF_STATUS' => $status]);

        if ($result->isSuccess()) {

            /** @var \Bitrix\Main\ORM\Entity $task */
            $task = $entity_data_class::getById($task_id)->fetch();
            $data = \lib\StructHelper::getBlockFieldsArray($hlblock['ID'], $task);
            self::clearComponentCache($this->getName());
            return AjaxJson::createSuccess(['task' => $data]);
        }

        return AjaxJson::createError($result->getErrors());
    }

    /**
     * Удаление задачи
     */
    public function removeTaskAction($task_id)
    {
        if (empty($task_id) && $task_id != 0) {
            return AjaxJson::createError(new ErrorCollection([new Error(GetMessage('HLBLOCK_TASK_NO_ID'))]));
        }

        $hlblock_id = $this->arParams['BLOCK_ID'];
        if (empty($hlblock_id)) {
            return AjaxJson::createError(new ErrorCollection([new Error(GetMessage('HLBLOCK_LIST_NO_ID'))]));
        }

        $hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
        if (empty($hlblock)) {
            return AjaxJson::createError(new ErrorCollection([new Error(GetMessage('HLBLOCK_LIST_404'))]));
        }

        $this->checkRights($hlblock_id);

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);

        $entity_data_class = $entity->getDataClass();

        $result = $entity_data_class::delete($task_id);

        if ($result->isSuccess()) {
            self::clearComponentCache($this->getName());
            return AjaxJson::createSuccess();
        }

        return AjaxJson::createError($result->getErrors());
    }

    public function onPrepareComponentParams($arParams)
    {
        if (empty($arParams['BLOCK_ID'])) {
            $arParams['BLOCK_ID'] = self::BLOCK_ID;
        }

        return $arParams;
    }
}