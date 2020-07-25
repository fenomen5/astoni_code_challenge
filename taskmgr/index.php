<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список задач");
?>
<?$APPLICATION->IncludeComponent(
    "astoni:tasks.list",
    ".default",
    array(
        "COMPONENT_TEMPLATE" => ".default",
        "BLOCK_ID" => "1",
        "DETAIL_URL" => "",
        "ROWS_PER_PAGE" => "20",
        "PAGEN_ID" => "page",
        "FILTER_NAME" => "",
        "SORT_FIELD" => "UF_DATETIME",
        "SORT_ORDER" => "DESC",
        "CHECK_PERMISSIONS" => "N"
    ),
    false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>