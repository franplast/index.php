<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule("iblock");

$IBLOCK_TYPE = 'catalog'; // TODO get saved option
$IBLOCK_IDS = $_POST['iblock_ids']; // TODO get saved option

$arResult = [];
$obj = new CIBlock;
$rs = $obj->getList( [], ["TYPE" => $IBLOCK_TYPE, "ACTIVE" => "Y", "ID" => array_unique($IBLOCK_IDS)] );

while($rsItem = $rs->Fetch()) {
    $arResult['ITEMS'][] = [
        "NAME" => $rsItem['NAME'],
        "ID" => $rsItem['ID']
    ];
}


echo json_encode($arResult);