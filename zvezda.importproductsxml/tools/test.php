<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zvezda.importproductsxml/tools/script.php");


$shopId = 45996;
$import = new importProductsXml($shopId);
$import->operationCategories();
$import->operationOffers();

?>