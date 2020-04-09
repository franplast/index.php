<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule("iblock");

/**********
 * Типы свойств инфоблоков:
 *  S — строка
 *  N — число
 *  L — список
 *  F — файл
 *  G — привязка к разделу
 *  E — привязка к элементу
 *  S:UserID — Привязка к пользователю
 *  S:DateTime — Дата/Время
 *  E:EList — Привязка к элементам в виде списка
 *  S:FileMan — Привязка к файлу (на сервере)
 *  S:map_yandex — Привязка к Яndex.Карте
 *  S:HTML — HTML/текст
 *  S:map_google — Привязка к карте Google Maps
 *  S:ElementXmlID — Привязка к элементам по XML_ID
 */


$arResult = [];


/* ...... КОД .......  */


$status = "1"; // Пока доступен файл или нет, позже сюда же можно добавить обновлён удалённый или нет. 0- недоступен, 1 - доступен
$message =""; // Заполняем, если есть что сказать, например, что файл не обновлялся с последней проверки, или что он не доступен
$arResult['STATUS'] = $status;
$arResult['MESSAGE'] = $message;


$arResult['ITEMS_FIELDS'] = [
    [ "ID" => 1, "NAME" => "Имя поля 1", "CODE" => "NAME_FIELD_1" ],
    [ "ID" => 2, "NAME" => "Имя поля 2", "CODE" => "NAME_FIELD_2" ],
    [ "ID" => 3, "NAME" => "Имя поля 3", "CODE" => "NAME_FIELD_3" ],
    [ "ID" => 4, "NAME" => "Имя поля 4", "CODE" => "NAME_FIELD_4" ],
    [ "ID" => 5, "NAME" => "Имя поля 5", "CODE" => "NAME_FIELD_5" ],
];
$a5Result['ITEMS_PROPERTIES'] = [
    [ "ID" => 1, "NAME" => "Имя поля 1", "CODE" => "NAME_FIELD_1" ],
    [ "ID" => 2, "NAME" => "Имя поля 2", "CODE" => "NAME_FIELD_2" ],
    [ "ID" => 3, "NAME" => "Имя поля 3", "CODE" => "NAME_FIELD_3" ],
    [ "ID" => 4, "NAME" => "Имя поля 4", "CODE" => "NAME_FIELD_4" ],
    [ "ID" => 5, "NAME" => "Имя поля 5", "CODE" => "NAME_FIELD_5" ],
];

echo json_encode($arResult);