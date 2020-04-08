<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;

// TODO проверяем доступность файла и возвращаем статус

$arResult = [
    "STATUS" => 1,
];
echo json_encode($arResult);