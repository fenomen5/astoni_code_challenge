<?

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use \lib\Validator;

Loader::includeModule("highloadblock");

class CTaskList extends CBitrixComponent
{

    public $arErrors;
    public $oldValues;

    public function executeComponent()
    {
        if ($this->request->isPost()) {
            $result = $this->saveTask();
            if (!empty($result) && $result->isSuccess()) {
                LocalRedirect($SITE_SERVER_NAME. '/taskmgr');
            }
        }

        if ($this->arParams['TASK_ID']) {
            $this->oldValues = $this->getTaskFields($this->arParams['TASK_ID']);
        }

        $this->arResult['fields'] = $fields = $GLOBALS['USER_FIELD_MANAGER']
            ->GetUserFields('HLBLOCK_'. $this->arParams['BLOCK_ID'], 0, LANGUAGE_ID);

        $this->includeComponentTemplate();
        return $this->arResult["Y"];
    }

    /**
     * Создание или обновление данных задачи
     * @return Entity\Result
     * @throws \Bitrix\Main\LoaderException
     */
    protected function saveTask()
    {
        $hlblock_id = $this->arParams['BLOCK_ID'];
        if (empty($hlblock_id)) {
            ShowError(GetMessage('LIST_NO_ID'));
            return 0;
        }

        $hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
        if (empty($hlblock)) {
            ShowError(GetMessage('LIST_404'));
            return 0;
        }
        $this->checkRights($hlblock_id);

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $fields = $this->request->getPostList()->toArray();

        $rules = [
            ['field' => 'UF_TASK_DATETIME', 'ruleName'=>Validator::RULE_DATETIME],
            ['field' => 'UF_TASK_DATETIME', 'ruleName'=>Validator::RULE_NOT_EMPTY],
            ['field' => 'UF_NAME', 'ruleName'=>Validator::RULE_NOT_EMPTY],
            ['field' => 'UF_STATUS', 'ruleName'=>Validator::RULE_NOT_EMPTY]
        ];

        $errors = Validator::validate($fields,$rules);
        if (!empty($errors)) {
            $this->arErrors = $errors;
            $this->oldValues = $fields;
            return false;
        }
        $dataClass = $entity->getDataClass();

        if (empty($fields['ID'])) {
            try {
                $result = $dataClass::add(array_diff_key($fields,['ID'=>'']));
            } catch (\Exception $e) {
                error_log(GetMessage('TASK_CANNOT_BE_CREATED') . $e->getMessage());
                $this->arErrors = ['PAGE_ERROR' => GetMessage('TASK_CANNOT_BE_CREATED')];
                return false;
            }
        } else {
            try {
                $result = $dataClass::update($fields['ID'], $fields);
            } catch (\Exception $e) {
                error_log(GetMessage('TASK_CANNOT_BE_UPDATED') . $e->getMessage());
                $this->arErrors = ['PAGE_ERROR' => GetMessage('TASK_CANNOT_BE_UPDATED')];
                return false;
            }
        }

        self::clearComponentCache('astoni:tasks.list');
        return $result;
    }

    /**
     * Получить поля указанной задачи
     * @param $taskID
     * @return bool|int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getTaskFields($taskID)
    {
        $hlblock_id = $this->arParams['BLOCK_ID'];
        if (empty($hlblock_id)) {
            ShowError(GetMessage('LIST_NO_ID'));
            return 0;
        }

        $hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
        if (empty($hlblock)) {
            ShowError(GetMessage('LIST_404'));
            return 0;
        }
        $this->checkRights($hlblock_id);

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $task = $entity_data_class::getById($taskID)->fetch();
        $data = \lib\StructHelper::getBlockFieldsArray($hlblock['ID'], $task);

        $data['UF_STATUS'] = $data['UF_STATUS']['ID'];
        return $data;
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
                ShowError(GetMessage('LIST_404'));
                return 0;
            }
        }
    }

    public function onPrepareComponentParams($arParams)
    {
        if (empty($arParams['BLOCK_ID'])) {
            $arParams['BLOCK_ID'] = self::BLOCK_ID;
        }

        $taskID = $this->request->get('ID');
        if (!empty($taskID)) {
            $arParams['TASK_ID'] = $taskID;
        }

        return $arParams;
    }
}