<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult['ERROR']))
{
	ShowError($arResult['ERROR']);
	return false;
}

global $USER_FIELD_MANAGER;

$listUrl = $SITE_SERVER_NAME. '/taskmgr';

$this->addExternalJs($this->GetFolder() . '/../js/moment.min.js');
$this->addExternalJs($this->GetFolder() . '/../js/bootstrap-datetimepicker.min.js');
$this->addExternalJs($this->GetFolder() . '/../js/task_edit.js');
$this->addExternalCss($this->GetFolder() . '/../css/bootstrap-datetimepicker.css');

CJSCore::Init(array('ajax', 'window','jquery','moment'));
?>

<a href="<?=htmlspecialcharsbx($listUrl)?>"><?=GetMessage('ROW_VIEW_BACK_TO_LIST')?></a><br><br>

<? $oldValues = $this->getComponent()->oldValues;
    $errors = $this->getComponent()->arErrors;
?>

<div class="reports-result-list-wrap">
	<form method="post" action="<?=$APPLICATION->GetCurPage();?>">
        <div class="col-12"><input type="hidden" name="ID" id="ID" value="<?=$oldValues['ID'] ?? ''?>">
        <div class="col-4 form-group">
            <label for="UF_TASK_DATETIME"><?=$arResult['fields']['UF_TASK_DATETIME']['LIST_COLUMN_LABEL']?></label>
            <input type="text" class="form-control" name="UF_TASK_DATETIME" id="UF_TASK_DATETIME" value="<?= $oldValues['UF_TASK_DATETIME'] ?? '' ?>">
            <?if (!empty($errors['UF_TASK_DATETIME'])) :?>
                <span class="text-danger text-bold"><?= implode(';', array_values($errors['UF_TASK_DATETIME']))?></span>
            <?endif?>
        </div>

        <div class="col-6 form-group">
            <label for="UF_NAME"><?=$arResult['fields']['UF_NAME']['LIST_COLUMN_LABEL']?></label>
            <input type="text" class="form-control" name="UF_NAME" id="UF_NAME" value="<?= $oldValues['UF_NAME'] ?? '' ?>">
            <?if (!empty($errors['UF_NAME'])) :?>
                <span class="text-danger text-bold"><?= implode(';', array_values($errors['UF_NAME']))?></span>
            <?endif?>
        </div>
        <div class="col-6 form-group">
            <label for="UF_COMMENT"><?=$arResult['fields']['UF_COMMENT']['LIST_COLUMN_LABEL']?></label>
            <input type="text" class="form-control" name="UF_COMMENT" id="UF_COMMENT" value="<?= $oldValues['UF_COMMENT'] ?? '' ?>">
            <?if (!empty($errors['UF_COMMENT'])) :?>
                <span class="text-danger text-bold"><?= implode(';', array_values($errors['UF_COMMENT']))?></span>
            <?endif?>
        </div>
        <div class="col-6 form-group">
            <label for="UF_STATUS"><?=$arResult['fields']['UF_STATUS']['LIST_COLUMN_LABEL']?></label>
            <select class="form-control" name="UF_STATUS" id="UF_STATUS">
                <? $available_statuses = \lib\StructHelper::getEnumItems($fields['UF_STATUS']['ID']); ?>
                <option value="">Не задано</option>
                <? foreach($available_statuses as $status): ?>
                <option value="<?=$status['ID']?>" <? if (isset($oldValues['UF_STATUS']) && $oldValues['UF_STATUS'] == $status['ID']) echo 'selected' ?>><?=$status['VALUE']?></option>
                <?endforeach;?>
            </select>
            <?if (!empty($errors['UF_STATUS'])) :?>
                <span class="text-danger text-bold"><?= implode(';', array_values($errors['UF_STATUS']))?></span>
            <?endif?>
        </div>
            <div class="col-6 text-right">
                <button type="submit" class="btn btn-primary flex-right"><? if ($oldValues['ID']):?> Обновить <?else:?> Создать <?endif;?></button>
            </div>
        </div>
    </form>
</div>
<script>


</script>