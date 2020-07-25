<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\UI\Extension;

Extension::load('ui.bootstrap4');
$APPLICATION->SetAdditionalCSS("/bitrix/css/main/font-awesome.css");

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?$APPLICATION->ShowHead()?>
<title><?$APPLICATION->ShowTitle()?></title>
</head>

<body>


<?$APPLICATION->ShowPanel();?>

<div id="container">

<table id="content" class="w-100" cellpadding="0" cellspacing="0">
	<tr>
		<td class="main-column w-100">

		<h1 id="pagetitle"><?$APPLICATION->ShowTitle(false)?></h1>