<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('COMPONENT_NAME'),
	"DESCRIPTION" => GetMessage('COMPONENT_DESCRIPTION'),
	"ICON" => "",
	"CACHE_PATH" => "Y",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "taskmgr.list",
			"NAME" => GetMessage('COMPONENT_CATEGORY_TITLE'),
			"CHILD" => array(
				"ID" => "taskmgr_list",
			),
		),
	),
);

?>