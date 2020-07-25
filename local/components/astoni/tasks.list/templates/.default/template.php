<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult['ERROR']))
{
	echo $arResult['ERROR'];
	return false;
}
$this->addExternalJs($this->GetFolder() . '/../js/task_list.js');
$this->addExternalJs($this->GetFolder() . '/../js/utils.js');
$this->addExternalCss($this->GetFolder() . '/../css/main.css');

CJSCore::Init(array('ajax', 'window','jquery'));
?>

<div class="reports-result-list-wrap">
<div class="report-table-wrap px-5">
<div class="reports-list-left-corner"></div>
<div class="reports-list-right-corner col-12 mt-3"></div>
    <a href="/taskmgr/add_task/" class="btn btn-primary mb-3 ">Добавить задачу</a>
<table class="reports-list-table task-list w-100" id="report-result-table">
	<!-- head -->
	<tr>
		<? $i = 0; foreach(array_keys($arResult['tableColumns']) as $col): ?>
		<?
		$i++;

		if ($i == 1)
		{
			$th_class = 'reports-first-column';
		}
		else if ($i == count($arResult['viewColumns']))
		{
			$th_class = 'reports-last-column';
		}
		else
		{
			$th_class = 'reports-head-cell';
		}

		// title
		$arUserField = $arResult['fields'][$col];
		$title = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $col;

		// sorting
		$defaultSort = 'DESC';
		//$defaultSort = $col['defaultSort'];

		if ($col === $arResult['sort_id'])
		{
			$th_class .= ' reports-selected-column';

			if($arResult['sort_type'] == 'ASC')
			{
				$th_class .= ' reports-head-cell-top';
			}
		}
		else
		{
			if ($defaultSort == 'ASC')
			{
				$th_class .= ' reports-head-cell-top';
			}
		}

		?>
		<th class="<?=$th_class?>" colId="<?=htmlspecialcharsbx($col)?>" defaultSort="<?=$defaultSort?>">
			<div class="reports-head-cell"><?if($defaultSort):
				?><span class="reports-table-arrow"></span><?
			endif?><span class="reports-head-cell-title"><?=htmlspecialcharsex($title)?></span></div>
		</th>
		<? endforeach; ?>
	</tr>

	<!-- data -->
	<? foreach ($arResult['rows'] as $row): ?>
	<tr class="reports-list-item task" data-id="<?=$row['ID']?>">
		<? $i = 0; foreach(array_keys($arResult['tableColumns']) as $col): ?>
		<?
		$i++;
		if ($i == 1)
		{
			$td_class = 'reports-first-column';
		}
		else if ($i == count($arResult['viewColumns']))
		{
			$td_class = 'reports-last-column';
		}
		else
		{
			$td_class = '';
		}

        $td_class .= empty($td_class) ? $col : ' ' .$col;

		//if (CReport::isColumnPercentable($col))
		if (false) // numeric rows
		{
			$td_class .= ' reports-numeric-column';
		}

		$finalValue = $row[$col];

		if ($col === 'ID' && !empty($arParams['DETAIL_URL']))
		{
			$url = str_replace(
				array('#ID#', '#BLOCK_ID#'),
				array($finalValue, intval($arParams['BLOCK_ID'])),
				$arParams['DETAIL_URL']
			);

			$finalValue = '<a href="'.htmlspecialcharsbx($url).'">'.$finalValue.'</a>';
		}

        if ($col === 'UF_STATUS') {
            $finalValue = $row[$col]['value'];
        }
		?>
		<td class="<?=$td_class?>"><?=$finalValue?></td>
		<? endforeach; ?>
        <td class="control">
            <button class="btn btn-success py-1 update-status" data-status="<?=$row['UF_STATUS']['id']?>" title="Сменить статус">
                <? if ($row['UF_STATUS']['id'] == CTaskList::WORK_IN_PROGRESS): ?>
                    <i class="fa fa-check"></i>
                <? else: ?>
                    <i class="fa fa-undo"></i>
                <? endif; ?>
            </button>
        </td>
        <td class="control"><a href="/taskmgr/edit_task?ID=<?= $row['ID'];?>" class="btn btn-info py-1" title="Изменить"><i class="fa fa-pencil"></i></a></td>
        <td class="control"><button class="btn btn-danger py-1 remove" title="Удалить"><i class="fa fa-trash-o"></i></button></td>
	</tr>
	<? endforeach; ?>

</table>

<?php
if ($arParams['ROWS_PER_PAGE'] > 0):
	$APPLICATION->IncludeComponent(
		'bitrix:main.pagenavigation',
		'',
		array(
			'NAV_OBJECT' => $arResult['nav_object'],
			'SEF_MODE' => 'N',
		),
		false
	);
endif;
?>


<form id="hlblock-table-form" action="" method="get">
	<input type="hidden" name="BLOCK_ID" value="<?=htmlspecialcharsbx($arParams['BLOCK_ID'])?>">
	<input type="hidden" name="sort_id" value="">
	<input type="hidden" name="sort_type" value="">
</form>

<script type="text/javascript">
	BX.ready(function(){
		var rows = BX.findChildren(BX('report-result-table'), {tag:'th'}, true);
		for (i in rows)
		{
			var ds = rows[i].getAttribute('defaultSort');
			if (ds == '')
			{
				BX.addClass(rows[i], 'report-column-disabled-sort')
				continue;
			}

			BX.bind(rows[i], 'click', function(){
				var colId = this.getAttribute('colId');
				var sortType = '';

				var isCurrent = BX.hasClass(this, 'reports-selected-column');

				if (isCurrent)
				{
					var currentSortType = BX.hasClass(this, 'reports-head-cell-top') ? 'ASC' : 'DESC';
					sortType = currentSortType == 'ASC' ? 'DESC' : 'ASC';
				}
				else
				{
					sortType = this.getAttribute('defaultSort');
				}

				var idInp = BX.findChild(BX('hlblock-table-form'), {attr:{name:'sort_id'}});
				var typeInp = BX.findChild(BX('hlblock-table-form'), {attr:{name:'sort_type'}});

				idInp.value = colId;
				typeInp.value = sortType;

				BX.submit(BX('hlblock-table-form'));
			});
		}
	});
</script>

</div>
</div>