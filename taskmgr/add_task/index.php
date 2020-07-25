<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Создать задачу");

?><?$APPLICATION->IncludeComponent(
	"astoni:task.edit",
	"",
	Array(
		"BLOCK_ID" => '1',
		"CHECK_PERMISSIONS" => "N",
		"LIST_URL" => "",
		"ROW_ID" => null,
		"ROW_KEY" => "ID"
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>