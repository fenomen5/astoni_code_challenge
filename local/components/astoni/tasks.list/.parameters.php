<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	'GROUPS' => array(
	),
	'PARAMETERS' => array(
		'BLOCK_ID' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('COMPONENT_BLOCK_ID_PARAM'),
			'TYPE' => 'TEXT'
		),
		'DETAIL_URL' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('COMPONENT_DETAIL_URL_PARAM'),
			'TYPE' => 'TEXT'
		),
		'SORT_FIELD' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('COMPONENT_SORT_FIELD_PARAM'),
			'TYPE' => 'TEXT',
			'DEFAULT' => 'ID'
		),
		'SORT_ORDER' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('COMPONENT_SORT_ORDER_PARAM'),
			'TYPE' => 'LIST',
			'DEFAULT' => 'DESC',
			'VALUES' => array(
				'DESC' => GetMessage('COMPONENT_SORT_ORDER_PARAM_DESC'),
				'ASC' => GetMessage('COMPONENT_SORT_ORDER_PARAM_ASC')
			)
		),
		'CHECK_PERMISSIONS' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('COMPONENT_CHECK_PERMISSIONS_PARAM'),
			'TYPE' => 'CHECKBOX'
		),
	),
);