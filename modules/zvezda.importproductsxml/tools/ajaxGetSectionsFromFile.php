<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//use Bitrix\Main\Loader;
//Loader::includeModule("iblock");

//$IBLOCK_TYPE = 'catalog'; // TODO gep saved option
//
//$iblock = new CIBlock;
//$arShops = [];
//
//$rsIblock = $iblock->getList([], ["TYPE" => $IBLOCK_TYPE, "ACTIVE" => "Y"]);
//
//while ($obIblock = $rsIblock->Fetch()) {
//    $arIblock[] = [
//        "NAME" => $obIblock['NAME'],
//        "ID" => $obIblock['ID']
//    ];
//}

$arResult = [
    [ 'ID' => 1,   "NAME" => 'test_1' ],
    [ 'ID' => 2,   "NAME" => 'test_2' ],
    [ 'ID' => 3,   "NAME" => 'test_3' ]
];

echo json_encode($arResult);