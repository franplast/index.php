<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule("iblock");

$IBLOCK_ID = $_POST['iblock']; // TODO get saved option
$NEED = $_POST['need']; // TODO get saved option


$status = "1"; // Пока доступен файл или нет, позже сюда же можно добавить обновлён удалённый или нет. 0- недоступен, 1 - доступен
$message =""; // Заполняем, если есть что сказать, например, что файл не обновлялся с последней проверки, или что он не доступен
$arResult['STATUS'] = $status;
$arResult['MASSAGE'] = $message;


$arResult['ITEMS'] = [
    ['ID' => 2, 'NAME' => 'name', 'CODE' => 'code' ],
    ['ID' => 1, 'NAME' => 'name2', 'CODE' => 'code2' ],
];

echo json_encode($arResult);