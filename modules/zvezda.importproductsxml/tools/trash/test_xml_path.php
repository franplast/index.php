<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zvezda.importproductsxml/tools/script.php");


$xmlPath = "http://tk-konstruktor.ru/export/xml.php?iblock_id=16";
$import = new importProductsXml(0, $xmlPath);
$import->operationCategories();
$import->operationOffers();
?>